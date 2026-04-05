@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

    <div class="px-16 justify-center items-center my-6 max-w-full overflow-y-auto flex-grow">

        {{-- Page Title & Back Button --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('home') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fa-solid fa-chevron-left text-xl"></i>
            </a>
            <h1 class="text-4xl font-bebas tracking-widest">Semua Katalog</h1>
        </div>

        <div class="flex md:flex-row gap-4 mb-10 items-center">
            {{-- Search and Filter Row --}}
            <div x-data class="flex flex-1 gap-2 md:w-auto">

                <button type="button" @click="$dispatch('open-modal', 'modal-filter-produk-katalog')"
                    class="p-3 border rounded-xl hover:bg-gray-50 shadow-sm transition-all">
                    <i class="fa-solid fa-sliders text-black"></i>
                </button>

                <form method="GET" action="{{ route('katalog') }}"
                    class="flex items-center flex-1 md:w-80 border rounded-xl shadow-sm px-4 py-3 focus-within:ring-2 focus-within:ring-secondary">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Produk"
                        class="flex-1 bg-transparent outline-none text-gray-700">
                    <button type="submit" class="text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

            </div>
        </div>

        {{-- Product Grid --}}
        @if ($katalog->count())
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-8">
                @foreach ($katalog as $item)
                    @php
                        $firstFoto = $item->fotos->first();
                        $fallbackImage = 'https://picsum.photos/seed/katalog-' . $item->katalog_id . '/600/800';
                        $imageUrl = $firstFoto ? asset('storage/' . $firstFoto->path) : $fallbackImage;
                    @endphp

                    <a href="{{ route('product.show', $item->katalog_id) }}" class="block hover:opacity-90 transition">
                        <x-cards.product-card.vertical :name="$item->produk->nama_produk" :category="'#' . $item->kategori" :price="number_format($item->harga, 0, ',', '.')"
                            :image="$imageUrl" />
                    </a>
                @endforeach
            </div>

            {{-- Pagination (sementara di bawah dulu) --}}
            <div class="mt-10">
                {{ $katalog->links() }}
            </div>
        @else
            <div class="flex-col flex w-full h-full justify-center items-center">
                <p class="text-black mb-2 font-medium text-3xl">Produk Tidak Ditemukan</p>
                <p class="text-gray-600 mb-8 font-normal text-[20px]">Tidak menemukan yang kamu cari?</p>

                <div class="flex w-[50%] justify-center items-center ">
                    <x-shared.button href="{{ route('kustom') }}" variant="outline" class="flex flex-grow md:w-auto py-4 ">
                        <span class="text-xl font-bebas text-[30px] tracking-widest gap-4">
                            <i class="fa-solid fa-shirt"></i>
                            Buat Seragammu Sendiri!
                            <i class="fa-solid fa-pen"></i>
                        </span>
                    </x-shared.button>
                </div>
            </div>
        @endif

        {{-- Floating Hubungi Kami Button --}}
        <div class="fixed bottom-[10%] right-[2%] z-50">
            <x-shared.button variant="outline" class="w-full md:w-auto py-4 px-4">
                <p class="font-medium text-xl">
                    Hubungi Kami
                <div class="ms-3 bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                </div>
                </p>
            </x-shared.button>
        </div>

    </div>

    {{-- <x-guest.katalog.modals.filter-produk-katalog /> --}}
    <x-guest.katalog.modals.filter-produk-katalog :categories="$categories" />

@endsection