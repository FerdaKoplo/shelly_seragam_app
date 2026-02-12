@php
$active = 'bg-secondary text-black ';
$inactive = 'text-white hover:text-black hover:bg-secondary hover:bg-opacity-70 transition-colors duration-200';
$role = auth()->user()->role;
@endphp

<aside class="fixed left-0 top-24 w-80 h-[calc(100vh-6rem)] bg-neutral text-white transition-transform -translate-x-full lg:translate-x-0 overflow-y-auto">

    <div class="flex flex-col pt-12 w-full font-sans">
        @if($role === 'Admin' )
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
        @if($role === 'Admin' )
        <a href="{{ route('statistik.transaksi') }}"
            class="block w-full text-right py-4 px-8 font-semibold text-lg transition-colors duration-200 mb-1 {{ request()->routeIs('statistik.transaksi') ? $active : $inactive }}">
            Statistik Transaksi
        </a>

        <a href="{{ route('traffic') }}"
            class="block w-full text-right py-4 px-8 font-semibold text-lg transition-colors duration-200 mb-1 {{ request()->routeIs('traffic') ? $active : $inactive }}">
            Traffic Website
        </a>
        @endif
    </div>
</aside>