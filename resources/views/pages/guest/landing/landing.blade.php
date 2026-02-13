@extends('layouts.guest.layout')
@section('title', 'Shelly Seragam')
@section('content')

{{-- 1. Hero Section --}}

<div class=" flex w-full bg-orange-400 text-orange-400 text-[4px]"> Cina </div>
@include('pages.guest.landing.partials.hero')
<div class=" flex w-full bg-black text-[6px]"> Nigga </div>

{{-- 2. Stats Banner --}}
@include('pages.guest.landing.partials.stats')

<div class="flex justify-center -mt-6 relative z-20 text-[24px]">
    <x-shared.button variant="outline" class="bg-white px-40 py-3 shadow-lg flex items-center gap-2" href="https://wa.me/081214747968">
        Hubungi Kami <span class="text-green-500 text-xl"><i class="fa-brands fa-whatsapp fa-xl px-2"></i></span>
    </x-shared.button>
</div>

{{-- 3. Product Catalog Preview --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-end mb-8">
        <h2 class="text-3xl font-bold uppercase tracking-tight">Katalog Produk</h2>
        <a href="{{ route('katalog') }}" class="text-yellow-600 font-semibold hover:underline">Lihat Semua Katalog</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- We will create a loop here later --}}
        @foreach(range(1, 9) as $item)
        <x-shared.product-card />
        @endforeach
    </div>

</section>

<div class="mt-16 flex flex-col items-center">
    <p class="text-gray-600 mb-6 font-medium text-[20px]">Tidak menemukan yang kamu cari?</p>

    <div class="w-full flex items-center gap-0">
        <div class="flex-1 h-4 bg-[#F3C344]"></div>
        <x-shared.button href="{{ route('kustom') }}" variant="outline" class="w-full md:w-auto py-4 px-20">
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
@include('pages.guest.landing.partials.location')
@endsection