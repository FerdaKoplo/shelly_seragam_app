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

        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm" x-cloak style="display: none;">
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
                    class="bg-white rounded-xl shadow-2xl w-[950px] max-h-[90vh] overflow-hidden flex flex-col transform transition-all" x-transition>

                    <div class="p-8 flex-1 overflow-y-auto">
                        <div class="flex gap-8">

                            <div class="w-1/2 border border-gray-200 rounded-lg p-6 flex flex-col justify-between shadow-sm bg-white h-fit">
                                <div>
                                    <h2 class="text-xl font-bold mb-6 border-b border-gray-100 pb-4 text-gray-800">Detail Transaksi</h2>
                                    
                                    <div class="mb-8 text-sm text-gray-600 space-y-3">
                                        <div class="grid grid-cols-3">
                                            <strong class="text-gray-800 col-span-1">Customer:</strong> 
                                            <span class="col-span-2">{{ $trx->nama_customer }}</span>
                                        </div>
                                        <div class="grid grid-cols-3">
                                            <strong class="text-gray-800 col-span-1">Alamat Pengiriman:</strong> 
                                            <span class="text-gray-500 col-span-2 leading-relaxed">{{ $trx->alamat_customer }}</span>
                                        </div>
                                    </div>

                                @if($isKustom)
                                        @php
                                            $kustomOrder = $trx->orderKustoms->first(); 
                                            $paymentAttachment = $kustomOrder->attachments->first(function ($attachment) {
                                                return str_contains($attachment->path, 'payments/kustom');
                                            });
                                        @endphp
                                        
                                        <div class="mt-4 p-4 border border-dashed border-gray-300 rounded-lg bg-gray-50">
                                            <h4 class="text-sm font-bold text-gray-800 mb-2">Bukti Pembayaran (Kustom)</h4>
                                            
                                            @if($paymentAttachment)
                                                <a href="{{ asset('storage/' . $paymentAttachment->path) }}" target="_blank" class="text-xs text-blue-600 hover:underline mb-3 block flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    Lihat Bukti Saat Ini
                                                </a>

                                                <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded text-xs font-medium flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Pembayaran telah dilunasi dan bukti sudah diunggah.
                                                </div>
                                            @else
                                                <form action="{{ route('manage.transaksi.upload-payment') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                                    @csrf
                                                    <input type="hidden" name="order_kustom_id" value="{{ $kustomOrder->order_kustom_id }}">
                                                    <input type="file" name="file_payment" accept=".jpg,.jpeg,.png,.pdf" required
                                                        class="text-xs w-full file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-white file:border-gray-300 file:border file:text-gray-700 hover:file:bg-gray-100 cursor-pointer">
                                                    <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded text-xs hover:bg-black transition self-start font-medium shadow-sm">
                                                        Unggah Bukti & Lunasi
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif

                                    <form action="{{ route('manage.transaksi.update', $trx->transaksi_id) }}" method="POST" id="form-{{$trx->transaksi_id}}">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4 text-sm mt-4">
                                            <div class="grid grid-cols-3 items-center">
                                                <label class="text-gray-700 font-medium col-span-1">Status Bayar:</label>
                                                <div class="col-span-2">
                                                    @if($statusBayar == 'Lunas')
                                                        <span class="text-gray-400 text-[11px]">{{ $trx->status  }}</span>
                                                    @else
                                                        <span class="text-red-400 text-[11px]">Belum Lunas</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-3 items-center">
                                                <label class="text-gray-700 font-medium col-span-1">Status Kirim:</label>
                                                <div class="col-span-2">
                                                    <input type="text" name="no_resi_customer" value="{{ $trx->no_resi_customer }}"
                                                        placeholder="Masukkan No. Resi"
                                                        class="border border-gray-300 rounded px-3 py-2 w-full bg-gray-50 focus:bg-white focus:outline-none text-xs">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-4 italic">* Riwayat perubahan akan tercatat otomatis.</p>
                                    </form>
                                </div>

                                <div class="flex justify-center gap-3 mt-6 border-t border-gray-100 pt-6">
                                    <button type="button" @click="modalOpen = false; selectedTrx = null"
                                        class="w-full py-2.5 border border-gray-300 rounded text-sm hover:bg-gray-50 transition font-medium">Tutup</button>
                                    <button type="submit" form="form-{{$trx->transaksi_id}}"
                                        class="w-full py-2.5 bg-[#333333] text-white rounded text-sm hover:bg-gray-800 transition font-medium">Simpan Perubahan</button>
                                </div>

                            </div>

                            <div class="w-1/2">
                                <h2 class="text-xl font-bold mb-4 text-gray-800">Item {{ $isKustom ? 'Kustom' : 'Katalog' }}</h2>
                                
                                @if($isKustom)
                                    @php $kustom = $trx->orderKustoms->first(); @endphp
                                    <div class="border border-gray-200 p-4 rounded-lg bg-white shadow-sm">
                                        <div class="space-y-3 text-sm text-gray-700">
                                            <p><strong class="block text-gray-800 mb-1">Tipe:</strong> {{ $kustom->tipe_kustom }}</p>
                                            <p><strong class="block text-gray-800 mb-1">Kain:</strong> {{ $kustom->catatan ?? 'N/A' }}</p>
                                            <p><strong class="block text-gray-800 mb-1">Detail Kustomisasi:</strong> <x-shared.chip :details="$kustom->detail_pilihan_kustomisasi" /></p>
                                            <p><strong class="block text-gray-800 mb-1">Jumlah:</strong> {{ $kustom->quantity }} pcs</p>
                                        </div>
                                        <div class="mt-6 border-t border-gray-200 pt-4 text-right">
                                            <span class="font-bold text-2xl text-black">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($trx->produkTransaksis as $pt)
                                            <div class="flex gap-4 p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                                
                                                <div class="w-24 h-32 bg-gray-50 border border-gray-200 rounded object-cover flex-shrink-0 flex items-center justify-center">
                                                    <span class="text-xs text-gray-400">Image</span>
                                                </div>
                                                
                                                <div class="flex-1 flex flex-col justify-between">
                                                    <div>
                                                        <h4 class="font-bold text-sm text-gray-900">{{ $pt->produk->nama ?? 'Nama Produk' }}</h4>
                                                        <p class="text-[10px] text-gray-400 italic mt-0.5">#Kemeja #Katun #Formal</p>
                                                        <p class="font-bold text-xs mt-1 text-gray-800">Rp.{{ number_format($pt->produk->katalog->harga ?? 0, 0, ',', '.') }}</p>
                                                    </div>

                                                    <div class="mt-3">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-xs font-bold text-gray-900">Ukuran</span>
                                                            <span class="text-[9px] text-gray-500 tracking-wide">📏 Panduan Ukuran</span>
                                                        </div>
                                                        <div class="grid grid-cols-3 gap-2">
                                                            @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                                                <div class="border {{ $pt->size == $size ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-300 text-gray-600' }} rounded flex items-center justify-center py-1 text-[11px] font-medium">
                                                                    {{ $size }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <span class="text-xs font-bold text-gray-900 block mb-1">Quantity</span>
                                                        <div class="flex justify-between items-end">
                                                            <div class="flex items-center border border-gray-300 rounded bg-white text-sm w-24">
                                                                <span class="px-3 py-1 border-r border-gray-200 text-gray-500">-</span>
                                                                <span class="flex-1 text-center font-bold text-gray-800">{{ $pt->quantity }}</span>
                                                                <span class="px-3 py-1 border-l border-gray-200 text-gray-500">+</span>
                                                            </div>
                                                            <div class="font-bold text-2xl text-black leading-none">
                                                                Rp{{ number_format($pt->subtotal, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
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