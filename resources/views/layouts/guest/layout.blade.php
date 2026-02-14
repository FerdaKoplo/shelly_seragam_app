@extends('layouts.app')

@section('body')
@include('components.navigation.top-navbar')

<main class="flex-grow flex flex-col">
    
    @yield('content')
</main>

@include('components.shared.footer')
@endsection