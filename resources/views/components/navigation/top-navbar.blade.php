@extends('components.shared.top')
@section('action')
@php
$active = 'text-black border-b-2 border-yellow-400';
$inactive = 'text-gray-400 hover:text-gray-800';
@endphp

<nav class="flex items-center gap-10 text-xl">

    <a href="{{ route('home') }}"
        class="{{ request()->routeIs('home') ? $active : $inactive }}">
        Beranda
    </a>

    <a href="{{ route('kustom') }}"
        class="{{ request()->routeIs('kustom') ? $active : $inactive }}">
        Kustom
    </a>

    <a href="{{ route('katalog') }}"
        class="{{ request()->routeIs('katalog') ? $active : $inactive }}">
        Katalog
    </a>

    <a href="{{ route('cart') }}"
        class="{{ request()->routeIs('cart') ? $active : $inactive }}">
        <i class="fa-solid fa-cart-shopping"></i>
    </a>

</nav>

@endsection()