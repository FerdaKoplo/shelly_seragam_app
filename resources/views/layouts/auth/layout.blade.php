@extends('layouts.app')

@section('body')
@include('components.shared.top')
@yield('content')
@include('components.shared.footer')
@endsection