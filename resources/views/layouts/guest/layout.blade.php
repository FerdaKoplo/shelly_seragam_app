@extends('layouts.app')

@section('body')
@include('components.navigation.top-navbar')
@yield('content')
@include('components.shared.footer')
@endsection