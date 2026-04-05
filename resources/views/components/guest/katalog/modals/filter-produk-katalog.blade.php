@props([
    'categories' => collect(),
])

<x-shared.modal_base name="modal-filter-produk-katalog" title="Filter Produk" maxWidth="3xl">
    <form method="GET" action="{{ route('katalog') }}" class="space-y-6 text-left">

        {{-- Keep existing search query from search bar --}}
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        {{-- Kategori (dari DB) --}}
        <div>
            <h4 class="font-bold text-gray-900 mb-3">Kategori</h4>

            <div class="relative">
                <select
                    name="filter_kategori"
                    class="w-full appearance-none border border-black rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black bg-white"
                >
                    <option value="">Semua Kategori</option>

                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('filter_kategori') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Rentang Harga (opsional, tapi controller bisa support) --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Rentang Harga</h4>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input
                            type="number"
                            name="min_harga"
                            placeholder="Min"
                            value="{{ request('min_harga') }}"
                            class="w-full border border-black rounded-md pl-10 pr-3 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black placeholder-gray-400"
                        >
                    </div>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input
                            type="number"
                            name="max_harga"
                            placeholder="Max"
                            value="{{ request('max_harga') }}"
                            class="w-full border border-black rounded-md pl-10 pr-3 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black placeholder-gray-400"
                        >
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    Kosongkan jika tidak ingin membatasi harga.
                </p>
            </div>

            {{-- Status stok --}}
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Status Stok</h4>

                <div class="relative">
                    <select
                        name="filter_status"
                        class="w-full appearance-none border border-black rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black bg-white"
                    >
                        {{-- active = semua yang tidak diarsipkan (stok >= 0) --}}
                        <option value="active" {{ request('filter_status', 'active') == 'active' ? 'selected' : '' }}>
                            Semua (Tidak diarsipkan)
                        </option>
                        <option value="ready" {{ request('filter_status') == 'ready' ? 'selected' : '' }}>
                            Stok Ready (&gt; 0)
                        </option>
                        <option value="empty" {{ request('filter_status') == 'empty' ? 'selected' : '' }}>
                            Stok Habis (0)
                        </option>
                    </select>

                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Urutkan --}}
        <div>
            <h4 class="font-bold text-gray-900 mb-3">Urutkan</h4>

            <div class="relative">
                <select
                    name="sort"
                    class="w-full appearance-none border border-black rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black bg-white"
                >
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                        Terbaru
                    </option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                        Harga Tertinggi
                    </option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                        Harga Terendah
                    </option>
                </select>

                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>

            <p class="text-xs text-gray-500 mt-2">
                "Paling laris" & "Paling relevan" bisa ditambahkan setelah ada data penjualan / scoring relevansi.
            </p>
        </div>

        {{-- Action buttons --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a
                href="{{ route('katalog') }}"
                class="px-8 py-2 font-bold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
            >
                Reset
            </a>

            <button
                type="submit"
                class="px-8 py-2 font-bold text-white bg-[#333333] rounded-md hover:bg-black transition-colors"
            >
                Terapkan Filter
            </button>
        </div>
    </form>
</x-shared.modal_base>