@props(['variant' => 'primary', 'href' => '#'])

@php
    $baseClasses = "inline-flex items-center justify-center px-6 py-2 rounded-full font-medium transition-all duration-200 border-2";
    $variants = [
        'primary' => 'bg-black text-white border-black hover:bg-gray-800',
        'outline' => 'bg-white text-black border-gray-200 hover:border-black',
        'ghost' => 'border-transparent text-gray-600 hover:text-black',
    ];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses {$variants[$variant]}"]) }}>
    {{ $slot }}
</a>