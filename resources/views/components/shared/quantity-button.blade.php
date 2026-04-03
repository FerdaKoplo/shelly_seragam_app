@props([
    'label' => 'Quantity',
    'min' => 1,
    'max' => 99,
    'model' => 'quantity' 
    // {{-- Default variable name --}}
])

<div class="mb-8">
    @if($label)
        <span class="font-bold text-lg block mb-3">{{ $label }}</span>
    @endif
    
    <div class="flex items-center border border-gray-300 w-28 rounded-md overflow-hidden bg-white">
        <button 
            type="button"
            @click="if({{ $model }} > {{ $min }}) {{ $model }}--" 
            class="px-3 py-1 text-xl hover:bg-gray-100 transition disabled:opacity-30"
            :disabled="{{ $model }} <= {{ $min }}"
        >
            −
        </button>

        <input 
            type="text" 
            x-model="{{ $model }}" 
            class="w-full text-center border-none focus:ring-0 text-sm font-bold bg-transparent pointer-events-none" 
            readonly
        >

        <button 
            type="button"
            @click="if({{ $model }} < {{ $max }}) {{ $model }}++" 
            class="px-3 py-1 text-xl hover:bg-gray-100 transition disabled:opacity-30"
            :disabled="{{ $model }} >= {{ $max }}"
        >
            +
        </button>
    </div>
</div>