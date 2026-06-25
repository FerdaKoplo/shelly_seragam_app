@php
    $active = 'bg-secondary text-black shadow-inner';
    $inactive = 'text-white hover:text-black hover:bg-secondary hover:bg-opacity-70 transition-all duration-200';
    $role = auth()->user()?->role;
@endphp

<aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed left-0 top-0 lg:top-24 w-80 h-screen lg:h-[calc(100vh-6rem)] bg-neutral text-white transition-transform duration-300 ease-in-out z-50 lg:z-40 lg:translate-x-0 overflow-y-auto shadow-2xl lg:shadow-none">

    <div class="flex items-center justify-between px-8 py-6 lg:hidden border-b border-white/10">
        <span class="font-bebas text-2xl">Menu Navigasi</span>
        <button @click="sidebarOpen = false" class="text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="flex flex-col pt-8 lg:pt-12 w-full font-sans">
        @if($role === 'Admin')
            <a href="{{ route('manage.pegawai') }}"
                class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('manage.pegawai') ? $active : $inactive }}">
                Manajemen Pegawai
            </a>
        @endif

        <a href="{{ route('manage.transaksi') }}"
            class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('manage.transaksi') ? $active : $inactive }}">
            Manajemen Transaksi
        </a>

        <a href="{{ route('manage.katalog') }}"
            class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('manage.katalog') ? $active : $inactive }}">
            Manajemen Katalog
        </a>

        <a href="{{ route('manage.kustom') }}"
            class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('manage.kustom') ? $active : $inactive }}">
            Manajemen Produk Kustom
        </a>

        @if($role === 'Admin')
            <div class="mt-4 mb-2 px-8 border-b border-white/10"></div> {{-- Separator --}}

            <a href="{{ route('statistik.transaksi') }}"
                class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('statistik.transaksi') ? $active : $inactive }}">
                Statistik Transaksi
            </a>

            <a href="{{ route('manage.voucher') }}"
                class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('manage.voucher') ? $active : $inactive }}">
                Manajemen Voucher
            </a>

            <a href="{{ route('traffic') }}"
                class="block w-full text-right py-4 px-8 font-semibold text-lg mb-1 {{ request()->routeIs('traffic') ? $active : $inactive }}">
                Traffic Website
            </a>
        @endif
    </div>
</aside>