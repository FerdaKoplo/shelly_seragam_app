@extends('layouts.user.layout')
@section('title', 'Katalog')
@section('content')

    <div class="flex gap-16 flex-col justify-center mt-6 items-center px-10">
        <div class="flex items-center gap-7 w-full">
            <div>
                <button class="border border-black p-2 rounded-md">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.36869 0.842173C3.36869 0.378978 2.98971 0 2.52652 0C2.06332 0 1.68435 0.378978 1.68435 0.842173V4.21086H0V9.2639H5.05304V4.21086H3.36869V0.842173ZM6.73738 12.6326C6.73738 13.7274 7.44481 14.6538 8.42173 15.0075V18.5278H10.1061V15.0075C11.083 14.6622 11.7904 13.7358 11.7904 12.6326V10.9482H6.73738V12.6326ZM0 12.6326C0 13.7274 0.707425 14.6538 1.68435 15.0075V18.5278H3.36869V15.0075C4.34561 14.6538 5.05304 13.7274 5.05304 12.6326V10.9482H0V12.6326ZM16.8435 4.21086V0.842173C16.8435 0.378978 16.4645 0 16.0013 0C15.5381 0 15.1591 0.378978 15.1591 0.842173V4.21086H13.4748V9.2639H18.5278V4.21086H16.8435ZM10.1061 0.842173C10.1061 0.378978 9.7271 0 9.2639 0C8.80071 0 8.42173 0.378978 8.42173 0.842173V4.21086H6.73738V9.2639H11.7904V4.21086H10.1061V0.842173ZM13.4748 12.6326C13.4748 13.7274 14.1822 14.6538 15.1591 15.0075V18.5278H16.8435V15.0075C17.8204 14.6622 18.5278 13.7358 18.5278 12.6326V10.9482H13.4748V12.6326Z"
                            fill="#323232" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 relative">
                <input type="text" placeholder="Cari Produk"
                    class="w-full p-2 rounded-md  border border-black focus:outline-none">
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.5958 11.0843H11.7997L11.5176 10.8122C12.5051 9.66348 13.0996 8.17214 13.0996 6.54981C13.0996 2.9323 10.1673 0 6.54981 0C2.9323 0 0 2.9323 0 6.54981C0 10.1673 2.9323 13.0996 6.54981 13.0996C8.17214 13.0996 9.66348 12.5051 10.8122 11.5176L11.0843 11.7997V12.5958L16.1226 17.624L17.624 16.1226L12.5958 11.0843ZM6.54981 11.0843C4.04073 11.0843 2.01532 9.05889 2.01532 6.54981C2.01532 4.04073 4.04073 2.01532 6.54981 2.01532C9.05889 2.01532 11.0843 4.04073 11.0843 6.54981C11.0843 9.05889 9.05889 11.0843 6.54981 11.0843Z"
                            fill="#323232" />
                    </svg>
                </div>
            </div>

            <div>
                {{ $katalog->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>

        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-16">
            @forelse ($katalog as $item)
                <div class="bg-white  shadow-md rounded-2xl relative group hover:shadow-lg transition">

                    <button
                        class="absolute top-3 right-3 bg-white p-1.5 rounded-full shadow-sm hover:bg-gray-100 transition z-10">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0 9.48896V11.9856H2.49666L9.86013 4.62215L7.36348 2.12549L0 9.48896ZM11.7909 2.6914C12.0505 2.43174 12.0505 2.01231 11.7909 1.75265L10.233 0.194739C9.97332 -0.0649131 9.55388 -0.0649131 9.29422 0.194739L8.07586 1.41311L10.5725 3.90977L11.7909 2.6914V2.6914Z"
                                fill="#323232" />
                        </svg>

                    </button>

                    <div class="w-full aspect-square overflow-hidden rounded-xl bg-[#F5F5F5] mb-3">
                        <img src="{{ $item->fotos->first()?->path ?? asset('images/default-product.jpg') }}"
                            alt="{{ $item->produk->nama }}" class="w-full h-full object-contain">
                    </div>

                    <div class="space-y-1 grid grid-cols-1 px-2 pb-3">
                        <h3 class="font-bold text-xl text-gray-900 truncate">{{ $item->produk->nama_produk ?? 'Nama Produk' }}
                        </h3>

                        <p class="text-[10px] text-gray-500">#{{ $item->kategori ?? 'Kategori Tidak Tersedia' }}</p>

                        <div class="flex items-center justify-between mt-2">
                            <p class="font-bold text-xl">Rp.{{ number_format($item->harga, 0, ',', '.') }}</p>

                            <button class="text-gray-700 hover:text-black">
                                <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.5401 5.3262H9.27647V2.6631H6.70214V5.3262H4.4385L7.98931 8.87701L11.5401 5.3262ZM14.2032 0H1.76652C0.781176 0 0 0.79893 0 1.7754V14.2032C0 15.1797 0.781176 15.9786 1.76652 15.9786H14.2032C15.1797 15.9786 15.9786 15.1797 15.9786 14.2032V1.7754C15.9786 0.79893 15.1797 0 14.2032 0ZM14.2032 14.2032H1.7754V11.5401H4.93562C5.54813 12.5965 6.68439 13.3155 7.99818 13.3155C9.31198 13.3155 10.4394 12.5965 11.0607 11.5401H14.2032V14.2032ZM14.2032 9.76471H9.77358C9.77358 10.7412 8.97465 11.5401 7.99818 11.5401C7.02171 11.5401 6.22278 10.7412 6.22278 9.76471H1.7754L1.76652 1.7754H14.2032V9.76471Z"
                                        fill="#323232" />
                                </svg>

                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500">
                    <p>Belum Ada Produk Yang Tersedia</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- floating button --}}
    <a href="{{ route('manage.katalog.create') }}">
        <button
            class="z-50 fixed bottom-8 flex font-inter items-center gap-3 right-4 px-5 bg-white border border-black text-black p-3 rounded-full shadow-md ">
            <p>
                Tambahkan Produk Baru
            </p>

            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M21.4891 13.1995L16.1213 13.1995V7.83171H13.199V13.1995L7.83124 13.1995V16.1217L13.199 16.1217V21.4895H16.1213V16.1217L21.4891 16.1217V13.1995ZM25.0227 4.29808C19.2922 -1.4324 10.0281 -1.4324 4.29762 4.29808C-1.43286 10.0286 -1.43286 19.2927 4.29762 25.0232C10.0281 30.7536 19.2922 30.7536 25.0227 25.0232C30.7532 19.2927 30.7532 10.0286 25.0227 4.29808ZM6.37013 22.9506C1.80025 18.3808 1.80025 10.9405 6.37013 6.37059C10.94 1.80071 18.3803 1.80071 22.9502 6.37059C27.5201 10.9405 27.5201 18.3808 22.9502 22.9506C18.3803 27.5205 10.94 27.5205 6.37013 22.9506Z"
                    fill="#323232" />
            </svg>

        </button>
    </a>



@endsection