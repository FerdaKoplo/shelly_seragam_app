@extends('layouts.app')

@section('body')

@include('components.user.nav')
<main class="bg-white pl-2 md:ps-60 lg:ps-80">
    @include('components.user.sidebar')
    <div class="p-4 mt-32">
        @yield('content')
    </div>
</main>

@endsection