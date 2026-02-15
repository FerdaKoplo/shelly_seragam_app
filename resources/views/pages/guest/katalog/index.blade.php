@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

<div  class="px-16 justify-center items-center my-6 max-w-full overflow-y-auto flex-grow">

    {{-- Page Title & Back Button --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('home') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i class="fa-solid fa-chevron-left text-xl"></i>
        </a>
        <h1 class="text-4xl font-bebas  tracking-widest">Semua Katalog</h1>
    </div>

    <div class="flex md:flex-row gap-4 mb-10 items-center">
        {{-- Search and Filter Row --}}
        <div x-data class="flex flex-1 gap-2 md:w-auto">

            <button @click="$dispatch('open-modal', 'modal-filter-produk-katalog')" class="p-3 border rounded-xl hover:bg-gray-50 shadow-sm transition-all">
                <i class="fa-solid fa-sliders text-black"></i>
            </button>


            <div class="flex items-center flex-1 md:w-80 border rounded-xl shadow-sm px-4 py-3 focus-within:ring-2 focus-within:ring-secondary">
                <input type="text" placeholder="Cari Produk"
                    class="flex-1 bg-transparent outline-none text-gray-700">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>

            </div>

        </div>

        {{-- Pagination Placeholder for Header--}}
        <div class="ml-auto hidden md:block ">
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-50 disabled:opacity-50">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>

                <button class="w-10 h-10 flex items-center justify-center border rounded-lg bg-gray-50 font-bold">1</button>
                <button class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-50">2</button>
                <button class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-50">3</button>
                <span class="px-2 text-gray-400">....</span>
                <button class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-50">10</button>

                <button class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Product Grid --}}

    @php
    $isEmpty = false;
    @endphp
    @if (!$isEmpty)
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-8">
        @foreach(range(1, 30) as $item)
        <x-cards.product-card.vertical
            name="Kemeja Kotak"
            category="#Kemeja #Katun #Formal"
            price="114.000" />
        @endforeach
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
                    <i class=" fa-solid fa-pen"></i>
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

<x-guest.katalog.modals.filter-produk-katalog/>

@endsection