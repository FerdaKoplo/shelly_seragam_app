<x-shared.modal_base name="filter-katalog" title="Filter Produk" maxWidth="3xl">
    <div class="space-y-6 text-left">
        {{-- Pakaian Untuk Section --}}
        <div>
            <h4 class="font-bold text-gray-900 mb-3">Pakaian Untuk</h4>
            <div class="flex flex-wrap gap-3">
                <x-guest.katalog.filter-button label="Pria" active />
                <x-guest.katalog.filter-button label="Wanita" />
            </div>
        </div>

        {{-- Kategori Section --}}
        <div>
            <h4 class="font-bold text-gray-900 mb-3">Kategori</h4>
            <div class="flex flex-wrap gap-3">
                <x-guest.katalog.filter-button label="Semua" active />
                <x-guest.katalog.filter-button label="Seragam" />
                <x-guest.katalog.filter-button label="Formal" />
                <x-guest.katalog.filter-button label="Atasan" />
                <x-guest.katalog.filter-button label="Jas" />
                <x-guest.katalog.filter-button label="Batik" />
                <x-guest.katalog.filter-button label="Setelan" />
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
                    <x-guest.katalog.filter-button label="Semua" active />
                    <x-guest.katalog.filter-button label="Katun" />
                    <x-guest.katalog.filter-button label="Woll" />
                    <x-guest.katalog.filter-button label="Kargo" />
                    <x-guest.katalog.filter-button label="Resmi" />
                    <x-guest.katalog.filter-button label="Batik" />
                </div>
            </div>

            {{-- Ukuran Section --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Ukuran</h4>
                <div class="grid grid-cols-3 gap-2">
                    <x-guest.katalog.filter-button label="All Size" active />
                    <x-guest.katalog.filter-button label="S" />
                    <x-guest.katalog.filter-button label="M" />
                    <x-guest.katalog.filter-button label="L" />
                    <x-guest.katalog.filter-button label="XL" />
                    <x-guest.katalog.filter-button label="XXL" />
                </div>
            </div>
        </div>

        {{-- Urutkan Section --}}
        <div>
            <h4 class="font-bold text-gray-900 mb-3">Urutkan</h4>
            <div class="flex flex-wrap gap-3">
                <x-guest.katalog.filter-button label="Paling Relevant" active />
                <x-guest.katalog.filter-button label="Harga Tertinggi" />
                <x-guest.katalog.filter-button label="Harga Terendah" />
                <x-guest.katalog.filter-button label="Terbaru" />
                <x-guest.katalog.filter-button label="Paling Laris" />
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-3 w-full">
            <button @click="closeModal()" class="px-8 py-2 font-bold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                Hapus Filter
            </button>
            <button class="px-8 py-2 font-bold text-white bg-[#333333] rounded-md hover:bg-black transition-colors">
                Terapkan Filter
            </button>
        </div>
    </x-slot>
</x-shared.modal_base>