@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

<div class="max-w-full mx-auto px-4 py-8"
    x-data="{ 
        activeImage: 'https://picsum.photos/id/1/1080', 
        quantity: 1,
        selectedSize: 'M',
        selectedColor: 'red',
        basePrice: {{ $product->price }}
     }">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('katalog') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i class="fa-solid fa-chevron-left text-xl"></i>
        </a>
        <h1 class="text-4xl font-bebas  tracking-widest">Detail Produk</h1>
    </div>

    <div class=" grid grid-cols-1 lg:grid-cols-12 gap-4 mx-auto">
        <div class="lg:col-span-1 flex flex-col space-y-4">
            @foreach(range(1, 4) as $i)
            @php
            // Mocking different images using the index
            $thumbnailUrl = "https://picsum.photos/id/" . $i . "/1080" ;
            @endphp

            <button
                @click="activeImage = '{{ $thumbnailUrl }}'"
                class="border transition-all duration-200 aspect-[3/4] overflow-hidden"
                :class="activeImage === '{{ $thumbnailUrl }}' ? 'border-black ring-1 ring-black' : 'border-transparent hover:border-gray-300'">
                <img src="{{ $thumbnailUrl }}" alt="Thumbnail {{ $i }}" class="w-full h-full object-cover">
            </button>
            @endforeach
        </div>

        <div class="lg:col-span-6 bg-gray-50 flex items-center justify-center rounded-sm overflow-hidden min-h-[500px]">
            <img
                :key="activeImage" {{-- Adding a key helps Alpine/Browser track the swap --}}
                :src="activeImage"
                alt="{{ $product->name }}"
                class="max-h-[700px] object-fill transition-opacity duration-300"
                x-transition:enter="opacity-0"
                x-transition:enter-end="opacity-100">
        </div>

        <div class="lg:col-span-5">
            <h1 class="text-4xl font-normal text-gray-900 mb-2">{{ $product->name }}</h1>
            <p class="text-5xl font-bold mb-2">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="text-gray-600 mb-8">Stok: {{ $product->stock }}</p>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-bold text-lg">Ukuran</span>
                    <button class="text-xs flex items-center text-gray-500 hover:text-black">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1"></path>
                        </svg>
                        Panduan Ukuran
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <x-guest.katalog.size-selector label="XS" id="XS" />
                    <x-guest.katalog.size-selector label="S" id="S" />
                    <x-guest.katalog.size-selector label="M" id="M" />
                    <x-guest.katalog.size-selector label="L" id="L" />
                    <x-guest.katalog.size-selector label="XL" id="XL" />
                    <x-guest.katalog.size-selector label="XXL" id="XXL" />
                </div>
            </div>

            <div class="mb-8">
                <span class="font-bold text-lg block mb-3">Varian Warna</span>
                <div class="grid grid-cols-3 gap-2">
                    <x-guest.katalog.color-swatch color="#e56b6b" id="red" />
                    <x-guest.katalog.color-swatch color="#d9a26c" id="tan" />
                    <x-guest.katalog.color-swatch color="#2d1b1b" id="dark" />
                    <x-guest.katalog.color-swatch color="#6b5ce5" id="purple" />
                    <x-guest.katalog.color-swatch color="#5ca6e5" id="blue" />
                    <x-guest.katalog.color-swatch color="#5ce57d" id="green" />
                </div>
            </div>

            <div class="mb-8">
                <span class="font-bold text-lg block mb-3">Quantity</span>
                <div class="flex items-center border border-gray-300 w-24 rounded-md overflow-hidden">
                    <button @click="if(quantity > 1) quantity--" class="px-3 py-1 text-xl hover:bg-gray-100 transition">-</button>
                    <input type="text" x-model="quantity" class="w-full text-center border-none focus:ring-0 text-sm font-bold" readonly>
                    <button @click="quantity++" class="px-3 py-1 text-xl hover:bg-gray-100 transition">+</button>
                </div>
            </div>

            <div class="mt-10">
                <p class="text-5xl font-bold mb-6">
                    Rp<span x-text="(quantity * basePrice).toLocaleString('id-ID')"></span>
                </p>

                <div class="flex flex-col space-y-3">
                    <x-shared.button variant="outline" :rounded="false">
                        Add To Cart
                    </x-shared.button>
                    <x-shared.button variant="dark" :rounded="false">
                        Checkout
                    </x-shared.button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-16 max-w-4xl">
        <p class="text-gray-500 mb-2">Terjual : 1.231.214</p>
        <h3 class="font-bold mb-2">Deskripsi</h3>
        <p class="text-gray-600 leading-relaxed mb-6">
            {{ $product->description }}
        </p>
        <div class="flex gap-2 font-bold text-black uppercase tracking-wider text-sm">
            @foreach($product->tags as $tag)
            <span>{{ $tag }}</span>
            @endforeach
        </div>
    </div>
</div>

@endsection