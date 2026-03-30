@props(['activeCondition'])

<button type="button" 
    {{ $attributes->merge(['class' => 'px-4 py-2 border border-black rounded-lg font-medium transition-colors text-sm']) }}
    :class="{{ $activeCondition }} ? 'bg-black text-white' : 'bg-white text-black hover:bg-gray-50'">
    {{ $slot }}
</button>