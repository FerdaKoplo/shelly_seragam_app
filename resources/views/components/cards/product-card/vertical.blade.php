@props(['name', 'category', 'price', 'image' => null])

@php
    $fallbackImage = 'https://picsum.photos/seed/product-card/600/800';
    $imageUrl = $image ?: $fallbackImage;
@endphp

<div class="group cursor-pointer">
    <div class="bg-[#F3F3F3] rounded-2xl overflow-hidden aspect-[4/5] relative mb-3 transition-transform group-hover:-translate-y-1">
        <img src="{{ $imageUrl }}"
             alt="{{ $name }}"
             class="w-full h-full object-cover mix-blend-multiply">
    </div>

    <div class="space-y-1">
        <h3 class="font-bold text-lg leading-tight">{{ $name }}</h3>
        <p class="text-[10px] text-gray-400 font-medium">{{ $category }}</p>
        <p class="font-bold text-black text-lg">Rp. {{ $price }}</p>
    </div>
</div>