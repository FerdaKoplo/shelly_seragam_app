@extends('layouts.user.layout')
@section('title', 'Tambah Produk Kustom')

@section('content')
    @php
        $materials = ['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'];
        $allCounts = [1, 2, 3];
        $allBordirs = [0, 1, 2, 3, 4, 5];
        $existingSections = \App\Models\ProdukKustom::pluck('spesifikasi_khusus')->toArray();
    @endphp

    <div class="flex justify-start mt-6 px-4 md:px-10 pb-20">
        <div class="w-full flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('manage.kustom') }}" class="text-gray-400 hover:text-black transition shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl md:text-[28px] font-bold text-black line-clamp-1">Tambah Produk Kustomisasi</h1>
            </div>

            <form action="{{ route('manage.kustom.store') }}" method="POST" x-data="kustomForm($dispatch)"
                @submit.prevent="submitForm($el)">
                @csrf

                <x-shared.modal_base name="modal-tambah-section" maxWidth="md" :showCloseButton="true">
                    <div class="p-4 md:p-2" data-cy="modal-tambah-section">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Tambah Section Baru</h3>
                        <p class="text-sm text-gray-500 mb-4">Masukkan nama area / spesifikasi khusus pakaian yang ingin
                            Anda kustomisasi.</p>

                        <input data-cy="nama-section-input" type="text" x-model="newSectionName"
                            @keydown.enter.prevent="confirmAddSection()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:border-black focus:ring-1 focus:ring-black outline-none transition"
                            placeholder="Contoh: Bentuk Kerah, Lengan, Sablon, dll." autofocus>
                    </div>

                    <x-slot name="footer">
                        <div class="flex justify-end gap-2 p-2">
                            <button data-cy="btn-add-section-batal" type="button"
                                @click="$dispatch('close-modal', 'modal-tambah-section')"
                                class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-black transition">Batal</button>
                            <button type="button" @click="confirmAddSection()" data-cy="btn-add-section-simpan"
                                class="px-5 py-2 text-sm font-bold bg-black text-white rounded-lg hover:bg-gray-800 transition">Tambahkan</button>
                        </div>
                    </x-slot>
                </x-shared.modal_base>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-6">
                    <div
                        class="flex border rounded-2xl border-black p-2 items-center gap-3 overflow-x-auto custom-scrollbar no-scrollbar">
                        <template x-for="(sec, idx) in sections" :key="sec.id">
                            <button type="button" @click="!sec.exists && (activeSection = idx)" :disabled="sec.exists"
                                :class="sec.exists
                                        ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                        : activeSection === idx ? 'bg-black text-white border-black' : 'bg-white text-black border-gray-300 hover:border-black'"
                                class="px-4 md:px-8 py-2 border rounded-lg font-medium text-sm md:text-lg transition-all relative whitespace-nowrap shrink-0">
                                <span x-text="sec.name"></span>
                                <span x-show="sec.exists"
                                    class="absolute -top-1 -right-1 text-[8px] bg-gray-400 text-white px-1.5 py-0.5 rounded-full">Ada</span>
                            </button>
                        </template>

                        <button data-cy="btn-add-section" type="button" @click="addSection()"
                            class="px-4 md:px-10 py-2 border border-dashed border-gray-400 rounded-lg text-sm md:text-lg text-gray-500 hover:border-black hover:text-black transition-colors whitespace-nowrap shrink-0">
                            + Section Baru
                        </button>
                    </div>
                </div>

                <template x-for="(sec, sIdx) in sections" :key="sec.id">
                    <div x-show="activeSection === sIdx && !sec.exists" class="flex flex-col gap-6">

                        <div class="flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg md:text-xl font-bold" x-text="'Konfigurasi Section ' + sec.name"></h2>
                                <button type="button" x-show="sections.length > 1" @click="removeSection(sIdx)"
                                    class="text-xs text-red-500 hover:text-red-700 underline font-medium">Hapus
                                    Section</button>
                            </div>
                            <div class="w-full border-b border-black"></div>
                        </div>

                        <div x-show="sec.showKombinasi" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
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

                        <button type="button" @click="tambahAspek(sIdx)" x-show="!sec.showKombinasi || !sec.showBordir" data-cy="btn-add-aspek-utama"
                            class="w-full border border-dashed border-gray-400 rounded-xl py-4 md:py-6 text-sm text-gray-500 hover:bg-gray-50 transition font-medium">
                            + Tambahkan Aspek Utama
                        </button>
                    </div>
                </template>

                <div class="flex flex-col gap-4 mt-8">
                    <div x-show="showCatatan"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-3 relative bg-white">
                        <button type="button" @click="showCatatan = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <h3 class="text-lg md:text-xl font-bold">Catatan Pesanan</h3>
                        <p class="text-xs text-gray-400">Pelanggan dapat menuliskan catatan tambahan untuk kustomisasi ini.
                        </p>
                        <textarea rows="3" disabled
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 outline-none resize-none"
                            placeholder="Preview input catatan..."></textarea>
                    </div>

                    <div x-show="showUpload"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-3 relative bg-white">
                        <button type="button" @click="showUpload = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <p class="text-sm font-medium text-gray-700">* Upload Desain & Keperluan Badge</p>
                        <div
                            class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-xl py-8 md:py-12 bg-gray-50">
                            <span class="text-xs md:text-sm text-gray-400">Preview Form Upload File</span>
                        </div>
                    </div>

                    <div x-show="showUkuran"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col gap-4 relative bg-white">
                        <button type="button" @click="showUkuran = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-lg p-2">×</button>
                        <h3 class="text-lg md:text-xl font-bold">Variasi Ukuran</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                <span
                                    class="px-4 md:px-6 py-2 border border-gray-200 rounded-lg text-xs md:text-sm text-center text-gray-500 bg-gray-50">{{ $size }}</span>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" @click="tambahAspekTambahan()" x-show="!showCatatan || !showUpload || !showUkuran"
                        class="w-full border border-dashed border-black rounded-xl py-4 text-sm font-bold text-black hover:bg-gray-50 transition">
                        + Tambahkan Aspek Tambahan (Catatan/Upload/Ukuran)
                    </button>
                </div>

                <div class="mt-10">
                    <button type="submit" data-cy="btn-save-create"
                        class="w-full py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg active:scale-[0.98]">
                        Simpan Semua Konfigurasi
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
        function kustomForm($dispatch) {
            const existingSections = @json($existingSections);

            function makeSection(name, isNew = false) {
                return {
                    id: Date.now() + Math.random(),
                    name: name,
                    exists: !isNew,
                    showKombinasi: false,
                    showBordir: false,
                    enabledCounts: [1],
                    enabledBordirs: [0],
                };
            }

            const sections = existingSections.map(n => makeSection(n, false));

            return {
                sections,
                activeSection: -1,
                newSectionName: '',

                addSection() {
                    this.newSectionName = '';
                    $dispatch('open-modal', 'modal-tambah-section');
                },

                confirmAddSection() {
                    const name = this.newSectionName;
                    if (!name || !name.trim()) {
                        $dispatch('notify', 'Nama section tidak boleh kosong!');
                        return;
                    }
                    const cleanName = name.trim();

                    if (this.sections.some(s => s.name.toLowerCase() === cleanName.toLowerCase())) {
                        $dispatch('notify', `Section "${cleanName}" sudah ada di tab form ini!`);
                        return;
                    }
                    if (existingSections.some(s => s.toLowerCase() === cleanName.toLowerCase())) {
                        $dispatch('notify', `Section "${cleanName}" sudah pernah dibuat di database! Silahkan gunakan menu Edit.`);
                        return;
                    }

                    this.sections.push(makeSection(cleanName, true));
                    this.activeSection = this.sections.length - 1;

                    $dispatch('close-modal', 'modal-tambah-section');
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

                showCatatan: false,
                showUpload: false,
                showUkuran: false,

                tambahAspekTambahan() {
                    if (!this.showCatatan) { this.showCatatan = true; return; }
                    if (!this.showUpload) { this.showUpload = true; return; }
                    if (!this.showUkuran) { this.showUkuran = true; return; }
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
                        if (sec.exists) return;
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

                    if (submittedCount === 0) {
                        $dispatch('notify', 'Silahkan tambahkan section baru sebelum menyimpan!');
                        return;
                    }

                    add('show_catatan', this.showCatatan ? '1' : '0');
                    add('show_upload', this.showUpload ? '1' : '0');
                    add('show_ukuran', this.showUkuran ? '1' : '0');

                    form.submit();
                }
            };
        }
    </script>
@endsection