{{-- USAGE
    <template x-for="(item, index) in items" :key="item.id">
                    <x-cards.product-card.cart-item/>
                </template>
    --}}

<div data-cy="cart-item" class="flex flex-col md:flex-row gap-6 border-b border-gray-100 pb-12 last:border-0 relative">

    <div class="w-full md:w-40">
        <div class="bg-gray-100 rounded-xl p-4 aspect-square flex items-center justify-center mb-2">
            <img :src="item.image_url || item.image || 'https://picsum.photos/id/1/600/800'" class="w-full object-contain" :alt="item.name">
        </div>
        <h3 data-cy="item-name" class="font-bold text-lg" x-text="item.name"></h3>
        <p data-cy="item-price" class="font-normal mt-1" x-text="formatCurrency(item.price)"></p>
        <form :action="'{{ url('/keranjang/remove') }}/' + item.katalog_id" method="POST" class="mt-3"
            onsubmit="return confirm('Hapus item ini dari keranjang?')">
            @csrf
            @method('DELETE')
            <button data-cy="remove-item-btn" type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                Hapus Item
            </button>
        </form>
    </div>

    <div class="flex-1 space-y-6">
        {{-- Selected Options --}}
        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700">
            <template x-if="item.size">
                <div><span class="font-bold">Ukuran:</span> <span x-text="item.size"></span></div>
            </template>
            <template x-if="item.color">
                <div><span class="font-bold">Warna:</span> <span x-text="item.color"></span></div>
            </template>
            <template x-if="item.mode === 'preorder'">
                <div class="text-amber-700 font-medium">Pre-Order</div>
            </template>
        </div>

        {{-- Quantity & Subtotal --}}
        <div class="flex justify-between items-end">
            <div class="mb-8">
                <span class="font-bold text-lg block mb-3">Quantity</span>

                <form :action="'{{ url('/keranjang/update') }}/' + item.katalog_id" method="POST" class="flex items-center border border-gray-300 w-28 rounded-md overflow-hidden bg-white">
                    @csrf
                    @method('PATCH')

                    <button
                        data-cy="decrement-btn"
                        type="submit"
                        name="action"
                        value="decrement"
                        class="px-3 py-1 text-xl hover:bg-gray-100 transition disabled:opacity-30"
                        :disabled="item.quantity <= 1">
                        −
                    </button>

                    <input data-cy="quantity-input" type="text" x-model="item.quantity"
                        class="w-full text-center border-none focus:ring-0 text-sm font-bold bg-transparent pointer-events-none"
                        readonly>

                    <button
                        data-cy="increment-btn"
                        type="submit"
                        name="action"
                        value="increment"
                        class="px-3 py-1 text-xl hover:bg-gray-100 transition">
                        +
                    </button>
                </form>
            </div>
            <div class="text-right">
                <p data-cy="item-subtotal" class="text-2xl font-bold" x-text="formatCurrency(item.price * item.quantity)"></p>
            </div>
        </div>
    </div>
</div>