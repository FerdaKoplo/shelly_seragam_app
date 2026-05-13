@extends('layouts.user.layout')
@section('title', 'Manage Transaksi')
@section('content')
    <div x-data="{
        modalOpen: false,
        selectedTrx: null,
        
        ongkirResults: null,
        loadingOngkir: false,
        selectedCourier: 'jne',
        destinationId: '',
    }" class="p-8 w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 min-h-full border border-gray-200">

            <div class="flex justify-between items-center mb-6">
                <form method="GET" action="{{ route('manage.transaksi') }}" class="flex w-1/3">
                    <div class="relative w-full border border-gray-300 rounded-md overflow-hidden flex items-center bg-white">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Transaksi"
                            class="w-full py-2 px-4 focus:outline-none text-sm">
                        <button type="submit" class="px-3 text-gray-500 hover:text-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="flex space-x-2">
                    <a href="{{ route('manage.transaksi', ['filter' => 'katalog']) }}"
                        class="px-4 py-2 border border-gray-300 text-sm rounded-md transition {{ request('filter') == 'katalog' ? 'bg-black text-white font-medium shadow-sm' : 'bg-white hover:bg-gray-50' }}">
                        Produk Katalog
                    </a>
                    <a href="{{ route('manage.transaksi', ['filter' => 'kustom']) }}"
                        class="px-4 py-2 border border-gray-300 text-sm rounded-md transition {{ request('filter') == 'kustom' ? 'bg-black text-white font-medium shadow-sm' : ' bg-white text-gray-800  over:bg-gray-50' }}">
                        Produk Kustom
                    </a>
                    @if(request('filter') || request('search'))
                        <a href="{{ route('manage.transaksi') }}" class="px-4 py-2 text-sm text-red-500 hover:underline flex items-center">Reset</a>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-600">
                            <th class="py-3 px-2 font-medium">ID</th>
                            <th class="py-3 px-2 font-medium">Customer</th>
                            <th class="py-3 px-2 font-medium">Tipe</th>
                            <th class="py-3 px-2 font-medium">Status Bayar</th>
                            <th class="py-3 px-2 font-medium">Status Kirim</th>
                            <th class="py-3 px-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $trx)
                            @php
                                $isKustom = $trx->orderKustoms->count() > 0;
                                $tipe = $isKustom ? 'Kustom' : 'Katalog';
                                $statusBayar = in_array($trx->status, ['Paid', 'Delivered', 'Done']) ? 'Lunas' : 'Belum Lunas';
                                $statusKirim = $trx->pengiriman ? ucfirst($trx->pengiriman->status_pengiriman) : ($trx->no_resi_customer ? 'Dikirim' : 'Belum Kirim');
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-2 text-gray-500">#TRX{{ str_pad($trx->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-3 px-2">{{ $trx->nama_customer }}</td>
                                <td class="py-3 px-2">{{ $tipe }}</td>
                                <td class="py-3 px-2">{{ $statusBayar }}</td>
                                <td class="py-3 px-2">{{ $statusKirim }}</td>
                                <td class="py-3 px-2">
                                    <button @click="modalOpen = true; selectedTrx = {{ $trx->transaksi_id }}; ongkirResults = null; destinationId = ''; destinationName = ''; destinations = [];" class="bg-[#333333] text-white px-5 py-1.5 rounded text-xs hover:bg-gray-800 transition shadow-sm">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data transaksi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-10 flex items-center justify-center">
                {{ $transaksis->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" x-cloak style="display: none;">
            @foreach($transaksis as $trx)
                @php
                    $isKustom = $trx->orderKustoms->count() > 0;
                    $statusBayar = in_array($trx->status, ['Paid', 'Delivered', 'Done']) ? 'Lunas' : 'Belum Lunas';
                    
                    $totalBerat = 0;
                    if($isKustom) {
                        $totalBerat = ($trx->orderKustoms->first()->quantity ?? 1) * 500;
                    } else {
                        foreach($trx->produkTransaksis as $pt) {
                            $totalBerat += ($pt->quantity * 500);
                        }
                    }
                    if ($totalBerat < 1000) $totalBerat = 1000;
                @endphp

                <div x-show="selectedTrx === {{ $trx->transaksi_id }}" @click.away="modalOpen = false; selectedTrx = null"
                    class="bg-white  shadow-2xl w-[1000px] max-h-[90vh] overflow-hidden flex flex-col transform transition-all"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                Detail Transaksi 
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-sm font-medium">
                                    #TRX{{ str_pad($trx->transaksi_id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </h2>
                        </div>
                        <button @click="modalOpen = false; selectedTrx = null" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-full transition-colors outline-none focus:ring-2 focus:ring-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-8 flex-1 overflow-y-auto bg-gray-50/50 custom-scrollbar">
                        <div class="flex gap-8">

                            <div class="w-1/2 space-y-6">
                                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Informasi Customer
                                    </h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="grid grid-cols-3">
                                            <span class="text-gray-500 col-span-1">Nama</span> 
                                            <span class="font-medium text-gray-900 col-span-2">{{ $trx->nama_customer }}</span>
                                        </div>
                                        <div class="grid grid-cols-3">
                                            <span class="text-gray-500 col-span-1">Alamat</span> 
                                            <span class="font-medium text-gray-900 col-span-2 leading-relaxed">{{ $trx->alamat_customer }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($isKustom)
                                    @php
                                        $kustomOrder = $trx->orderKustoms->first(); 
                                        $paymentAttachment = $kustomOrder->attachments->first(function ($attachment) {
                                            return str_contains($attachment->path, 'payments/kustom');
                                        });
                                    @endphp
                                    
                                    <div class="bg-white border {{ $paymentAttachment ? 'border-green-200' : 'border-dashed border-gray-300' }} rounded-xl p-5 shadow-sm">
                                        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            Bukti Pembayaran (Kustom)
                                        </h3>
                                        
                                        @if($paymentAttachment)
                                            <div class="bg-green-50/50 border border-green-200 rounded-lg p-4 flex flex-col gap-4">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="bg-green-100 rounded-full p-1 mt-0.5 shrink-0">
                                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <div>
                                                        <h5 class="text-xs font-bold text-green-800">Pembayaran Selesai</h5>
                                                        <p class="text-[10px] text-green-600 mt-0.5 leading-relaxed">Bukti pembayaran kustom telah berhasil diunggah.</p>
                                                    </div>
                                                </div>

                                                <div class="bg-white border border-green-100 rounded-md p-2.5 flex items-center justify-between shadow-sm hover:shadow transition-shadow group">
                                                    <div class="flex items-center gap-3 overflow-hidden pr-3">
                                                        <div class="bg-gray-50 border border-gray-100 p-2 rounded text-gray-500 shrink-0">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        </div>
                                                        <div class="flex flex-col min-w-0">
                                                            <span class="text-[11px] font-semibold text-gray-800 truncate" title="{{ basename($paymentAttachment->path) }}">
                                                                {{ basename($paymentAttachment->path) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $paymentAttachment->path) }}" target="_blank" 
                                                        class="shrink-0 text-[10px] font-semibold text-gray-700 bg-white border border-gray-300 px-3 py-1.5 rounded hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                                        Buka File
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <form action="{{ route('manage.transaksi.upload-payment') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3" x-data="{ fileName: '' }">
                                                @csrf
                                                <input type="hidden" name="order_kustom_id" value="{{ $kustomOrder->order_kustom_id }}">

                                                <label for="file_payment_{{ $trx->transaksi_id }}" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition duration-200">
                                                    <div class="flex flex-col items-center justify-center px-4 text-center">
                                                        <svg class="w-6 h-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                        <div x-show="!fileName">
                                                            <p class="text-xs font-semibold text-gray-700">Klik untuk mengunggah file</p>
                                                            <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, atau PDF (Max. 5MB)</p>
                                                        </div>
                                                        <div x-show="fileName" x-cloak class="flex items-center gap-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            <p class="text-xs font-bold text-green-600 truncate max-w-[200px]" x-text="fileName"></p>
                                                        </div>
                                                    </div>
                                                    <input id="file_payment_{{ $trx->transaksi_id }}" type="file" name="file_payment" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required @change="fileName = $event.target.files[0].name">
                                                </label>

                                                <button type="submit" class="w-full bg-gray-900 text-white px-3 py-2.5 rounded-lg text-xs transition font-medium shadow-sm flex justify-center items-center gap-2" :class="fileName ? 'hover:bg-black' : 'opacity-70 hover:bg-gray-800'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Unggah Bukti & Lunasi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Update Status
                                    </h3>
                                    <form action="{{ route('manage.transaksi.update', $trx->transaksi_id) }}" method="POST" id="form-{{$trx->transaksi_id}}">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4 text-sm">
                                            <div class="grid grid-cols-3 items-center">
                                                <label class="text-gray-600 col-span-1">Status Bayar</label>
                                                <div class="col-span-2">
                                                    @if($statusBayar == 'Lunas')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-green-100 text-green-800 border border-green-200">
                                                            {{ $trx->status }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-100 text-red-800 border border-red-200">
                                                            Belum Lunas
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-3 items-center">
                                                <label class="text-gray-600 col-span-1">No. Resi</label>
                                                <div class="col-span-2">
                                                    <input type="text" name="no_resi_customer" value="{{ $trx->no_resi_customer }}" placeholder="Masukkan No. Resi"
                                                        class="border border-gray-300 rounded-lg px-3 py-2 w-full bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-400 text-sm transition-colors">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-4 italic">* Riwayat perubahan akan tercatat otomatis.</p>
                                    </form>
                                </div>
                            </div>

                            <div class="w-1/2">
                                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm h-full flex flex-col">
                                    <h3 class="text-lg font-bold mb-4 text-gray-900 border-b border-gray-100 pb-3">Item {{ $isKustom ? 'Kustom' : 'Katalog' }}</h3>
                                    
                                    @if($isKustom)
                                        @php $kustom = $trx->orderKustoms->first(); @endphp
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div class="space-y-5 text-sm text-gray-700">
                                                <div class="grid grid-cols-3">
                                                    <strong class="col-span-1 text-gray-500">Tipe</strong> 
                                                    <span class="col-span-2 font-medium text-gray-900">{{ $kustom->tipe_kustom }}</span>
                                                </div>
                                                <div class="grid grid-cols-3">
                                                    <strong class="col-span-1 text-gray-500">Kain / Catatan</strong> 
                                                    <span class="col-span-2 font-medium text-gray-900">{{ $kustom->catatan ?? '-' }}</span>
                                                </div>
                                                <div class="grid grid-cols-3">
                                                    <strong class="col-span-1 text-gray-500">Jumlah</strong> 
                                                    <span class="col-span-2 font-medium text-gray-900">{{ $kustom->quantity }} pcs</span>
                                                </div>
                                                <div class="grid grid-cols-3 ">
                                                    <strong class="block text-gray-500 mb-2">Detail Kustomisasi</strong>
                                                    <x-shared.chip :details="$kustom->detail_pilihan_kustomisasi" />
                                                </div>
                                            </div>
                                            <div class="mt-6 border-t border-gray-200 pt-5 text-right">
                                                <p class="text-xs text-gray-500 mb-1 font-bold uppercase tracking-wider">Total Harga</p>
                                                <span class="font-black text-3xl text-gray-900">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-4 flex-1">
                                            @foreach($trx->produkTransaksis as $pt)
                                                <div class="flex gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors">
                                                    <div class="w-20 h-28 bg-white border border-gray-200 rounded-lg object-cover flex-shrink-0 flex items-center justify-center shadow-sm">
                                                        <span class="text-[10px] text-gray-400 font-medium">Image</span>
                                                    </div>
                                                    <div class="flex-1 flex flex-col justify-between">
                                                        <div>
                                                            <h4 class="font-bold text-sm text-gray-900 line-clamp-1">{{ $pt->produk->nama ?? 'Nama Produk' }}</h4>
                                                            <p class="font-semibold text-xs mt-1 text-gray-700">Rp {{ number_format($pt->produk->katalog->harga ?? 0, 0, ',', '.') }}</p>
                                                        </div>
                                                        <div class="mt-2 flex items-center gap-2">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-300 bg-white text-xs font-bold text-gray-700">
                                                                {{ $pt->size }}
                                                            </span>
                                                            <span class="text-xs text-gray-500 font-medium">x {{ $pt->quantity }} pcs</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col justify-end text-right">
                                                        <p class="text-[10px] text-gray-500 font-bold uppercase mb-0.5">Subtotal</p>
                                                        <p class="font-bold text-sm text-gray-900">Rp{{ number_format($pt->subtotal, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-6 border-t border-gray-200 pt-5 text-right shrink-0">
                                            <p class="text-xs text-gray-500 mb-1 font-bold uppercase tracking-wider">Total Harga</p>
                                            <span class="font-black text-3xl text-gray-900">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. STICKY FOOTER --}}
                    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50 shrink-0 flex justify-end gap-3 items-center">
                        <button type="button" @click="modalOpen = false; selectedTrx = null"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm hover:bg-white hover:text-gray-900 text-gray-600 transition font-medium shadow-sm">
                            Tutup
                        </button>
                        <button type="submit" form="form-{{$trx->transaksi_id}}"
                            class="px-6 py-2.5 bg-gray-900 text-white rounded-lg text-sm hover:bg-black transition font-medium shadow-sm flex items-center gap-2">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        [x-cloak] { display: none !important; }
    </style>

@endsection