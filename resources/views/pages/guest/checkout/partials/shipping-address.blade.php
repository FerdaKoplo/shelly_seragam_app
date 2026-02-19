<div class="space-y-4">
    <h2 class="text-xl font-bold">Alamat Pengiriman</h2>
    
    <div class="grid grid-cols-2 gap-3">
        <input type="text" name="address" placeholder="Alamat Jalan" class="col-span-2 bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black transition">
        <input type="text" name="city" placeholder="Kota" class="col-span-2 bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black">
        <input type="text" name="province" placeholder="Provinsi" class="bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black">
        <input type="text" name="postal_code" placeholder="Kode Pos" class="bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black">
    </div>

    {{-- Dynamic Shipping Selector --}}
    <div class="space-y-3 pt-2">
        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Opsi Pengiriman</label>
        <div class="flex flex-wrap gap-3">
            <template x-for="option in shippingOptions" :key="option.id">
                <button 
                    type="button" 
                    @click="selectShipping(option)"
                    :class="shippingMethod === option.id ? 'border-black bg-black text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-400'"
                    class="flex-1 min-w-[140px] border-2 py-4 px-6 rounded-xl text-sm font-bold transition-all duration-200 flex flex-col items-center gap-1">
                    
                    <span x-text="option.label"></span>
                    <span class="opacity-80 font-normal" x-text="formatCurrency(option.price)"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Hidden input to ensure the selected method is sent to the server --}}
    <input type="hidden" name="shipping_id" :value="shippingMethod">
</div>