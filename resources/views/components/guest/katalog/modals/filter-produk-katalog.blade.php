<x-shared.modal_base name="modal-filter-produk-katalog" data-cy="modal-filter" title="Filter Produk" maxWidth="3xl">
    <div x-data="{
            filters: [],
            groups: {
                gender: ['pria', 'wanita'],
                kategori: ['kategori-semua','seragam','formal','atasan','jas','batik','setelan'],
                ukuran: ['size-semua','size-s','size-m','size-l','size-xl','size-xxl'],
                material: ['material-semua', 'katun', 'woll', 'kargo', 'resmi', 'material-batik'],
                sort: ['relevan','highest-price','lowest-price','newest','best-selling'],
                stok: ['stok-ready','stok-empty'],
            },

            toggleFilter(id) {
                for (const [groupName, ids] of Object.entries(this.groups)) {
                    if (ids.includes(id)) {
                        this.filters = this.filters.filter(f => !ids.includes(f));
                        break;
                    }
                }

                if (this.filters.includes(id)) {
                    this.filters = this.filters.filter(f => f !== id);
                } else {
                    this.filters.push(id);
                }
            },

            resetFilters() {
                const url = new URL(window.location.href);
                const keep = new URLSearchParams();
                ['search','min_harga','max_harga'].forEach((k) => {
                    const v = url.searchParams.get(k);
                    if (v !== null && v !== '') keep.set(k, v);
                });
                const query = keep.toString();
                window.location.href = query ? (`/katalog?${query}`) : '/katalog';
            },

            applyFilters() {
                const url = new URL(window.location.href);

                ['filter_kategori','filter_status','sort','filter_ukuran', 'filter_gender', 'filter_material'].forEach((k) => url.searchParams.delete(k));

                const genderMap = { pria: 'Pria', wanita: 'Wanita' };
                for (const id of this.filters) {
                    if (genderMap[id]) { url.searchParams.set('filter_gender', genderMap[id]); break; }
                }

                const kategoriMap = {
                    seragam: 'Seragam', formal: 'Formal', atasan: 'Atasan',
                    jas: 'Jas', batik: 'Batik', setelan: 'Setelan',
                };
                for (const id of this.filters) {
                    if (kategoriMap[id]) { url.searchParams.set('filter_kategori', kategoriMap[id]); break; }
                }

                if (this.filters.includes('stok-ready')) url.searchParams.set('filter_status', 'ready');
                if (this.filters.includes('stok-empty')) url.searchParams.set('filter_status', 'pre-order');

                const materialMap = {
                    katun: 'Katun', woll: 'Woll', kargo: 'Kargo',
                    resmi: 'Resmi', 'material-batik': 'Batik'
                };
                for (const id of this.filters) {
                    if (materialMap[id]) { url.searchParams.set('filter_material', materialMap[id]); break; }
                }

                const sizeMap = {
                    'size-s': 'S', 'size-m': 'M', 'size-l': 'L',
                    'size-xl': 'XL', 'size-xxl': 'XXL',
                };
                for (const id of this.filters) {
                    if (sizeMap[id]) { url.searchParams.set('filter_ukuran', sizeMap[id]); break; }
                }

                if (this.filters.includes('highest-price')) url.searchParams.set('sort', 'price_high');
                else if (this.filters.includes('lowest-price')) url.searchParams.set('sort', 'price_low');
                else if (this.filters.includes('newest')) url.searchParams.set('sort', 'newest');
                else if (this.filters.includes('best-selling')) url.searchParams.set('sort', 'best_selling');
                else if (this.filters.includes('relevan')) url.searchParams.set('sort', 'relevan');

                window.location.href = url.toString();
            },
        }">
        <div class="space-y-6 text-left">
            {{-- Pakaian Untuk (belum di-handle backend; tetap UI saja dulu) --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Pakaian Untuk</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Pria" id="pria" />
                    <x-guest.katalog.filter-button label="Wanita" id="wanita" />
                </div>
            </div>

            {{-- Kategori --}}
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
                </div>
            </div>

            {{-- Ketersediaan (ini yang dibutuhkan untuk CUS002) --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Ketersediaan</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Stok Ready" id="stok-ready" />
                    <x-guest.katalog.filter-button label="Pre-Order" id="stok-empty" />
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Default: semua produk yang tidak diarsipkan (stok &gt;= 0).
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Material Kain (belum di-handle backend; tetap UI saja dulu) --}}
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

                {{-- Ukuran (param disiapkan; backend menyusul) --}}
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

            {{-- Urutkan --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Urutkan</h4>
                <div class="flex flex-wrap gap-3">
                    <x-guest.katalog.filter-button label="Paling Relevant" id="relevan" />
                    <x-guest.katalog.filter-button label="Harga Tertinggi" id="highest-price" />
                    <x-guest.katalog.filter-button label="Harga Terendah" id="lowest-price" />
                    <x-guest.katalog.filter-button label="Terbaru" id="newest" />
                    <x-guest.katalog.filter-button label="Paling Laris" id="best-selling" />
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Saat ini yang dipakai: Terbaru / Harga Tertinggi / Harga Terendah.
                </p>
            </div>
        </div>

        {{-- debug chips terpilih --}}
        <div x-show="filters.length > 0" class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Filter Terpilih:</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="filterId in filters" :key="filterId">
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full border border-gray-200">
                        <span x-text="filterId"></span>
                        {{-- <button @click="toggleFilter(filterId)" class="hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-xmark ml-1"></i>
                        </button> --}}
                    </span>
                </template>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <div class="flex justify-end gap-3 w-full">
                <button @click="resetFilters()" data-cy="btn-reset-filter"
                    class="px-8 py-2 font-bold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Hapus Filter
                </button>
                <button @click="applyFilters()" data-cy="btn-apply-filter"
                    class="px-8 py-2 font-bold text-white bg-[#333333] rounded-md hover:bg-black transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>
</x-shared.modal_base>