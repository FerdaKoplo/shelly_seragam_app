@extends('layouts.guest.layout')
@section('title', 'Kustomisasi Seragam')
@section('content')

<div
    class="lg:w-[90%] mx-auto px-4 py-8"
    x-data="{
        category: 'bundle',
        selectedSize: 'M',
        quantity: 1,

        // subtotal per section (per pcs) — diupdate oleh partial via event section-estimate
        sectionTotals: { atasan: 0, bawahan: 0 },

        // dummy fallback kalau event belum pernah ke-dispatch
        dummyBase: { atasan: 120000, bawahan: 110000 },

        formatCurrency(num) {
            num = Number(num || 0);
            return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        get estimatePerPcs() {
            const atasan = this.sectionTotals.atasan || this.dummyBase.atasan;
            const bawahan = this.sectionTotals.bawahan || this.dummyBase.bawahan;

            if (this.category === 'atasan') return atasan;
            if (this.category === 'bawahan') return bawahan;

            // bundle
            return atasan + bawahan;
        },

        get estimateTotal() {
            const qty = Number(this.quantity) || 1;
            return this.estimatePerPcs * qty;
        }
    }"
    x-on:section-estimate.window="
        sectionTotals[$event.detail.prefix] = Number($event.detail.total || 0)
    "
>
    <div class="flex items-center gap-4 mb-8">
        <a href="javascript:history.back()" class="text-2xl font-bold">
            <i class="fas fa-chevron-left text-lg"></i>
        </a>
        <h1 class="text-3xl font-black uppercase tracking-tighter">Produk Kustom</h1>
    </div>

    <div class="flex gap-3 mb-10">
        <x-shared.button
            @click="category = 'bundle'"
            ::class="category === 'bundle' ? 'bg-black text-white' : 'bg-white text-black border-gray-200 hover:bg-gray-100'">
            Bundle
        </x-shared.button>

        <x-shared.button
            @click="category = 'atasan'"
            ::class="category === 'atasan' ? 'bg-black text-white' : 'bg-white text-black border-gray-200 hover:bg-gray-100'">
            Atasan
        </x-shared.button>

        <x-shared.button
            @click="category = 'bawahan'"
            ::class="category === 'bawahan' ? 'bg-black text-white' : 'bg-white text-black border-gray-200 hover:bg-gray-100'">
            Bawahan
        </x-shared.button>
    </div>

    <form action="{{ route('checkout') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Hidden inputs to capture global Alpine state --}}
        <input type="hidden" name="type" value="kustom">
        <input type="hidden" name="category" :value="category">
        <input type="hidden" name="size" :value="selectedSize">
        <input type="hidden" name="total_quantity" :value="quantity">

        {{-- Estimation values (dummy) --}}
        <input type="hidden" name="estimated_per_pcs" :value="estimatePerPcs">
        <input type="hidden" name="estimated_total" :value="estimateTotal">

        <div x-show="category === 'bundle' || category === 'atasan'">
            @include('pages.guest.kustom.partials.section_config', ['title' => 'Section Atasan', 'prefix' => 'atasan'])
        </div>

        <div x-show="category === 'bundle' || category === 'bawahan'" class="mt-12">
            @include('pages.guest.kustom.partials.section_config', ['title' => 'Section Bawahan', 'prefix' => 'bawahan'])
        </div>

        <hr class="my-12 border-gray-300">

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium mb-2">Catatan</label>
                <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-md p-4 focus:ring-1 focus:ring-black outline-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">* Upload Design, Badge & keperluan lainnya</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center flex flex-col items-center justify-center bg-gray-50/50">
                    <i class="fas fa-cloud-upload-alt text-2xl mb-2 text-gray-400"></i>
                    <p class="text-sm text-gray-500 font-medium">Choose a file or drag & drop it here</p>
                    <p class="text-xs text-gray-400 mt-1 mb-4">SVG, JPEG, PNG formats, up to 10MB</p>
                    <label class="cursor-pointer bg-white border border-gray-300 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                        Browse File
                        <input type="file" name="design_file" class="hidden">
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-16 flex flex-wrap items-end justify-between gap-8">
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-lg font-bold">Ukuran</label>
                        <button type="button" class="text-xs text-gray-400 flex items-center gap-1">
                            <i class="fas fa-ruler-horizontal"></i> Panduan Ukuran
                        </button>
                    </div>
                    <div class="flex gap-2">
                        @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <x-guest.katalog.size-selector :label="$size" :id="$size" class="w-16" />
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="text-lg font-bold block mb-3">Quantity</label>
                    <div class="inline-flex items-center border border-black rounded-md px-3 py-1">
                        <button type="button" @click="if(quantity > 1) quantity--" class="px-2 font-bold">-</button>

                        <input
                            type="number"
                            min="1"
                            x-model.number="quantity"
                            class="w-12 text-center font-bold border-none focus:ring-0"
                        >

                        <button type="button" @click="quantity++" class="px-2 font-bold">+</button>
                    </div>
                </div>
            </div>

            <div class="text-right flex-1 sm:flex-initial">
                <div class="text-4xl font-bold mb-1" x-text="formatCurrency(estimateTotal)"></div>
                <p class="text-xs text-gray-400 mb-6">*Harga estimasi. Admin akan menghubungi untuk konfirmasi.</p>

                <x-shared.button type="submit" variant="primary" :rounded="false" class="w-full text-4xl py-4 bg-secondary text-black hover:bg-black hover:text-white transition-all font-bebas tracking-widest uppercase disabled:opacity-50">
                    CHECKOUT
                </x-shared.button>
            </div>
        </div>
    </form>
</div>

@endsection