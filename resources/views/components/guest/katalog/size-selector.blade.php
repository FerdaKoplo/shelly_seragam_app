@props(['label', 'id'])
<button
    type="button"
    @click="selectedSize = '{{ $id }}'"
    {{ $attributes->merge(['class' => 'px-4 py-2 text-sm font-medium border rounded-md transition-all text-center']) }}
    :class="selectedSize === '{{ $id }}' 
        ? 'border-black bg-white text-black ring-1 ring-black' 
        : 'border-gray-200 bg-white text-gray-500 hover:border-gray-400'">
    {{ $label }}
</button>