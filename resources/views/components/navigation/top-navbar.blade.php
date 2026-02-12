@extends('components.shared.top')
@section('navigation')
<nav class="flex items-center gap-8 text-xl space-x-3">
        <a href="/" class="hover:text-gray-600">Beranda</a>
        <a href="/kustom" class="hover:text-gray-600">Kustom</a>
        <a href="/katalog" class="hover:text-gray-600">Katalog</a>
        <a href="/cart" class="ml-4 hover:opacity-70 transition-opacity">
            <i class="fa-solid fa-cart-shopping"></i>
        </a>
    </nav>
@endsection()