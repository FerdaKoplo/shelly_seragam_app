@props(['color', 'id'])

<button 
    type="button"
    @click="selectedColor = '{{ $id }}'"
    {{ $attributes->merge(['class' => 'flex grow mx-2 h-10 rounded-md transition-all border-2']) }}
    style="background-color: {{ $color }};"
    :class="selectedColor === '{{ $id }}' ? 'border-black scale-110' : 'border-transparent'"
></button>