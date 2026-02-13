@props(['label', 'active' => false])

<button {{ $attributes->merge([
    'class' => 'px-6 py-1.5 text-sm font-medium rounded-md border transition-all ' . 
    ($active 
        ? 'border-black bg-white text-black ring-1 ring-black' 
        : 'border-gray-300 bg-white text-gray-600 hover:border-gray-400')
]) }}>
    {{ $label }}
</button>