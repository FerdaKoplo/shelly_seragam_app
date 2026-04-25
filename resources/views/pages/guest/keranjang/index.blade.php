@extends('layouts.guest.layout')
@section('title', 'Keranjang Saya')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl"
    x-data="{ 
        items: {{ Illuminate\Support\Js::from($items) }},
        notes: {{ Illuminate\Support\Js::from($notes) }},
        isSubmitting: false,
        noteSaveTimeout: null,
        noteSaveState: 'idle',

        shouldWarnOnLeave() {
            return this.items.length > 0 && !this.isSubmitting;
        },

        initLeaveGuard() {
            const handler = (event) => {
                if (!this.shouldWarnOnLeave()) {
                    return;
                }

                event.preventDefault();
                event.returnValue = '';
            };

            window.addEventListener('beforeunload', handler);
            this.$el.addEventListener('alpine:destroy', () => {
                window.removeEventListener('beforeunload', handler);
            }, { once: true });
        },

        queueSaveNotes() {
            if (this.noteSaveTimeout) {
                clearTimeout(this.noteSaveTimeout);
            }

            this.noteSaveState = 'saving';
            this.noteSaveTimeout = setTimeout(() => {
                this.saveNotes();
            }, 500);
        },

        async saveNotes() {
            try {
                const response = await fetch('{{ route('cart.notes.update') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ notes: this.notes }),
                });

                if (!response.ok) {
                    throw new Error('Failed to save notes');
                }

                this.noteSaveState = 'saved';
                setTimeout(() => {
                    if (this.noteSaveState === 'saved') {
                        this.noteSaveState = 'idle';
                    }
                }, 1200);
            } catch (error) {
                this.noteSaveState = 'error';
            }
        },

        get total() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        formatCurrency(num) {
            return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }"
    x-init="initLeaveGuard()"
    @submit.capture="isSubmitting = true">

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
            <template x-if="items.length > 0">
                <div class="mb-6 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Peringatan: item keranjang bisa hilang jika Anda meninggalkan aplikasi sebelum menyelesaikan checkout.
                </div>
            </template>
            <template x-if="items.length > 0">
                <form action="{{ route('cart.clear') }}" method="POST" class="mb-6"
                    onsubmit="return confirm('Kosongkan seluruh item di keranjang?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                        Kosongkan Keranjang
                    </button>
                </form>
            </template>
            <template x-if="items.length === 0">
                <div class="border rounded-xl p-6 text-gray-600">
                    Keranjang masih kosong. Tambahkan produk dari katalog.
                </div>
            </template>
            <template x-if="items.length > 0">
                <div class="space-y-12 overflow-y-auto max-h-[70vh] lg:max-h-[85vh] pr-4 no-scrollbar">
                    <template x-for="(item, index) in items" :key="item.id">
                        <x-cards.product-card.cart-item/>
                    </template>
                </div>
            </template>
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
                    @input="queueSaveNotes()"
                    class="w-full border-black rounded-xl h-40 focus:ring-black focus:border-black bg-slate-200 p-4"
                    placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                <p class="mt-2 text-xs text-gray-600" x-show="noteSaveState === 'saving'">Menyimpan catatan...</p>
                <p class="mt-2 text-xs text-green-700" x-show="noteSaveState === 'saved'">Catatan tersimpan.</p>
                <p class="mt-2 text-xs text-red-700" x-show="noteSaveState === 'error'">Gagal menyimpan catatan. Coba lagi.</p>
            </div>

            {{-- Final Summary & Action --}}
            <div class="space-y-6 pt-6">
                <div class="flex justify-end">
                    <p class="text-5xl font-black tracking-wider" x-text="formatCurrency(total)"></p>
                </div>

                <form action="{{ route('checkout') }}" method="POST" @submit="isSubmitting = true">
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
