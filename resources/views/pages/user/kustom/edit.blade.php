@extends('layouts.user.layout')
@section('title', 'Edit Produk Kustom')

@section('content')
    @php
        $materials = ['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'];
        $allCounts = [1, 2, 3];
        $allBordirs = [0, 1, 2, 3, 4, 5];
        $existingSections = \App\Models\ProdukKustom::pluck('spesifikasi_khusus')->toArray();

        $sectionData = $kustoms->map(function ($k) {
            $details = $k->produk->detailProduks->keyBy('nama_detail');

            $getRaw = fn($name) => $details->get($name)
                ?->pilihanDetails
                ->map(fn($p) => $p->getRawOriginal('opsi'))
                ->map(fn($v) => is_string(json_decode($v)) ? json_decode($v) : $v)
                ->toArray() ?? [];

            $counts = array_map('intval', $getRaw('Jumlah Kombinasi Kain'));
            $maxCount = count($counts) > 0 ? max($counts) : 1;

            $bordirs = array_map('intval', $getRaw('Jumlah Titik Bordir'));
            $maxBordir = count($bordirs) > 0 ? max($bordirs) : 0;

            $getFlag = function ($name) use ($details) {
                $raw = $details->get($name)?->pilihanDetails->first()?->getRawOriginal('opsi');
                if ($raw === null)
                    return false;
                $decoded = json_decode($raw);
                return ($decoded !== null ? (string) $decoded : $raw) === '1';
            };

            return [
                'id' => $k->kustom_id,
                'name' => $k->spesifikasi_khusus,
                'showKombinasi' => count($counts) > 0,
                'showBordir' => count($bordirs) > 0,
                'enabledCounts' => [$maxCount],
                'enabledBordirs' => [$maxBordir],
                'showCatatan' => $getFlag('Catatan'),
                'showUpload' => $getFlag('Upload Desain'),
                'showUkuran' => $getFlag('Ukuran'),
            ];
        })->values()->toArray();
    @endphp

    <div class="flex justify-start mt-6 px-4 md:px-10 pb-20">
        <div class="w-full flex flex-col gap-6">

            <div class="flex items-center gap-4">
                <a href="{{ route('manage.kustom') }}" class="text-gray-400 hover:text-black transition shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl md:text-[28px] font-bold text-black line-clamp-1">Edit Produk Kustomisasi</h1>
            </div>

            <form action="{{ route('manage.kustom.update', $kustoms->first()->kustom_id) }}" method="POST"
                x-data="kustomEditForm($dispatch)" @submit.prevent="submitForm($el)">
                @csrf @method('PUT')

                <div class="flex border rounded-2xl border-black p-2 items-center gap-3 mb-6 overflow-x-auto no-scrollbar custom-scrollbar"
                    data-cy="edit-section-tab">
                    <template x-for="(sec, idx) in sections" :key="sec.internalId">
                        <button type="button" @click="activeSection = idx"
                            :class="activeSection === idx ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300'"
                            class="px-5 py-2 border rounded-lg font-medium text-sm transition-all whitespace-nowrap shrink-0"
                            x-text="sec.name">
                        </button>
                    </template>
                </div>

                <template x-for="(sec, sIdx) in sections" :key="sec.internalId">
                    <div x-show="activeSection === sIdx" class="flex flex-col gap-6">

                        <div class="flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg md:text-xl font-bold" x-text="'Konfigurasi Section ' + sec.name"></h2>
                                <button data-cy="btn-delete-kustom" type="button" x-show="sections.length > 1"
                                    @click="removeSection(sIdx)"
                                    class="text-xs text-red-500 hover:text-red-700 underline font-medium">
                                    Hapus Section
                                </button>
                            </div>
                            <div class="w-full border-b border-black"></div>
                        </div>

                        <div x-show="sec.showKombinasi" x-transition:enter="transition ease-out duration-200"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-5 relative bg-white">

                            <button type="button" @click="sec.showKombinasi = false"
                                class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>

                            <h3 class="text-xl md:text-2xl font-bold pr-8">Kombinasi Jenis Kain</h3>

                            <div class="flex gap-2 md:gap-3 flex-wrap items-center">
                                @foreach($allCounts as $n)
                                    <button data-cy="toggle-kombinasi-{{ $n }}" type="button" @click="toggleCount(sIdx, {{ $n }})"
                                        :class="sec.enabledCounts.includes({{ $n }}) ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300'"
                                        class="relative px-3 md:px-4 py-2 border rounded-lg text-xs md:text-sm font-medium transition-colors">
                                        {{ $n }} Kombinasi
                                        <span x-show="sec.enabledCounts.includes({{ $n }})"
                                            class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-gray-300 text-gray-700 rounded-full text-[9px] font-bold flex items-center justify-center">x</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                @for($i = 1; $i <= 3; $i++)
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                                        x-show="sec.enabledCounts.length && Math.max(...sec.enabledCounts) >= {{ $i }}"
                                        x-transition:enter="transition ease-out duration-150">
                                        <h4 class="font-bold text-sm mb-3 text-gray-700">Material Jenis Kain {{ $i }}</h4>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($materials as $mat)
                                                <span
                                                    class="px-1 py-1.5 text-[10px] md:text-[11px] rounded border border-gray-300 bg-white text-gray-600 text-center truncate">
                                                    {{ $mat }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div x-show="sec.showBordir" x-transition:enter="transition ease-out duration-200"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-4 relative bg-white">

                            <button type="button" @click="sec.showBordir = false"
                                class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>

                            <h3 class="text-xl font-bold pr-8">Jumlah Titik Bordir</h3>

                            <div class="flex gap-2 md:gap-3 flex-wrap items-center">
                                @foreach($allBordirs as $n)
                                    <button type="button" @click="toggleBordir(sIdx, {{ $n }})"
                                        :class="sec.enabledBordirs.includes({{ $n }}) ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300'"
                                        class="relative w-12 md:w-16 text-center py-2 border rounded-lg text-sm font-medium transition-colors">
                                        {{ $n }}
                                        <span x-show="sec.enabledBordirs.includes({{ $n }})"
                                            class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-gray-300 text-gray-700 rounded-full text-[9px] font-bold flex items-center justify-center">x</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <button type="button" @click="tambahAspek(sIdx)" x-show="!sec.showKombinasi || !sec.showBordir"
                            class="w-full border border-dashed border-gray-400 rounded-xl py-4 md:py-6 text-sm text-gray-500 hover:bg-gray-50 transition font-medium">
                            + Edit / Tambahkan Aspek Utama
                        </button>
                    </div>
                </template>

                <div class="flex flex-col gap-4 mt-8">
                    <div x-show="showCatatan"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-3 relative bg-white">
                        <button type="button" @click="showCatatan = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <h3 class="text-lg md:text-xl font-bold">Catatan</h3>
                    </div>

                    <div x-show="showUpload"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-3 relative bg-white">
                        <button type="button" @click="showUpload = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <h3 class="text-lg md:text-xl font-bold">Upload Desain</h3>
                    </div>

                    <div x-show="showUkuran"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-4 relative bg-white">
                        <button type="button" @click="showUkuran = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <h3 class="text-lg md:text-xl font-bold">Ukuran</h3>
                    </div>

                    <button type="button" @click="tambahAspekTambahan()" x-show="!showCatatan || !showUpload || !showUkuran"
                        class="w-full border border-dashed border-black rounded-xl py-4 text-sm font-bold text-black hover:bg-gray-50 transition">
                        + Tambahkan Aspek Tambahan
                    </button>
                </div>

                <div class="mt-10">
                    <button type="submit" data-cy="btn-save-edit"
                        class="w-full py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #000;
            border-radius: 10px;
        }
    </style>

    <script>
        function kustomEditForm($dispatch) {
            const saved = @json($sectionData);
            const existingSections = @json($existingSections);

            return {
                activeSection: 0,
                newSectionName: '',

                sections: saved.map(s => ({
                    ...s,
                    internalId: Date.now() + Math.random(),
                    id: s.id
                })),

                showCatatan: saved.length > 0 && saved[0].showCatatan,
                showUpload: saved.length > 0 && saved[0].showUpload,
                showUkuran: saved.length > 0 && saved[0].showUkuran,

                tambahAspekTambahan() {
                    if (!this.showCatatan) { this.showCatatan = true; return; }
                    if (!this.showUpload) { this.showUpload = true; return; }
                    if (!this.showUkuran) { this.showUkuran = true; return; }
                },

 
                removeSection(idx) {
                    this.sections.splice(idx, 1);
                    if (this.activeSection >= this.sections.length) {
                        this.activeSection = this.sections.length - 1;
                    }
                },

                tambahAspek(idx) {
                    const sec = this.sections[idx];
                    if (!sec.showKombinasi) { sec.showKombinasi = true; return; }
                    if (!sec.showBordir) { sec.showBordir = true; }
                },

                toggleCount(idx, n) {
                    this.sections[idx].enabledCounts = [n];
                },

                toggleBordir(idx, n) {
                    this.sections[idx].enabledBordirs = [n];
                },

                submitForm(form) {
                    form.querySelectorAll('.alpine-generated').forEach(el => el.remove());

                    const add = (name, value) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        input.className = 'alpine-generated';
                        form.appendChild(input);
                    };

                    let submittedCount = 0;

                    this.sections.forEach((sec) => {
                        if (sec.id) add(`sections[${submittedCount}][kustom_id]`, sec.id);
                        add(`sections[${submittedCount}][name]`, sec.name);
                        add(`sections[${submittedCount}][show_kombinasi]`, sec.showKombinasi ? '1' : '0');
                        add(`sections[${submittedCount}][show_bordir]`, sec.showBordir ? '1' : '0');

                        if (sec.showKombinasi && sec.enabledCounts.length > 0) {
                            const maxC = sec.enabledCounts[0];
                            for (let i = 1; i <= maxC; i++) {
                                add(`sections[${submittedCount}][kombinasi_counts][${i - 1}]`, i);
                            }
                        }

                        if (sec.showBordir && sec.enabledBordirs.length > 0) {
                            const maxB = sec.enabledBordirs[0];
                            for (let i = 0; i <= maxB; i++) {
                                add(`sections[${submittedCount}][bordir_options][${i}]`, i);
                            }
                        }

                        submittedCount++;
                    });

                    add('show_catatan', this.showCatatan ? '1' : '0');
                    add('show_upload', this.showUpload ? '1' : '0');
                    add('show_ukuran', this.showUkuran ? '1' : '0');

                    form.submit();
                }
            };
        }
    </script>
@endsection