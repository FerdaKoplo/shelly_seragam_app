<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @for ($i = 1; $i <= 3; $i++)
        <div class="border border-gray-300 rounded-lg p-4 transition-opacity duration-300"
            x-show="combinationCount >= {{ $i }}" x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100">

            <h4 class="font-bold mb-4">Material Jenis Kain {{ $i }}</h4>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'] as $material)
                    <button type="button" @click="selectedMaterials.m{{ $i }} = '{{ $material }}'"
                        :class="selectedMaterials.m{{ $i }} === '{{ $material }}' ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                        class="px-0 py-2 text-[10px] font-medium rounded-md border transition-all text-center">
                        {{ $material }}
                    </button>
                @endforeach
            </div>
        </div>
    @endfor
</div>