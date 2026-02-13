{{-- resources/views/components/product-card.blade.php --}}
@props(['name' => 'Kemeja Kotak', 'price' => '114.000', 'image' => 'placeholder.jpg'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex gap-4 transition-transform hover:-translate-y-1">
    <div class="flex-1 flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-lg leading-tight">{{ $name }}</h3>
            <p class="text-[10px] text-gray-400 mt-1 line-clamp-2">Kemeja dengan motif kotak untuk sekolah formal dan non formal</p>
        </div>
        <div>
            <p class="font-bold text-black mt-2">Rp. {{ $price }}</p>
            <button class="bg-black text-white text-[10px] uppercase font-bold px-4 py-1 mt-2 rounded">Lihat</button>
        </div>
    </div>
    <div class="w-24 h-24 bg-[#EFEFEF] rounded-lg overflow-hidden flex-shrink-0">
        <img src="{{ asset('images/' . $image) }}" class="w-full h-full object-contain">
    </div>
</div>