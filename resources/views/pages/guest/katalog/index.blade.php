@extends('layouts.guest.layout')
@section('title', 'Katalog Seragam')
@section('content')

<div class="flex justify-center my-6">

    {{-- Product Catalog Searching and filtering --}}

    {{-- Product Catalog Preview --}}
    <section class="max-w-7xl mx-auto px-4">
        <!-- <div class="flex justify-between items-end mb-8">
        <h2 class="text-3xl font-bold uppercase tracking-tight">Katalog Produk</h2>
        <a href="{{ route('katalog') }}" class="text-yellow-600 font-semibold hover:underline">Lihat Semua Katalog</a>
    </div> -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- We will create a loop here later --}}
            @foreach(range(1, 9) as $item)
            <x-shared.product-card />
            @endforeach
        </div>

    </section>

    {{-- Product Catalog Pagination --}}
</div>

@endsection