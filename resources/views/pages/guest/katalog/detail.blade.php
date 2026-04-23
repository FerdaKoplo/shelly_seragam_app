@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

@php
$firstFoto = $item->fotos->first();
$fallbackImage = 'https://picsum.photos/id/1/1080';

$firstImageUrl = $firstFoto ? asset('storage/' . $firstFoto->path) : $fallbackImage;

$basePrice = (float) $item->harga;
@endphp

<div
    data-cy="product-detail"
    class="max-w-full mx-auto px-4 py-8"
    x-data="{
        activeImage: '{{ $firstImageUrl }}',
        quantity: 1,
        selectedSize: 'M',
        selectedColor: 'red',
        basePrice: {{ $basePrice }}
    }">

    <div class="flex items-center gap-4 mb-8">
        <a data-cy="btn-back" href="{{ route('katalog') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i class="fa-solid fa-chevron-left text-xl"></i>
        </a>
        <h1 class="text-4xl font-bebas tracking-widest">Detail Produk</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mx-auto items-start">

        {{-- Thumbnails --}}
        <div class="lg:col-span-1 flex flex-col space-y-4">
            @forelse($item->fotos as $foto)
            @php $url = asset('storage/' . $foto->path); @endphp

            <button
                type="button"
                data-cy="thumbnail-{{ $loop->index }}"
                @click="activeImage = '{{ $url }}'"
                class="border transition-all duration-200 aspect-[3/4] overflow-hidden"
                :class="activeImage === '{{ $url }}' ? 'border-black ring-1 ring-black' : 'border-transparent hover:border-gray-300'">
                <img src="{{ $url }}" alt="Thumbnail" class="w-full h-full object-cover">
            </button>
            @empty
            {{-- fallback kalau belum ada foto --}}
            @foreach(range(1, 4) as $i)
            @php $thumbnailUrl = "https://picsum.photos/id/" . $i . "/1080"; @endphp
            <button
                type="button"
                data-cy="thumbnail-fallback-{{ $i }}"
                @click="activeImage = '{{ $thumbnailUrl }}'"
                class="border transition-all duration-200 aspect-[3/4] overflow-hidden"
                :class="activeImage === '{{ $thumbnailUrl }}' ? 'border-black ring-1 ring-black' : 'border-transparent hover:border-gray-300'">
                <img src="{{ $thumbnailUrl }}" alt="Thumbnail {{ $i }}" class="w-full h-full object-cover">
            </button>
            @endforeach
            @endforelse
        </div>

        {{-- Main image --}}
        <div class="lg:col-span-6 bg-gray-50 flex items-center justify-center rounded-sm overflow-hidden min-h-[500px]">
            <img
            data-cy="main-image"
                :key="activeImage"
                :src="activeImage"
                alt="{{ $item->produk->nama_produk }}"
                class="max-h-[700px] object-fill transition-opacity duration-300 min-w-[700px]"
                x-transition:enter="opacity-0"
                x-transition:enter-end="opacity-100">
        </div>

        {{-- Product info --}}
        <div class="lg:col-span-4 mx-auto">
            <h1  data-cy="product-name" class="text-4xl font-normal text-gray-900 mb-2">{{ $item->produk->nama_produk }}</h1>
            <p data-cy="product-price" class="text-5xl font-bold mb-2">Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
            <p data-cy="product-stock" class="text-gray-600 mb-8">Stok: {{ $item->stok }}</p>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-bold text-lg">Ukuran</span>
                    <button data-cy="btn-size-guide" @click="$dispatch('open-modal', 'modal-panduan-ukuran')" class="text-xs flex items-center text-gray-500 hover:text-black">
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

            {{-- Quantity Button --}}
            <x-shared.quantity-button model="quantity" :max="$item->stok" />

            <div class="mt-10">
                <p data-cy="total-price" class="text-5xl font-bold mb-6">
                    Rp<span x-text="(quantity * basePrice).toLocaleString('id-ID')"></span>
                </p>

                <div class="flex flex-col space-y-3">
                    <x-shared.button data-cy="btn-add-to-cart" variant="outline" :rounded="false">
                        Add To Cart
                    </x-shared.button>
                    <x-shared.button data-cy="btn-checkout" variant="dark" :rounded="false">
                        Checkout
                    </x-shared.button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-16 max-w-4xl">
        <p  data-cy="product-sold" class="text-gray-500 mb-2">Terjual : 1.231.214</p>
        <h3 class="font-bold mb-2">Deskripsi</h3>
        <p  data-cy="product-description" class="text-gray-600 leading-relaxed mb-6">
            {{ $item->produk->deskripsi }}
        </p>

        <div class="flex gap-2 font-bold text-black uppercase tracking-wider text-sm">
            <span  data-cy="product-category">#{{ $item->kategori }}</span>
            <span  data-cy="product-type">#{{ $item->produk->jenis_produk }}</span>
        </div>
    </div>
</div>

<x-guest.katalog.modals.panduan-ukuran />

@endsection