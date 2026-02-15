@props([
    'variant' => 'primary', 
    'href' => null, 
    'type' => 'button', 
    'rounded' => true
])

@php
    $baseClasses = "inline-flex items-center justify-center transition-all duration-200 font-medium uppercase tracking-wide border";
    
    $variants = [
        'primary' => 'bg-black text-white border-black hover:bg-gray-800',
        'outline' => 'bg-white text-black border-gray-200 hover:border-black',
        'ghost'   => 'border-transparent text-gray-600 hover:text-black',
        'dark'    => 'bg-[#262626] text-white border-[#262626] hover:bg-black',
        'white'   => 'bg-white text-black border-black hover:bg-gray-50',
    ];

    // Safely get the variant classes
    $variantClass = $variants[$variant] ?? $variants['primary'];

    // Logic for sizing and rounding
    $roundingClass = $rounded ? 'rounded-full px-6 py-2' : 'rounded-none w-full py-4';
    
    // Combine everything into one clean string
    $classes = "{$baseClasses} {$roundingClass} {$variantClass}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif