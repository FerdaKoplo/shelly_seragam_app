<div class="border-t-2 border-black pt-4 mb-10"
     x-data="{
        // dari include: 'atasan' atau 'bawahan'
        prefix: '{{ $prefix ?? '' }}',

        combinationCount: 1,
        selectedMaterials: { m1: 'Standar', m2: 'Standar', m3: 'Standar' },
        bordirCount: 0,

        // ===== Dummy pricing (bebas dulu) =====
        basePrice() {
            return this.prefix === 'atasan' ? 120000 : 110000;
        },

        materialAddon: {
            'Standar': 0,
            'Katun': 10000,
            'Woll': 20000,
            'Nylon': 8000,
            'Kaos': 6000,
            'Kargo': 12000,
            'Satin': 15000,
            'Polyester': 9000,
            'Batik': 25000
        },

        bordirPerTitik: 7000,

        formatCurrency(num) {
            num = Number(num || 0);
            return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        estimate() {
            let materialCost = 0;

            for (let i = 1; i <= this.combinationCount; i++) {
                const key = 'm' + i;
                const mat = this.selectedMaterials[key] || 'Standar';
                materialCost += (this.materialAddon[mat] ?? 0);
            }

            const bordirCost = (Number(this.bordirCount) || 0) * this.bordirPerTitik;

            return this.basePrice() + materialCost + bordirCost;
        },

        dispatchEstimate() {
            // kirim ke parent agar total bundle bisa dihitung
            this.$dispatch('section-estimate', {
                prefix: this.prefix,
                total: this.estimate()
            });
        },

        init() {
            // initial dispatch
            this.dispatchEstimate();

            // Alpine deep-watch kadang beda versi; ini aman untuk primitive changes.
            this.$watch('combinationCount', () => this.dispatchEstimate());
            this.$watch('bordirCount', () => this.dispatchEstimate());

            // selectedMaterials itu object; paling aman dispatch di setiap click (lihat @click di bawah),
            // tapi tetap coba watch (kalau versi Alpine support).
            try {
                this.$watch('selectedMaterials', () => this.dispatchEstimate(), { deep: true });
            } catch (e) {
                // ignore kalau tidak support
            }
        }
     }">

    <h2 class="text-xl font-bold mb-6">{{ $title }}</h2>

    <div class="mb-8">
        <h3 class="text-2xl font-bold mb-4">Kombinasi Jenis Kain</h3>
        <div class="flex gap-3 mb-6">
            @foreach([1, 2, 3] as $num)
                <button type="button"
                data-cy="combination-{{ $num }}"
                    @click="combinationCount = {{ $num }}; dispatchEstimate();"
                    :class="combinationCount === {{ $num }} ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                    class="px-6 py-1.5 text-sm font-medium rounded-md border transition-all">
                    {{ $num }} Kombinasi
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @for ($i = 1; $i <= 3; $i++)
                <div class="border border-gray-300 rounded-lg p-4 transition-opacity duration-300"
                     x-show="combinationCount >= {{ $i }}"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">

                    <h4 class="font-bold mb-4">Material Jenis Kain {{ $i }}</h4>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'] as $material)
                            <button type="button"
                            data-cy="material-{{ $i }}-{{ strtolower($material) }}"
                                @click="selectedMaterials.m{{ $i }} = '{{ $material }}'; dispatchEstimate();"
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
                data-cy="bordir-{{ $i }}"
                    @click="bordirCount = {{ $i }}; dispatchEstimate();"
                    :class="bordirCount === {{ $i }} ? 'border-black bg-black text-white' : 'border-gray-300 bg-white text-gray-600'"
                    class="min-w-[80px] px-6 py-1.5 text-sm font-medium rounded-md border transition-all">
                    {{ $i }}
                </button>
            @endfor
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-3xl font-black uppercase tracking-tight">Estimasi Harga Produk</h3>
        <div class="mt-2 text-xl font-bold" x-text="formatCurrency(estimate())"></div>
        <p class="text-xs text-gray-400 mt-1">
            *Estimasi dummy (sementara), menunggu baseline harga final.
        </p>
    </div>
</div>