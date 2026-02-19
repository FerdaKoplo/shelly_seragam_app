@extends('layouts.guest.layout')
@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl"
    x-cloak
    x-data='{ 
        type: "{{ $type }}", 
        items: @json($items),
        customData: @json($customData),
        shippingOptions: @json($shippingOptions),
        isSubmitting: false,
        shippingMethod: null,
        shippingCost: 0,
        notes: "",

        selectShipping(option) {
            this.shippingMethod = option.id;
            this.shippingCost = option.price;
        },

        get subtotal() {
            if (this.type === "katalog") {
                return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            }
            return this.customData ? this.customData.price : 0;
        },

        get total() { 
            return this.subtotal + this.shippingCost; 
        },

        formatCurrency(num) {
            return "Rp" + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        async submitOrder() {
            if(!this.shippingMethod) return alert("Silahkan pilih opsi pengiriman");
            this.isSubmitting = true;
        }
    }'>

    {{-- Header with Back Button --}}
    <div class="flex items-center gap-4 mb-12">
        <a href="javascript:history.back()" class="hover:opacity-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-6xl font-bebas tracking-widest uppercase">Checkout</h1>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

        {{-- LEFT COLUMN: Forms --}}
        <div class="lg:col-span-6 space-y-12">
            @include('pages.guest.checkout.partials.customer-info')
            @include('pages.guest.checkout.partials.shipping-address')

            {{-- Only show Midtrans box for Katalog/Standard checkout --}}
            <div x-show="type === 'katalog'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                @include('pages.guest.checkout.partials.payment-method')
            </div>
        </div>

        {{-- RIGHT COLUMN: Order Content & Summary --}}
        <div class="lg:col-span-6 space-y-10">

            {{-- Product Items (For Katalog) --}}
            <template x-if="type === 'katalog'">
                <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 no-scrollbar">
                    <template x-for="(item, index) in items" :key="item.id">
                        <x-cards.product-card.cart-item />
                    </template>
                </div>
            </template>

            {{-- Custom Card (For Kustom) --}}
            <template x-if="type === 'kustom'">
                @include('pages.guest.checkout.partials.custom-summary')
            </template>

            {{-- Summary & Totals --}}
            <div class="pt-6 border-t border-gray-100">
                @include('pages.guest.checkout.partials.order-footer')
            </div>
        </div>
    </div>
</div>

<x-guest.katalog.modals.panduan-ukuran />
@endsection