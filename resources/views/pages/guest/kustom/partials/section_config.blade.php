<div class="border-t-2 border-black pt-4 mb-10" 
     x-data="{ 
        combinationCount: 1, 
        selectedMaterials: { m1: 'Standar', m2: 'Standar', m3: 'Standar' },
        bordirCount: 0 
     }">
    
    <h2 class="text-xl font-bold mb-6">{{ $title }}</h2>

    <div class="mb-8">
        <h3 class="text-2xl font-bold mb-4">Kombinasi Jenis Kain</h3>
        <div class="flex gap-3 mb-6">
            @foreach([1, 2, 3] as $num)
                <button type="button" 
                    @click="combinationCount = {{ $num }}"
                    :class="combinationCount === {{ $num }} ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                    class="px-6 py-1.5 text-sm font-medium rounded-md border transition-all">
                    {{ $num }} Kombinasi
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @for ($i = 1; $i <= 3; $i++)
                {{-- Only show the card if the current index is <= combinationCount --}}
                <div class="border border-gray-300 rounded-lg p-4 transition-opacity duration-300"
                     x-show="combinationCount >= {{ $i }}"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <h4 class="font-bold mb-4">Material Jenis Kain {{ $i }}</h4>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'] as $material)
                            <button type="button"
                                @click="selectedMaterials.m{{ $i }} = '{{ $material }}'"
                                :class="selectedMaterials.m{{ $i }} === '{{ $material }}' ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                                class="px-0 py-2 text-[10px] font-medium rounded-md border transition-all text-center">
                                {{ $material }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold mb-3">Jumlah Titik Bordir</h3>
        <div class="flex flex-wrap gap-2">
            @for ($i = 0; $i <= 5; $i++)
                <button type="button"
                    @click="bordirCount = {{ $i }}"
                    :class="bordirCount === {{ $i }} ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                    class="min-w-[80px] px-6 py-1.5 text-sm font-medium rounded-md border transition-all">
                    {{ $i }}
                </button>
            @endfor
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-3xl font-black uppercase tracking-tight">Estimasi Harga Produk</h3>
    </div>
</div>