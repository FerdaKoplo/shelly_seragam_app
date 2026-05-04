@extends('layouts.guest.layout')
@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl"
    x-cloak
    x-init="init()"
    x-data="checkoutPage({{ Js::from([
        'type' => $type,
        'items' => $items,
        'customData' => $customData,
        'shippingOptions' => $shippingOptions,
        'shippingMethod' => old('shipping_id'),
        'notes' => old('notes', $checkoutNotes),
        'customer' => [
            'full_name' => old('full_name', ''),
            'email' => old('email', ''),
            'phone' => old('phone', ''),
        ],
        'address' => [
            'address' => old('address', ''),
            'city' => old('city', ''),
            'province' => old('province', ''),
            'postal_code' => old('postal_code', ''),
        ],
        'errors' => $errors->toArray(),
    ]) }})">

    <form
        x-ref="checkoutForm"
        method="POST"
        action="{{ route('checkout') }}"
        @submit.prevent="submitOrder()">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

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

<script>
    function checkoutPage(initial) {
        return {
            type: initial.type,
            items: initial.items || [],
            customData: initial.customData || null,
            shippingOptions: initial.shippingOptions || [],
            isSubmitting: false,
            isConfirming: false,
            shippingMethod: initial.shippingMethod || null,
            shippingCost: 0,
            notes: initial.notes || "",
            customer: initial.customer || { full_name: "", email: "", phone: "" },
            address: initial.address || { address: "", city: "", province: "", postal_code: "" },
            errors: initial.errors || {},

            init() {
                this.flattenErrors();
                if (this.shippingMethod) {
                    const option = this.shippingOptions.find((o) => o.id === this.shippingMethod);
                    if (option) this.selectShipping(option);
                }
            },

            flattenErrors() {
                for (const key in this.errors) {
                    if (Array.isArray(this.errors[key])) {
                        this.errors[key] = this.errors[key][0];
                    }
                }
            },

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

            validateCheckout() {
                const errors = {};

                const fullName = (this.customer.full_name || "").trim();
                const email = (this.customer.email || "").trim();
                const phone = (this.customer.phone || "").trim();

                const address = (this.address.address || "").trim();
                const city = (this.address.city || "").trim();
                const province = (this.address.province || "").trim();
                const postal = (this.address.postal_code || "").trim();

                if (!fullName) errors.full_name = "Nama lengkap wajib diisi.";
                if (!email) errors.email = "Email wajib diisi.";
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = "Format email tidak valid.";

                if (!phone) errors.phone = "Nomor telepon wajib diisi.";
                else if (!/^[0-9+\-\s]{8,20}$/.test(phone)) errors.phone = "Nomor telepon tidak valid.";

                if (!address) errors.address = "Alamat jalan wajib diisi.";
                if (!city) errors.city = "Kota wajib diisi.";
                if (!province) errors.province = "Provinsi wajib diisi.";
                if (!postal) errors.postal_code = "Kode pos wajib diisi.";
                else if (!/^[0-9]{4,6}$/.test(postal)) errors.postal_code = "Kode pos tidak valid.";

                if (!this.shippingMethod) errors.shipping_id = "Pilih opsi pengiriman.";

                this.errors = errors;
                return Object.keys(errors).length === 0;
            },

            async submitOrder() {
                if (!this.validateCheckout()) return;
                if (this.isSubmitting || this.isConfirming) return;

                this.isConfirming = true;
                const confirmed = window.confirm("Apakah data yang dimasukkan sudah benar?");
                this.isConfirming = false;
                if (!confirmed) return;

                this.isSubmitting = true;
                this.$refs.checkoutForm.submit();
            }
        };
    }
</script>
    </form>
@endsection
