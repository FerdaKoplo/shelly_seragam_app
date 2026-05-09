@props(['name', 'category', 'price', 'image' => null])

@php
    $fallbackImage = 'https://picsum.photos/seed/product-card/600/800';
    $imageUrl = $image ?: $fallbackImage;
@endphp

<div class="group cursor-pointer flex flex-col h-full shadow-md rounded-2xl">
    <div class="bg-light rounded-2xl overflow-hidden aspect-[4/5] relative mb-3">
        <img src="{{ $imageUrl }}"
             alt="{{ $name }}"
             class="w-full h-full object-cover mix-blend-multiply">
    </div>

    <div class="flex flex-col flex-grow px-2 pb-3">
        <h3 class="font-bold text-lg leading-tight line-clamp-2 mb-1" title="{{ $name }}">
            {{ $name }}
        </h3>
        
        <p class="text-[10px] text-gray-400 font-medium mb-2">
            {{ $category }}
        </p>
        
        <p class="font-bold text-black text-lg mt-auto">
            Rp. {{ $price }}
        </p>
    </div>
</div>