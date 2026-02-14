<x-shared.modal_base name="modal-size-variation" title="Variasi Ukuran" maxWidth="2xl">
    <div x-data="{
        presets: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        selectedPreset: 'L', // Default selected
        sizeName: 'L',
        sleeveLength: '88.9',
        chestWidth: '69.9',
        
        selectPreset(preset) {
            this.selectedPreset = preset;
            this.sizeName = preset;
        },

        clearName() {
            this.sizeName = '';
            this.selectedPreset = null;
        },
        
        submit() {
            if(!this.sizeName) return;

            this.$dispatch('add-size', {
                name: this.sizeName,
                sleeve: this.sleeveLength,
                chest: this.chestWidth
            });

            this.closeModal(); 
            
            this.clearName();
        }
    }" class="px-1 pb-2">

        <h3 class="font-bold text-lg mb-3">Ukuran</h3>
        <div class="grid grid-cols-3 gap-3 mb-6">
            <template x-for="preset in presets" :key="preset">
                <button type="button" @click="selectPreset(preset)"
                    class="py-2.5 border rounded-md text-sm font-medium transition" :class="selectedPreset === preset 
                        ? 'bg-[#323232] text-white border-[#323232]' 
                        : 'bg-white text-gray-900 border-black hover:bg-gray-50'" x-text="preset"></button>
            </template>
        </div>

        <div class="mb-5">
            <button type="button" class="flex items-center gap-2 text-xs font-medium text-[#323232] hover:underline">
                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_936_3015)">
                        <path
                            d="M18.375 5.25H2.625C1.6625 5.25 0.875 6.0375 0.875 7V14C0.875 14.9625 1.6625 15.75 2.625 15.75H18.375C19.3375 15.75 20.125 14.9625 20.125 14V7C20.125 6.0375 19.3375 5.25 18.375 5.25ZM18.375 14H2.625V7H4.375V10.5H6.125V7H7.875V10.5H9.625V7H11.375V10.5H13.125V7H14.875V10.5H16.625V7H18.375V14Z"
                            fill="#323232" />
                    </g>
                    <defs>
                        <clipPath id="clip0_936_3015">
                            <rect width="21" height="21" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
                Panduan Ukuran
            </button>
        </div>

        <div class="space-y-4">

            <div>
                <div class="flex justify-between mb-1.5">
                    <label class="text-sm font-medium text-gray-900">Nama Ukuran</label>
                    <button type="button" @click="clearName()"
                        class="text-xs text-gray-500 hover:text-black">clear</button>
                </div>
                <input type="text" x-model="sizeName"
                    class="w-full border border-black rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1.5">Panjang Lengan</label>
                <div class="flex gap-2">
                    <input type="text" x-model="sleeveLength"
                        class="flex-1 border border-black rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <div
                        class="border border-black rounded-md px-3 py-2.5 text-sm font-medium flex items-center justify-center bg-white min-w-[3.5rem]">
                        CM
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1.5">Lebar Dada</label>
                <div class="flex gap-2">
                    <input type="text" x-model="chestWidth"
                        class="flex-1 border border-black rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <div
                        class="border border-black rounded-md px-3 py-2.5 text-sm font-medium flex items-center justify-center bg-white min-w-[3.5rem]">
                        CM
                    </div>
                </div>
            </div>

            <button type="button"
                class="w-full border border-dashed border-black rounded-md py-3 text-sm font-medium text-gray-900 hover:bg-gray-50">
                Pengukuran Lainnya
            </button>

        </div>

        <div class="mt-8">
            <button type="button" @click="submit()"
                class="w-full bg-[#323232] text-white py-3 rounded-md text-sm font-medium hover:opacity-90 transition">
                Simpan
            </button>
        </div>

    </div>
</x-shared.modal_base>