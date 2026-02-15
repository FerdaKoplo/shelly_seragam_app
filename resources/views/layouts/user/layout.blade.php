@extends('layouts.app')

@section('body')
{{-- @include('components.shared.top') --}}
@include('components.user.nav')
<main class="bg-white pl-2 md:ps-60 lg:ps-80">
    @include('components.user.sidebar')
    <div class="p-4 mt-32">
        @yield('content')
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.min.js" integrity="sha512-n/G+dROKbKL3GVngGWmWfwK0yPctjZQM752diVYnXZtD/48agpUKLIn0xDQL9ydZ91x6BiOmTIFwWjjFi2kEFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection