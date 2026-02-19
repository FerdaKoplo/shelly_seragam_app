@extends('layouts.guest.layout')
@section('title', 'Keranjang Saya')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl"
    x-data="{ 
        {{-- Mock Data: Easily replaceable with $cartItems->toJson() --}}
        items: [
            { id: 1, name: 'Kemeja Kotak', price: 114000, quantity: 2, size: 'S', image: 'product-1.png' },
            { id: 2, name: 'Kemeja Kotak', price: 114000, quantity: 2, size: 'S', image: 'product-1.png' },
            { id: 3, name: 'Kemeja Kotak', price: 114000, quantity: 2, size: 'S', image: 'product-1.png' }
        ],
        notes: '',
        
        {{-- Helper to calculate total --}}
        get total() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        {{-- Format IDR Currency --}}
        formatCurrency(num) {
            return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }">

    {{-- Header Section --}}
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('katalog') }}" class="text-2xl hover:opacity-70 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-4xl font-bebas tracking-widest uppercase">Keranjang Saya</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        {{-- Left Column: Cart Items --}}
        <div class="lg:col-span-6">
            <div class="space-y-12 overflow-y-auto max-h-[70vh] lg:max-h-[85vh] pr-4 no-scrollbar">
                <template x-for="(item, index) in items" :key="item.id">
                    <x-cards.product-card.cart-item/>
                </template>
            </div>
        </div>
        {{-- Right Column: Recommendations & Checkout --}}
        <div class="lg:col-span-6 space-y-10">

            {{-- Recommendations Section --}}
            <div class="w-full">
                <h2 class="text-xl font-bold mb-4">Cek Produk Lainnya</h2>

                {{-- Flex container with overflow and snapping --}}
                <div class="flex overflow-x-auto gap-4 pb-4 snap-x snap-mandatory no-scrollbar">
                    @php
                    // Mocking the collection for the loop - easily replaceable with backend data
                    $recommendations = [
                    ['name' => 'Kemeja Kotak', 'price' => '114.000', 'img' => 'product-1.png'],
                    ['name' => 'Kemeja Kotak Red', 'price' => '114.000', 'img' => 'product-1.png'],
                    ['name' => 'Kemeja Kotak Blue', 'price' => '114.000', 'img' => 'product-1.png'],
                    ['name' => 'Kemeja Flanel', 'price' => '125.000', 'img' => 'product-1.png'],
                    ];
                    @endphp

                    @foreach($recommendations as $product)
                    <div class="min-w-[280px] md:min-w-[320px] lg:min-w-[350px] snap-start">
                        <x-cards.product-card.horizontal
                            :name="$product['name']"
                            :price="$product['price']"
                            :image="$product['img']" />
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Notes Section --}}
            <div>
                <label class="text-xl font-bold mb-4 block">Catatan</label>
                <textarea
                    x-model="notes"
                    class="w-full border-black rounded-xl h-40 focus:ring-black focus:border-black bg-slate-200 p-4"
                    placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
            </div>

            {{-- Final Summary & Action --}}
            <div class="space-y-6 pt-6">
                <div class="flex justify-end">
                    <p class="text-5xl font-black tracking-wider" x-text="formatCurrency(total)"></p>
                </div>

                <form action="{{route('checkout')}}" method="#">
                    @csrf
                    {{-- Hidden inputs to send Alpine state to Backend --}}
                    <input type="hidden" name="cart_data" :value="JSON.stringify(items)">
                    <input type="hidden" name="notes" :value="notes">

                    <x-shared.button
                        type="submit"
                        variant="primary"
                        :rounded="false"
                        class="text-2xl py-6 text-white bg-black hover:bg-secondary hover:text-black tracking-widest">
                        Checkout
                    </x-shared.button>
                </form>
            </div>
        </div>

    </div>
</div>

<x-guest.katalog.modals.panduan-ukuran />
@endsection