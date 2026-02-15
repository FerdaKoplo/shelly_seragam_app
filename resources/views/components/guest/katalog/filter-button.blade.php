@props(['label', 'id', 'active' => false])

<button 
    type="button"
    data-initial-active="{{ $active ? 'true' : 'false' }}"
    data-id="{{ $id }}"
    @click="toggleFilter('{{ $id }}')"
    {{ $attributes->merge([
        'class' => 'px-6 py-1.5 text-sm font-medium rounded-md border transition-all'
    ]) }}
    :class="filters.includes('{{ $id }}') 
        ? 'border-black bg-white text-black ring-1 ring-black' 
        : 'border-gray-300 bg-white text-gray-600 hover:border-gray-400'"
>
    {{ $label }}
</button>