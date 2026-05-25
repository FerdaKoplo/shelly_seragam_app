@extends('layouts.user.layout')
@section('title', 'Manage Voucher')
@section('content')

    <div x-data class="flex gap-8 md:gap-16 flex-col justify-center items-center px-4 md:px-10" data-cy="voucher-page">
        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-7 w-full" data-cy="voucher-toolbar">
            <div class="w-full md:w-auto flex justify-between items-center gap-4">
                <div hidden>
                    <button id="btn-filter-voucher" data-cy="filter-voucher-button" type="button"
                        @click="$dispatch('open-modal', 'modal-filter-voucher')"
                        class="border border-black p-2.5 rounded-md hover:bg-gray-50 transition">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.36869 0.842173C3.36869 0.378978 2.98971 0 2.52652 0C2.06332 0 1.68435 0.378978 1.68435 0.842173V4.21086H0V9.2639H5.05304V4.21086H3.36869V0.842173ZM6.73738 12.6326C6.73738 13.7274 7.44481 14.6538 8.42173 15.0075V18.5278H10.1061V15.0075C11.083 14.6622 11.7904 13.7358 11.7904 12.6326V10.9482H6.73738V12.6326ZM0 12.6326C0 13.7274 0.707425 14.6538 1.68435 15.0075V18.5278H3.36869V15.0075C4.34561 14.6538 5.05304 13.7274 5.05304 12.6326V10.9482H0V12.6326ZM16.8435 4.21086V0.842173C16.8435 0.378978 16.4645 0 16.0013 0C15.5381 0 15.1591 0.378978 15.1591 0.842173V4.21086H13.4748V9.2639H18.5278V4.21086H16.8435ZM10.1061 0.842173C10.1061 0.378978 9.7271 0 9.2639 0C8.80071 0 8.42173 0.378978 8.42173 0.842173V4.21086H6.73738V9.2639H11.7904V4.21086H10.1061V0.842173ZM13.4748 12.6326C13.4748 13.7274 14.1822 14.6538 15.1591 15.0075V18.5278H16.8435V15.0075C17.8204 14.6622 18.5278 13.7358 18.5278 12.6326V10.9482H13.4748V12.6326Z"
                                fill="#323232" />
                        </svg>
                    </button>
                </div>

                <div class="md:hidden">
                    {{ $vouchers->onEachSide(0)->links('vendor.pagination.custom') }}
                </div>
            </div>

            <form data-cy="voucher-search-form" method="GET" action="{{ route('manage.voucher') }}"
                class="flex-1 relative w-full">
                <input data-cy="voucher-search-input" type="text" id="searchInput" name="search"
                    value="{{ request('search') }}" placeholder="Cari Kode atau Nama Voucher"
                    class="w-full p-2.5 rounded-md border border-black focus:outline-none text-sm">
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.5958 11.0843H11.7997L11.5176 10.8122C12.5051 9.66348 13.0996 8.17214 13.0996 6.54981C13.0996 2.9323 10.1673 0 6.54981 0C2.9323 0 0 2.9323 0 6.54981C0 10.1673 2.9323 13.0996 6.54981 13.0996C8.17214 13.0996 9.66348 12.5051 10.8122 11.5176L11.0843 11.7997V12.5958L16.1226 17.624L17.624 16.1226L12.5958 11.0843ZM6.54981 11.0843C4.04073 11.0843 2.01532 9.05889 2.01532 6.54981C2.01532 4.04073 4.04073 2.01532 6.54981 2.01532C9.05889 2.01532 11.0843 4.04073 11.0843 6.54981C11.0843 9.05889 9.05889 11.0843 6.54981 11.0843Z"
                            fill="#323232" />
                    </svg>
                </div>
            </form>

            <div class="hidden md:block" data-cy="voucher-pagination">
                {{ $vouchers->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        </div>

        <div data-cy="voucher-grid"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8 w-full">
            @forelse ($vouchers as $voucher)
                <div data-cy="voucher-card"
                    class="bg-white border border-gray-200 shadow-sm rounded-2xl relative group hover:shadow-md transition p-5 flex flex-col justify-between h-full min-h-[280px]">

                    <div
                        class="absolute top-3 right-3 flex gap-2 md:opacity-0 md:group-hover:opacity-100 transition duration-200 z-10">
                        <a data-cy="btn-edit-voucher" href="{{ route('manage.voucher.edit', $voucher->id) }}"
                            class="bg-white p-2 md:p-2.5 rounded-full shadow-md border border-gray-100 hover:bg-gray-50 transition"
                            title="Edit Voucher">
                            <svg class="w-4 h-4" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 9.48896V11.9856H2.49666L9.86013 4.62215L7.36348 2.12549L0 9.48896ZM11.7909 2.6914C12.0505 2.43174 12.0505 2.01231 11.7909 1.75265L10.233 0.194739C9.97332 -0.0649131 9.55388 -0.0649131 9.29422 0.194739L8.07586 1.41311L10.5725 3.90977L11.7909 2.6914V2.6914Z"
                                    fill="#323232" />
                            </svg>
                        </a>
                        <form data-cy="delete-voucher-form" action="{{ route('manage.voucher.destroy', $voucher->id) }}"
                            method="POST" onsubmit="return confirm('Hapus voucher ini?');">
                            @csrf
                            @method('DELETE')
                            <button data-cy="btn-delete-voucher" type="submit"
                                class="bg-white p-2 md:p-2.5 rounded-full shadow-md border border-gray-100 text-gray-700 hover:text-red-600 hover:bg-red-50 transition"
                                title="Hapus Voucher">
                                <svg class="w-4 h-4" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.973384 15.5741C0.973384 16.6449 1.84943 17.5209 2.92015 17.5209H10.7072C11.7779 17.5209 12.654 16.6449 12.654 15.5741V3.89354H0.973384V15.5741ZM13.6274 0.973384H10.2205L9.24715 0H4.38023L3.40684 0.973384H0V2.92015H13.6274V0.973384Z"
                                        fill="currentColor" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div>
                        <span data-cy="voucher-type"
                            class="inline-block bg-[#F5F5F5] text-[#323232] text-[9px] md:text-[10px] font-bold px-2 py-1 rounded-md mb-3 uppercase tracking-wider">
                            {{ $voucher->jenis_voucher }}
                        </span>
                        <h3 data-cy="voucher-name"
                            class="font-bold text-lg md:text-xl text-gray-900 leading-tight mb-2 truncate"
                            title="{{ $voucher->nama_voucher }}">
                            {{ $voucher->nama_voucher }}
                        </h3>
                        <p data-cy="voucher-description" class="text-[11px] md:text-xs text-gray-500 line-clamp-2 min-h-[32px]">
                            {{ $voucher->deskripsi }}</p>

                        <div class="mt-4 bg-[#F5F5F5] border border-dashed border-gray-300 rounded-lg p-3 text-center">
                            <span data-cy="voucher-code"
                                class="font-mono font-bold text-[#323232] text-base md:text-lg tracking-widest">{{ $voucher->kode_voucher }}</span>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[11px] md:text-xs font-medium text-gray-500">Nilai Diskon</span>
                            <span data-cy="voucher-discount" class="font-bold text-xl md:text-2xl text-[#323232]">
                                @if(strtolower($voucher->jenis_voucher) == 'nominal')
                                    Rp{{ number_format($voucher->nilai_diskon, 0, ',', '.') }}
                                @else
                                    {{ $voucher->nilai_diskon }}%
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] md:text-[10px] text-gray-400 mt-2">
                            <span
                                data-cy="voucher-start-date">{{ \Carbon\Carbon::parse($voucher->tanggal_mulai)->format('d M Y') }}</span>
                            <span class="mx-1">hingga</span>
                            <span
                                data-cy="voucher-end-date">{{ \Carbon\Carbon::parse($voucher->tanggal_berakhir)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div data-cy="empty-voucher-state"
                    class="col-span-full flex flex-col items-center justify-center min-h-[40vh] font-medium text-lg md:text-xl text-center py-2 text-black ">
                    <p>Belum Ada Voucher Yang Tersedia</p>
                </div>
            @endforelse
        </div>
    </div>

    <a data-cy="create-voucher-link" href="{{ route('manage.voucher.create') }}">
        <button data-cy="btn-tambah-voucher-baru" id="btn-tambah-voucher-baru"
            class="z-50 fixed bottom-6 md:bottom-8 flex font-inter items-center gap-2 md:gap-3 right-4 md:right-8 px-4 md:px-5 bg-white border border-black text-black py-2.5 md:p-3 rounded-full shadow-lg hover:bg-gray-50 transition active:scale-95">
            <p class="text-sm md:text-base font-bold">
                <span class="hidden sm:inline">Tambahkan</span> Voucher Baru
            </p>
            <svg width="24" height="24" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="md:w-[30px] md:h-[30px]">
                <path
                    d="M21.4891 13.1995L16.1213 13.1995V7.83171H13.199V13.1995L7.83124 13.1995V16.1217L13.199 16.1217V21.4895H16.1213V16.1217L21.4891 16.1217V13.1995ZM25.0227 4.29808C19.2922 -1.4324 10.0281 -1.4324 4.29762 4.29808C-1.43286 10.0286 -1.43286 19.2927 4.29762 25.0232C10.0281 30.7536 19.2922 30.7536 25.0227 25.0232C30.7532 19.2927 30.7532 10.0286 25.0227 4.29808ZM6.37013 22.9506C1.80025 18.3808 1.80025 10.9405 6.37013 6.37059C10.94 1.80071 18.3803 1.80071 22.9502 6.37059C27.5201 10.9405 27.5201 18.3808 22.9502 22.9506C18.3803 27.5205 10.94 27.5205 6.37013 22.9506Z"
                    fill="#323232" />
            </svg>
        </button>
    </a>

@endsection