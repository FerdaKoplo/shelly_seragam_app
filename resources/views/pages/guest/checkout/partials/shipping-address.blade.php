<div class="space-y-4">
    <h2 class="text-xl font-bold">Alamat Pengiriman</h2>
    
    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
            <input type="text" name="address" placeholder="Alamat Jalan"
                x-model="address.address" @input="delete errors.address"
                class="w-full bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black transition">
            <p x-show="errors.address" x-text="errors.address" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div class="col-span-2">
            <div class="relative">
                <input
                    type="text"
                    name="city"
                    placeholder="Kota / Kecamatan"
                    x-model="destinationQuery"
                    @input="delete errors.city; searchDestinations()"
                    @focus="searchDestinations()"
                    autocomplete="off"
                    class="w-full bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black transition">

                <div
                    x-show="destinationResults.length > 0"
                    class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-h-64 overflow-y-auto">
                    <template x-for="dest in destinationResults" :key="dest.id || dest.destination_id || dest.label">
                        <button
                            type="button"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50"
                            @click="selectDestination(dest)">
                            <div class="text-sm font-semibold" x-text="dest.label || dest.name || dest.city_name || dest.subdistrict_name || '-'"></div>
                            <div class="text-xs text-gray-500" x-text="dest.province_name || dest.province || ''"></div>
                        </button>
                    </template>
                </div>
            </div>
            <p x-show="errors.city" x-text="errors.city" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <input type="text" name="province" placeholder="Provinsi"
                x-model="address.province" @input="delete errors.province"
                class="w-full bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black">
            <p x-show="errors.province" x-text="errors.province" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <input type="text" name="postal_code" placeholder="Kode Pos"
                x-model="address.postal_code" @input="delete errors.postal_code"
                class="w-full bg-gray-100 border-none rounded-xl p-4 focus:ring-2 focus:ring-black">
            <p x-show="errors.postal_code" x-text="errors.postal_code" class="mt-1 text-sm text-red-600"></p>
        </div>
    </div>

    {{-- Dynamic Shipping Selector --}}
    <div class="space-y-3 pt-2">
        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Opsi Pengiriman</label>
        <div x-show="!isAddressValid" class="text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-4">
            Lengkapi alamat pengiriman yang valid untuk melihat opsi pengiriman, durasi, dan ongkir.
        </div>

        <div x-show="isAddressValid" class="flex flex-wrap gap-3">
            <template x-for="option in shippingOptions" :key="option.id">
                <button
                    type="button"
                    @click="selectShipping(option)"
                    :class="shippingMethod === option.id ? 'border-black bg-black text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-400'"
                    class="flex-1 min-w-[160px] border-2 py-4 px-6 rounded-xl text-sm font-bold transition-all duration-200 flex flex-col items-center gap-1">

                    <span x-text="option.label"></span>
                    <span class="opacity-80 font-normal" x-text="option.duration || ''"></span>
                    <span class="opacity-80 font-normal" x-text="formatCurrency(option.price)"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Hidden input to ensure the selected method is sent to the server --}}
    <input type="hidden" name="shipping_id" :value="shippingMethod">
    <input type="hidden" name="destination_id" :value="destinationId">
    <p x-show="errors.shipping_id" x-text="errors.shipping_id" class="text-sm text-red-600"></p>
</div>
