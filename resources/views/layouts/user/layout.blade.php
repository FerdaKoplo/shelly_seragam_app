@extends('layouts.app')
@section('body')
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-50">

        @include('components.user.nav')

        <div x-show="sidebarOpen" x-transition:enter="transition opacity-ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity-ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            x-cloak>
        </div>

        @include('components.user.sidebar')

        <main class="pt-24 transition-all duration-300 lg:ml-80">
            <div class="p-4 md:p-8">
                @yield('content')
            </div>
        </main>

        <x-shared.modal.delete-modal />
    </div>
@endsection