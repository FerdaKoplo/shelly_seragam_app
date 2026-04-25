{{-- USAGE
    <template x-for="(item, index) in items" :key="item.id">
                    <x-cards.product-card.cart-item/>
                </template>
    --}}
    
<div class="flex flex-col md:flex-row gap-6 border-b border-gray-100 pb-12 last:border-0 relative">

    <div class="w-full md:w-40">
        <div class="bg-gray-100 rounded-xl p-4 aspect-square flex items-center justify-center mb-2">
            <img :src="item.image_url || item.image || 'https://picsum.photos/id/1/600/800'" class="w-full object-contain" :alt="item.name">
        </div>
        <h3 class="font-bold text-lg" x-text="item.name"></h3>
        <p class="font-normal mt-1" x-text="formatCurrency(item.price)"></p>
    </div>

    <div class="flex-1 space-y-6">
        {{-- Size Selector --}}
        <div>
            <div class="flex justify-between items-center mb-3">
                <span class="font-bold text-lg">Ukuran</span>
                <button type="button" @click="$dispatch('open-modal', 'modal-panduan-ukuran')" class="text-xs flex items-center text-gray-500 hover:text-black">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1"></path>
                    </svg>
                    Panduan Ukuran
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="sz in ['XS', 'S', 'M', 'L', 'XL', 'XXL']">

                    <label class="cursor-pointer">
                        <input type="radio"
                            :name="'size-' + item.id"
                            :value="sz"
                            x-model="item.size"
                            class="hidden peer">
                        <div class="border border-gray-200 py-2 text-center rounded-lg peer-checked:bg-black peer-checked:text-white transition" x-text="sz"></div>
                    </label>
                </template>
            </div>
        </div>

        {{-- Quantity & Subtotal --}}
        <div class="flex justify-between items-end">
            <x-shared.quantity-button
                model="item.quantity"
                :min="1" />
            <div class="text-right">
                <p class="text-2xl font-bold" x-text="formatCurrency(item.price * item.quantity)"></p>
            </div>
        </div>
    </div>
</div>
