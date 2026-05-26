{{-- resources/views/pages/user/transaksi/index.blade.php --}}
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
    }" class="p-4 md:p-8 w-full">
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 min-h-full border border-gray-200">

        <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4 mb-6">
            <form method="GET" action="{{ route('manage.transaksi') }}" class="flex w-full lg:w-1/3">
                <div class="relative w-full border border-gray-300 rounded-md overflow-hidden flex items-center bg-white">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari Transaksi"
                        data-cy="transaksi-search-input"
                        class="w-full py-2 px-4 focus:outline-none text-sm">
                    <button type="submit" data-cy="transaksi-search-submit" class="px-3 text-gray-500 hover:text-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <a href="{{ route('manage.transaksi', ['filter' => 'katalog']) }}"
                    data-cy="filter-katalog"
                    class="whitespace-nowrap px-4 py-2 border border-gray-300 text-xs md:text-sm rounded-md transition {{ request('filter') == 'katalog' ? 'bg-black text-white font-medium shadow-sm' : 'bg-white hover:bg-gray-50' }}">
                    Produk Katalog
                </a>
                <a href="{{ route('manage.transaksi', ['filter' => 'kustom']) }}"
                    data-cy="filter-kustom"
                    class="whitespace-nowrap px-4 py-2 border border-gray-300 text-xs md:text-sm rounded-md transition {{ request('filter') == 'kustom' ? 'bg-black text-white font-medium shadow-sm' : ' bg-white text-gray-800 hover:bg-gray-50' }}">
                    Produk Kustom
                </a>
                @if(request('filter') || request('search'))
                <a href="{{ route('manage.transaksi') }}" data-cy="filter-reset" class="px-4 py-2 text-sm text-red-500 hover:underline flex items-center shrink-0">Reset</a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table data-cy="transaksi-table" class="w-full text-left text-sm min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-600">
                        <th class="py-3 px-4 font-medium">ID</th>
                        <th class="py-3 px-2 font-medium">Customer</th>
                        <th class="py-3 px-2 font-medium">Tipe</th>
                        <th class="py-3 px-2 font-medium">Status Bayar</th>
                        <th class="py-3 px-2 font-medium">Status Kirim</th>
                        <th class="py-3 px-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                    @php
                    $isKustom = $trx->orderKustoms->count() > 0;
                    $tipe = $isKustom ? 'Kustom' : 'Katalog';
                    $statusBayar = in_array($trx->status, ['Paid', 'Delivered', 'Done']) ? 'Lunas' : 'Belum Lunas';
                    $statusKirim = $trx->pengiriman ? ucfirst($trx->pengiriman->status_pengiriman) : ($trx->no_resi_customer != '-' ? 'Dikirim' : 'Belum Kirim');
                    @endphp
                    <tr data-cy="transaksi-row-{{ $trx->transaksi_id }}" class="border-b border-gray-100 hover:bg-gray-50 transition group">
                        <td data-cy="trx-id-{{ $trx->transaksi_id }}" class="py-3 px-4 text-gray-500">#TRX{{ str_pad($trx->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td data-cy="trx-customer-{{ $trx->transaksi_id }}" class="py-3 px-2 font-semibold text-gray-900">{{ $trx->nama_customer }}</td>
                        <td data-cy="trx-tipe-{{ $trx->transaksi_id }}" class="py-3 px-2 text-gray-600">{{ $tipe }}</td>
                        <td class="py-3 px-2">
                            <span data-cy="trx-status-bayar-{{ $trx->transaksi_id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBayar == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $statusBayar }}
                            </span>
                        </td>
                        <td data-cy="trx-status-kirim-{{ $trx->transaksi_id }}" class="py-3 px-2 text-gray-600">{{ $statusKirim }}</td>
                        <td class="py-3 px-4 text-right">
                            <button
                                @click="modalOpen = true; selectedTrx = {{ $trx->transaksi_id }};"
                                data-cy="btn-detail-{{ $trx->transaksi_id }}"
                                class="bg-gray-900 text-white px-5 py-1.5 rounded-md text-xs hover:bg-black transition shadow-sm">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" data-cy="transaksi-empty" class="py-12 text-center text-gray-400 italic">Tidak ada data transaksi ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div data-cy="transaksi-pagination" class="mt-10 flex items-center justify-center">
            {{ $transaksis->onEachSide(1)->links('vendor.pagination.custom') }}
        </div>
    </div>

    <div x-show="modalOpen" data-cy="modal-overlay" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" x-cloak style="display: none;">
        @foreach($transaksis as $trx)
        @php
        $isKustom = $trx->orderKustoms->count() > 0;
        $statusBayar = in_array($trx->status, ['Paid', 'Delivered', 'Done']) ? 'Lunas' : 'Belum Lunas';
        @endphp

        <div x-show="selectedTrx === {{ $trx->transaksi_id }}" @click.away="modalOpen = false; selectedTrx = null"
            data-cy="modal-transaksi-{{ $trx->transaksi_id }}"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="px-6 md:px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <h2 data-cy="modal-title-{{ $trx->transaksi_id }}" class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    Detail Transaksi
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-sm font-medium">
                        #TRX{{ str_pad($trx->transaksi_id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                </h2>
                <button @click="modalOpen = false; selectedTrx = null"
                    data-cy="modal-close-{{ $trx->transaksi_id }}"
                    class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-4 md:p-8 flex-1 overflow-y-auto bg-gray-50/50 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div class="space-y-6">
                        {{-- Customer Info --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Customer
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div class="grid grid-cols-3">
                                    <span class="text-gray-500">Nama</span>
                                    <span data-cy="modal-customer-name-{{ $trx->transaksi_id }}" class="font-semibold text-gray-900 col-span-2">{{ $trx->nama_customer }}</span>
                                </div>
                                <div class="grid grid-cols-3">
                                    <span class="text-gray-500">Alamat</span>
                                    <span data-cy="modal-customer-alamat-{{ $trx->transaksi_id }}" class="font-medium text-gray-700 col-span-2 leading-relaxed">{{ $trx->alamat_customer }}</span>
                                </div>
                            </div>

                            @if(!$isKustom)
                            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Bukti Pembayaran (Xendit Gateway)
                                </h3>

                                @if($trx->paymentInvoice)
                                <div data-cy="xendit-invoice-{{ $trx->transaksi_id }}" class="bg-blue-50/50 border border-blue-200 rounded-lg p-4 flex flex-col gap-3">
                                    <div class="flex items-start gap-2.5">
                                        <div class="bg-blue-100 rounded-full p-1 mt-0.5 shrink-0">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-blue-800">Invoice</h5>
                                            <p class="text-[10px] text-blue-600 mt-0.5 leading-relaxed">Status Gateway: <span class="font-bold uppercase">{{ $trx->paymentInvoice->status }}</span></p>
                                        </div>
                                    </div>

                                    <div class="bg-white border border-blue-100 rounded-md p-3 flex flex-col gap-2 shadow-sm">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-400">ID Referensi:</span>
                                            <span class="font-mono text-gray-800 font-medium">{{ $trx->paymentInvoice->external_id }}</span>
                                        </div>
                                        @if($trx->paymentInvoice->paid_at)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-400">Tanggal Bayar:</span>
                                            <span class="text-gray-800 font-medium">{{ $trx->paymentInvoice->paid_at->format('d M Y H:i') }} WIB</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2 mt-1">
                                            <span class="text-gray-400">Total Nominal:</span>
                                            <span class="font-bold text-gray-900">Rp {{ number_format($trx->paymentInvoice->amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    @if($trx->paymentInvoice->invoice_url)
                                    <a href="{{ $trx->paymentInvoice->invoice_url }}" target="_blank"
                                        data-cy="btn-view-xendit-{{ $trx->transaksi_id }}"
                                        class="w-full text-center text-xs font-bold text-white bg-blue-600 border border-transparent px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Buka Invoice Xendit
                                    </a>
                                    @endif
                                </div>
                                @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                    <p class="text-xs text-yellow-700 font-medium">Data invoice Xendit tidak ditemukan untuk transaksi ini.</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        @if($isKustom)
                        @php
                        $kustomOrder = $trx->orderKustoms->first();
                        $paymentAttachment = $kustomOrder->attachments->first(function ($attachment) {
                        return str_contains($attachment->path, 'payments/kustom');
                        });
                        @endphp

                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Bukti Pembayaran (Kustom)
                            </h3>

                            @if($paymentAttachment)
                            <div data-cy="payment-uploaded-{{ $trx->transaksi_id }}" class="bg-green-50/50 border border-green-200 rounded-lg p-4 flex flex-col gap-3">
                                <div class="flex items-start gap-2.5">
                                    <div class="bg-green-100 rounded-full p-1 mt-0.5 shrink-0">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-green-800">Pembayaran Selesai</h5>
                                        <p class="text-[10px] text-green-600 mt-0.5 leading-relaxed">Bukti pembayaran kustom telah berhasil diunggah.</p>
                                    </div>
                                </div>
                                <div class="bg-white border border-green-100 rounded-md p-2.5 flex items-center justify-between shadow-sm group">
                                    <div class="flex items-center gap-3 overflow-hidden pr-3">
                                        <div class="bg-gray-50 border border-gray-100 p-2 rounded text-gray-500 shrink-0 group-hover:text-blue-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span data-cy="payment-filename-{{ $trx->transaksi_id }}" class="text-[11px] font-semibold text-gray-800 truncate">{{ basename($paymentAttachment->path) }}</span>
                                            <span class="text-[9px] text-gray-400 uppercase">Dokumen Tersimpan</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $paymentAttachment->path) }}" target="_blank"
                                        data-cy="btn-open-payment-{{ $trx->transaksi_id }}"
                                        class="shrink-0 text-[10px] font-semibold text-gray-700 bg-white border border-gray-300 px-3 py-1.5 rounded hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                        Buka File
                                    </a>
                                </div>
                            </div>
                            @else
                            <form action="{{ route('manage.transaksi.upload-payment') }}" method="POST" enctype="multipart/form-data"
                                data-cy="form-upload-payment-{{ $trx->transaksi_id }}"
                                class="flex flex-col gap-3" x-data="{ fileName: '' }">
                                @csrf
                                <input type="hidden" name="order_kustom_id" value="{{ $kustomOrder->order_kustom_id }}">
                                <label for="file_payment_{{ $trx->transaksi_id }}" data-cy="upload-payment-dropzone-{{ $trx->transaksi_id }}" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition duration-200">
                                    <div class="flex flex-col items-center justify-center px-4 text-center">
                                        <svg class="w-6 h-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <div x-show="!fileName">
                                            <p class="text-xs font-semibold text-gray-700">Klik untuk mengunggah file</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, atau PDF (Max. 5MB)</p>
                                        </div>
                                        <div x-show="fileName" x-cloak class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="text-xs font-bold text-green-600 truncate max-w-[200px]" x-text="fileName"></p>
                                        </div>
                                    </div>
                                    <input id="file_payment_{{ $trx->transaksi_id }}" type="file" name="file_payment"
                                        data-cy="input-file-payment-{{ $trx->transaksi_id }}"
                                        accept=".jpg,.jpeg,.png,.pdf" class="hidden" required
                                        @change="fileName = $event.target.files[0].name">
                                </label>
                                <button type="submit"
                                    data-cy="btn-upload-payment-{{ $trx->transaksi_id }}"
                                    class="w-full bg-gray-900 text-white px-3 py-2.5 rounded-lg text-xs transition font-medium shadow-sm flex justify-center items-center gap-2"
                                    :class="fileName ? 'hover:bg-black' : 'opacity-70 hover:bg-gray-800'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Unggah Bukti & Lunasi
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif

                        {{-- Update Status --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Update Status
                            </h3>
                            <form action="{{ route('manage.transaksi.update', $trx->transaksi_id) }}" method="POST"
                                id="form-{{$trx->transaksi_id}}"
                                data-cy="form-update-status-{{ $trx->transaksi_id }}">
                                @csrf @method('PUT')
                                <div class="space-y-4 text-sm">
                                    <div class="flex justify-between items-center">
                                        <label class="text-gray-600 font-medium">Status Bayar</label>
                                        <span data-cy="modal-status-bayar-{{ $trx->transaksi_id }}" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $statusBayar == 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $trx->status }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="text-gray-600 font-medium">No. Resi Customer</label>
                                        <input type="text" name="no_resi_customer"
                                            value="{{ $trx->no_resi_customer }}"
                                            placeholder="Masukkan No. Resi"
                                            data-cy="input-resi-{{ $trx->transaksi_id }}"
                                            class="border border-gray-300 rounded-lg px-3 py-2 w-full bg-gray-50 focus:bg-white focus:ring-1 focus:ring-black outline-none transition-all">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Item detail panel --}}
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm h-full flex flex-col">
                            <h3 class="text-lg font-bold mb-4 text-gray-900 border-b border-gray-100 pb-3">Item {{ $isKustom ? 'Kustom' : 'Katalog' }}</h3>

                            @if($isKustom)
                            @php $kustom = $trx->orderKustoms->first(); @endphp
                            <div class="flex-1 flex flex-col justify-between">
                                <div data-cy="modal-kustom-detail-{{ $trx->transaksi_id }}" class="space-y-5 text-sm text-gray-700">
                                    <div class="grid grid-cols-3">
                                        <strong class="col-span-1 text-gray-500">Tipe</strong>
                                        <span data-cy="kustom-tipe-{{ $trx->transaksi_id }}" class="col-span-2 font-semibold text-gray-900">{{ $kustom->tipe_kustom }}</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <strong class="col-span-1 text-gray-500">Kain / Catatan</strong>
                                        <span data-cy="kustom-catatan-{{ $trx->transaksi_id }}" class="col-span-2 font-medium text-gray-700 leading-relaxed">{{ $kustom->catatan ?? '-' }}</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <strong class="col-span-1 text-gray-500">Jumlah</strong>
                                        <span data-cy="kustom-qty-{{ $trx->transaksi_id }}" class="col-span-2 font-bold text-gray-900">{{ $kustom->quantity }} pcs</span>
                                    </div>
                                    <div class="block pt-2">
                                        <strong class="block text-gray-500 mb-2 font-bold uppercase text-[10px] tracking-widest">Detail Kustomisasi</strong>
                                        <x-shared.chip :details="$kustom->detail_pilihan_kustomisasi" :trxId="$trx->transaksi_id" />
                                    </div>
                                </div>
                                <div class="mt-8 border-t border-gray-200 pt-5 text-right">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Grand Total</p>
                                    <span data-cy="modal-total-{{ $trx->transaksi_id }}" class="font-black text-3xl text-gray-900">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @else
                            <div data-cy="modal-katalog-items-{{ $trx->transaksi_id }}" class="space-y-4 flex-1">
                                @foreach($trx->produkTransaksis as $pt)
                                <div data-cy="katalog-item-{{ $pt->produk_transaksi_id ?? $loop->index }}" class="flex gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-white hover:shadow-md hover:border-gray-200 transition-all">
                                    <div class="w-16 h-20 md:w-20 md:h-28 bg-white border border-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center shadow-sm">
                                        <span class="text-[10px] text-gray-300">IMG</span>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                        <h4 class="font-bold text-sm text-gray-900 truncate">{{ $pt->produk->nama ?? 'Produk Katalog' }}</h4>
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-gray-900 text-white text-[10px] font-bold">{{ $pt->size }}</span>
                                            <span class="text-xs text-gray-500">x{{ $pt->quantity }} pcs</span>
                                        </div>
                                        <p class="font-bold text-sm text-gray-900">Rp{{ number_format($pt->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-8 border-t border-gray-200 pt-5 text-right shrink-0">
                                <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Grand Total</p>
                                <span data-cy="modal-total-{{ $trx->transaksi_id }}" class="font-black text-3xl text-gray-900">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 md:px-8 py-4 border-t border-gray-100 bg-gray-50 shrink-0 flex flex-col sm:flex-row justify-end gap-3 items-center">
                <button type="button" @click="modalOpen = false; selectedTrx = null"
                    data-cy="btn-modal-tutup-{{ $trx->transaksi_id }}"
                    class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 rounded-lg text-sm hover:bg-white text-gray-600 transition font-medium shadow-sm">
                    Tutup
                </button>
                <button type="submit" form="form-{{$trx->transaksi_id}}"
                    data-cy="btn-simpan-{{ $trx->transaksi_id }}"
                    class="w-full sm:w-auto px-8 py-2.5 bg-gray-900 text-white rounded-lg text-sm hover:bg-black transition font-bold shadow-md flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>

        </div>
        @endforeach
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
@endsection