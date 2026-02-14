@props(['categories'])

<x-shared.modal_base name="modal-filter-katalog" maxWidth="md" title="Filter Produk">
    
    <div class="mb-6 pb-4 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">Filter Katalog</h2>
        <p class="text-sm text-gray-500 mt-1">Sesuaikan tampilan produk sesuai kebutuhan Anda</p>
    </div>

    <form method="GET" action="{{ route('manage.katalog') }}" class="space-y-6">
        
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
            <div class="relative">
                <select name="filter_kategori" 
                    class="w-full appearance-none border border-black rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('filter_kategori') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Rentang Harga</label>
            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input type="number" name="min_harga" placeholder="Min" value="{{ request('min_harga') }}"
                        class="w-full border border-black rounded-md pl-10 pr-3 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black placeholder-gray-400">
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input type="number" name="max_harga" placeholder="Max" value="{{ request('max_harga') }}"
                        class="w-full border border-black rounded-md pl-10 pr-3 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black placeholder-gray-400">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Status Stok</label>
            <div class="relative">
                <select name="filter_status" 
                    class="w-full appearance-none border border-black rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-black bg-white">
                    <option value="active" {{ request('filter_status') == 'active' ? 'selected' : '' }}>Aktif (Tersedia)</option>
                    <option value="ready" {{ request('filter_status') == 'ready' ? 'selected' : '' }}>Stok Ready (>0)</option>
                    <option value="empty" {{ request('filter_status') == 'empty' ? 'selected' : '' }}>Stok Habis (0)</option>
                    <option value="archived" {{ request('filter_status') == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-4 mt-6">
            <a href="{{ route('manage.katalog') }}" 
                class="flex-1 text-center py-3 rounded-md border border-black text-black font-medium text-sm hover:bg-gray-50 transition">
                Reset Filter
            </a>
            <button type="submit" 
                class="flex-1 py-3 rounded-md bg-[#323232] text-white font-medium text-sm hover:opacity-90 transition shadow-md">
                Terapkan
            </button>
        </div>
    </form>

</x-shared.modal_base>