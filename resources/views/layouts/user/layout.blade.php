@extends('layouts.app')

@section('body')
@include('components.shared.top')
@include('components.user.nav')
<main class="bg-white pl-2 md:pl-60">
    @include('components.user.sidebar')
    <div class="p-4">
        @yield('content')
    </div>
</main>

@include('components.shared.footer')
@endsection