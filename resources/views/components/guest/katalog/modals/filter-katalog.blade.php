<x-shared.modal_base name="modal-filter-katalog" title="Filter Produk" maxWidth="3xl">
    <div x-data="{ 
        filters: [],
        toggleFilter(id) {
            if (this.filters.includes(id)) {
                this.filters = this.filters.filter(f => f !== id);
            } else {
                this.filters.push(id);
            }
        },
        resetFilters() {
            this.filters = [];
        },
        applyFilters() {
            console.log('Applying filters:', this.filters);
            // You can redirect here: window.location.href = '/katalog?filter=' + this.filters.join(',');
            this.closeModal();
        },
        
    }">
        <div class="space-y-6 text-left">

            {{-- Pakaian Untuk Section --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Pakaian Untuk</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Pria" id="pria" />
                    <x-guest.katalog.filter-button label="Wanita" id="wanita" />
                </div>
            </div>

            {{-- Kategori Section --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Kategori</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Semua" id="kategori-semua" />
                    <x-guest.katalog.filter-button label="Seragam" id="seragam" />
                    <x-guest.katalog.filter-button label="Formal" id="formal" />
                    <x-guest.katalog.filter-button label="Atasan" id="atasan" />
                    <x-guest.katalog.filter-button label="Jas" id="jas" />
                    <x-guest.katalog.filter-button label="Batik" id="batik" />
                    <x-guest.katalog.filter-button label="Setelan" id="setelan" />
                    <button class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Material Kain Section --}}
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">Material Kain</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <x-guest.katalog.filter-button label="Semua" id="material-semua" />
                        <x-guest.katalog.filter-button label="Katun" id="katun" />
                        <x-guest.katalog.filter-button label="Woll" id="woll" />
                        <x-guest.katalog.filter-button label="Kargo" id="kargo" />
                        <x-guest.katalog.filter-button label="Resmi" id="resmi" />
                        <x-guest.katalog.filter-button label="Batik" id="material-batik" />
                    </div>
                </div>

                {{-- Ukuran Section --}}
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">Ukuran</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <x-guest.katalog.filter-button label="All Size" id="size-semua" />
                        <x-guest.katalog.filter-button label="S" id="size-s" />
                        <x-guest.katalog.filter-button label="M" id="size-m" />
                        <x-guest.katalog.filter-button label="L" id="size-l" />
                        <x-guest.katalog.filter-button label="XL" id="size-xl" />
                        <x-guest.katalog.filter-button label="XXL" id="size-xxl" />
                    </div>
                </div>
            </div>

            {{-- Urutkan Section --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Urutkan</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Paling Relevant" id="relevan" />
                    <x-guest.katalog.filter-button label="Harga Tertinggi" id="highest-price" />
                    <x-guest.katalog.filter-button label="Harga Terendah" id="lowest-price" />
                    <x-guest.katalog.filter-button label="Terbaru" id="newest" />
                    <x-guest.katalog.filter-button label="Paling Laris" id="best-selling" />
                </div>
            </div>
        </div>

        <div x-show="filters.length > 0" class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Filter Terpilih:</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="filterId in filters" :key="filterId">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full border border-gray-200">
                        <span x-text="filterId"></span>
                        <button @click="toggleFilter(filterId)" class="hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-xmark ml-1"></i>
                        </button>
                    </span>
                </template>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <div class="flex justify-end gap-3 w-full">
                <button @click="resetFilters()" class="px-8 py-2 font-bold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Hapus Filter
                </button>
                <button @click="applyFilters()"
                    class="px-8 py-2 font-bold text-white bg-[#333333] rounded-md hover:bg-black transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>
</x-shared.modal_base>