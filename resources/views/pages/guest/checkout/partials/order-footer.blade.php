<div class="space-y-8 mt-10">
    {{-- 1. Catatan Section --}}
    <div>
        <h3 class="font-bold text-xl mb-4">Catatan</h3>
        {{-- Kustom: Shows the specific requirement/static note --}}
        <div x-show="type === 'kustom'" class="text-gray-600 mb-4 italic">
            <p>Lorem Ipsum Dolor Sit aAmet</p>
        </div>
        {{-- Katalog: Shows the editable textarea --}}


        <textarea
            x-show="type === 'katalog'"
            x-model="notes"
            rows="6"
            class="w-full border border-gray-300 rounded-md p-4 focus:ring-1 focus:ring-black outline-none"
            placeholder="Tambahkan catatan untuk pesanan Anda...">
        </textarea>
    </div>

    {{-- 2. Ringkasan Pesanan (Calculations) --}}
    <div class="space-y-3">
        <h3 class="font-bold text-xl">Ringkasan Pesanan</h3>
        <div class="space-y-1">
            <div class="flex justify-between text-lg text-gray-600">
                <span>Subtotal:</span>
                <span x-text="formatCurrency(subtotal)"></span>
            </div>
            <div class="flex justify-between text-lg text-gray-600">
                <span>Ongkir:</span>
                <span x-text="formatCurrency(shippingCost)"></span>
            </div>
        </div>
    </div>

    {{-- 3. Final Total & Action --}}
    <div class="pt-4">
        <div class="text-right mb-6">
            {{-- Main Large Total --}}
            <h2 class="text-6xl font-black tracking-tighter" x-text="formatCurrency(total)"></h2>

            {{-- Kustom Specific Disclaimer --}}
            <p x-show="type === 'kustom'" class="text-gray-400 text-sm mt-2">
                *Harga estimasi. Admin akan menghubungi untuk konfirmasi.
            </p>
        </div>

        {{-- Payment Gateway Integration Button --}}
        <x-shared.button
            @click="submitOrder()"
            data-cy="btn-submit-checkout"
            variant="primary"
            :rounded="false"
            ::disabled="isSubmitting"
            class="w-full text-4xl py-4 bg-secondary text-black hover:bg-black hover:text-white transition-all font-bebas tracking-widest uppercase disabled:opacity-50">

            <div class="flex items-center justify-center gap-3">
                {{-- Spinner shown during gateway handshake --}}
                <template x-if="isSubmitting">
                    <svg class="animate-spin h-8 w-8 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>

                <span x-show="!isSubmitting && type === 'katalog'">Bayar</span>
                <span x-show="!isSubmitting && type === 'kustom'">Buat Pesanan</span>
                <span x-show="isSubmitting">Memproses...</span>
            </div>
        </x-shared.button>
    </div>
</div>
