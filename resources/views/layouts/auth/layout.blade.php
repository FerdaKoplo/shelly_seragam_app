@extends('layouts.app')
@section('body')

@include('components.shared.top')

<div class="z-10">
    @yield('content')
</div>

@include('components.shared.footer')
@endsection