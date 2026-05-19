@extends('layouts.guest.layout')
@section('title', 'Shelly Seragam')
@section('content')

{{-- 1. Hero Section --}}

{{-- <div class=" flex w-full bg-orange-400 text-orange-400 text-[4px]"> Cina </div> --}}
<div class="w-full h-2 bg-orange-400"></div>
<div data-cy="hero-section">
@include('pages.guest.landing.partials.hero')
</div>
{{-- <div class=" flex w-full bg-black text-[6px]"> Nigga </div> --}}


{{-- 2. Stats Banner --}}
<div data-cy="stats-section">
@include('pages.guest.landing.partials.stats')
</div>

<div class="flex justify-center -mt-6 relative z-20 px-4">
    <x-shared.button variant="outline"
        data-cy="btn-whatsapp"
        class="bg-white w-full sm:w-auto sm:min-w-[320px] px-8 py-3 shadow-lg flex items-center justify-center gap-2"
        href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo%20Shelly%20Seragam%2C%20saya%20ingin%20konsultasi%2Fbikin%20seragam."
        {{-- href="https://wa.me/6287893385014?text=Halo%20Shelly%20Seragam%2C%20saya%20ingin%20konsultasi%2Fbikin%20seragam." --}}
        target="_blank" rel="noopener noreferrer" aria-label="Hubungi Shelly Seragam via WhatsApp">
        Hubungi Kami
        <span class="text-green-500 text-xl">
            <i class="fa-brands fa-whatsapp fa-xl px-2" aria-hidden="true"></i>
        </span>
    </x-shared.button>
</div>

{{-- 3. Product Catalog Preview --}}
<section class="max-w-7xl mx-auto px-4" data-cy="section-katalog">
    <div class="flex justify-between items-end mb-8">
        <h2 class="text-3xl font-bold uppercase tracking-tight">Katalog Produk</h2>
        <a href="{{ route('katalog') }}" class="text-yellow-600 font-semibold hover:underline">Lihat Semua Katalog</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($katalogTerbaru as $item)
        @php
        $image = optional($item->fotos->first())->path ?? 'placeholder.jpg';
        @endphp
        <a href="{{ route('product.show', $item->katalog_id) }}"  data-cy="product-item">
            <x-cards.product-card.horizontal      data-cy="product-card" :name="$item->produk->nama_produk" :price="number_format($item->harga, 0, ',', '.')" image="placeholder.jpg" />
        </a>
        @empty
        <p class="text-gray-500">Belum ada katalog.</p>
        @endforelse

    </div>

</section>

<div class="mt-16 flex flex-col items-center">
    <p class="text-gray-600 mb-6 font-medium text-[20px]">Tidak menemukan yang kamu cari?</p>

    <div class="w-full flex items-center gap-0">
        <div class="flex-1 h-4 bg-[#F3C344]"></div>
        <x-shared.button href="{{ route('kustom') }}" variant="outline" class="w-full md:w-auto py-4 px-20"
        data-cy="btn-custom-uniform">
            <span class="text-xl font-bebas text-[28px] tracking-widest gap-4">
                <i class="fa-solid fa-shirt"></i>
                Buat Seragammu Sendiri!
                <i class=" fa-solid fa-pen"></i>
            </span>
        </x-shared.button>


        <div class="flex-1 h-6 bg-[#F3C344]"></div>
    </div>
</div>

{{-- 4. Location Section --}}
<div data-cy="location-section">
@include('pages.guest.landing.partials.location')
</div>
@endsection