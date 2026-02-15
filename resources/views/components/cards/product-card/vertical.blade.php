@props(['name', 'category', 'price'])

<div class="group cursor-pointer">
    <div class="bg-[#F3F3F3] rounded-2xl overflow-hidden aspect-[4/5] relative mb-3 transition-transform group-hover:-translate-y-1">
        <img src="https://images.unsplash.com/photo-1598033129183-c4f50c7176c8?q=80&w=400" 
             class="w-full h-full object-cover mix-blend-multiply">
    </div>
    
    <div class="space-y-1">
        <h3 class="font-bold text-lg leading-tight">{{ $name }}</h3>
        <p class="text-[10px] text-gray-400 font-medium">{{ $category }}</p>
        <p class="font-bold text-black text-lg">Rp. {{ $price }}</p>
    </div>
</div>