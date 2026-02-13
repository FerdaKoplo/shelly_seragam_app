@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

<div class="flex justify-center my-6">

    <section class="max-w-7xl mx-auto px-4 py-8">
        {{-- Page Title & Back Button --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('home') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fa-solid fa-chevron-left text-xl"></i>
            </a>
            <h1 class="text-4xl font-black uppercase tracking-tight">Semua Katalog</h1>
        </div>

        {{-- Search and Filter Row --}}
        <div class="flex flex-col md:flex-row gap-4 mb-10 items-center">
            <div class="flex gap-2 w-full md:w-auto">
                <button class="p-3 border rounded-xl hover:bg-gray-50 shadow-sm">
                    <i class="fa-solid fa-sliders text-black"></i>
                </button>
                <div class="relative flex-1 md:w-80">
                    <input type="text" placeholder="Cari Produk"
                        class="w-full pl-4 pr-10 py-3 border rounded-xl shadow-sm focus:ring-2 focus:ring-secondary focus:border-transparent outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- Pagination Placeholder for Header
            <div class="ml-auto hidden md:block">
                <x-pagination />
            </div>
            --}}
        </div>

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-8">
            @foreach(range(1, 18) as $item)
            <x-cards.product-card.vertical
                name="Kemeja Kotak"
                category="#Kemeja #Katun #Formal"
                price="114.000" />
            @endforeach
        </div>

        {{-- Bottom Pagination 
        <div class="mt-12 flex justify-center md:justify-end">
            <x-pagination />
        </div>
        --}}
    </section>

    {{-- Floating Hubungi Kami Button --}}
    <div class="fixed bottom-6 right-6 z-50">

        <x-shared.button variant="outline" class="w-full md:w-auto py-4 px-4">
            <p class="font-medium text-xl">
                Hubungi Kami   <div class="ms-3 bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                </div>
            </p>


        </x-shared.button>


    </div>

</div>

@endsection