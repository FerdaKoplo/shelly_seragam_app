@extends('layouts.user.layout')
@section('title', 'Tambah Produk Kustom')

@section('content')
@php
$materials = ['Standar', 'Katun', 'Woll', 'Nylon', 'Kaos', 'Kargo', 'Satin', 'Polyester', 'Batik'];
$allCounts = [1, 2, 3];
$allBordirs = [0, 1, 2, 3, 4, 5];
$allSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
$defaultSections = ['Bundle', 'Atasan', 'Bawahan'];
$existingSections = \App\Models\ProdukKustom::pluck('spesifikasi_khusus')->toArray();
@endphp

<div class="flex justify-start mt-6 px-10 pb-20">
    </template>
    <div class="w-full flex flex-col gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('manage.kustom') }}" class="text-gray-400 hover:text-black transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-[28px] font-bold text-black">Tambah Produk Kustomisasi</h1>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('manage.kustom.store') }}" method="POST"
            x-data="kustomForm()"
            @submit.prevent="submitForm($el)">
            @csrf

            {{-- Section tabs --}}
            <div class="flex gap-8 border rounded-2xl border-black py-4 px-4 items-center mb-6 flex-wrap w-fit">
                <template x-for="(sec, idx) in sections" :key="sec.id">
                    <button type="button"
                        @click="!sec.exists && (activeSection = idx)"
                        :disabled="sec.exists"
                        :class="sec.exists
                            ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                            : activeSection === idx ? 'bg-black text-white border-black' : 'bg-white text-black border-black hover:bg-gray-50'"
                        class="px-14 py-2 border rounded-lg font-medium text-lg transition-colors relative">
                        <span x-text="sec.name"></span>
                        <span x-show="sec.exists"
                            class="absolute -top-2 -right-2 text-[10px] bg-gray-400 text-white px-1.5 py-0.5 rounded-full">
                            Ada
                        </span>
                    </button>
                </template>
                <button data-cy="btn-add-section" type="button" @click="addSection()"
                    class="px-16 py-2 border border-dashed border-gray-400 rounded-lg text-lg text-gray-500 hover:border-black hover:text-black transition-colors">
                    +
                </button>
            </div>

            {{-- Section panels --}}
            <template x-for="(sec, sIdx) in sections" :key="sec.id">
                <div x-show="activeSection === sIdx" class="flex flex-col gap-4">

                    {{-- Section header --}}
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold" x-text="'Section ' + sec.name"></h2>
                            <button type="button" x-show="sections.length > 1"
                                @click="removeSection(sIdx)"
                                class="text-xs text-red-500 hover:text-red-700 underline">
                                Hapus Section
                            </button>
                        </div>
                        <div class="w-full border-b border-black"></div>
                    </div>

                    {{-- ── Aspek Utama: Kombinasi Jenis Kain ── --}}
                    <div x-show="sec.showKombinasi"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col gap-5 relative">

                        <button type="button" @click="sec.showKombinasi = false"
                            class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-sm">X</button>

                        <h3 class="text-2xl font-bold">Kombinasi Jenis Kain</h3>

                        {{-- Admin chooses which count options customers can pick from --}}
                        <div class="flex gap-3 flex-wrap items-center">
                            @foreach($allCounts as $n)
                            <button type="button"
                                data-cy="toggle-kombinasi-{{ $n }}"
                                @click="toggleCount(sIdx, {{ $n }})"
                                :class="sec.enabledCounts.includes({{ $n }})
                                        ? 'bg-black text-white border-black'
                                        : 'bg-white text-black border-black hover:bg-gray-50'"
                                class="relative px-4 py-2 border rounded-lg text-sm font-medium transition-colors">
                                {{ $n }} Kombinasi
                                <span x-show="sec.enabledCounts.includes({{ $n }})"
                                    class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-gray-300 text-gray-700 rounded-full text-[9px] font-bold flex items-center justify-center">x</span>
                            </button>
                            @endforeach
                        </div>

                        {{-- Preview: material grid shown per max enabled slot — display only, customers pick here --}}
                        <div class="flex flex-wrap gap-4">
                            @for($i = 1; $i <= 3; $i++)
                                <div class="border border-black rounded-lg p-4 w-[280px]"
                                x-show="sec.enabledCounts.length && Math.max(...sec.enabledCounts) >= {{ $i }}"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">
                                <h4 class="font-bold text-sm mb-3">Material Jenis Kain {{ $i }}</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($materials as $mat)
                                    {{-- Non-interactive preview — customers will select these --}}
                                    <span class="px-1 py-1.5 text-[11px] rounded border border-gray-300 bg-white text-gray-800 text-center">
                                        {{ $mat }}
                                    </span>
                                    @endforeach
                                </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- ── Aspek Utama: Jumlah Titik Bordir ── --}}
                <div x-show="sec.showBordir"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col gap-4 relative">

                    <button type="button" @click="sec.showBordir = false"
                        class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-sm">X</button>

                    <h3 class="text-xl font-bold">Jumlah Titik Bordir</h3>

                    {{-- Admin chooses which bordir numbers customers can pick from --}}
                    <div class="flex gap-3 flex-wrap items-center">
                        @foreach($allBordirs as $n)
                        <button type="button"
                            @click="toggleBordir(sIdx, {{ $n }})"
                            :class="sec.enabledBordirs.includes({{ $n }})
                                        ? 'bg-black text-white border-black'
                                        : 'bg-white text-black border-black hover:bg-gray-50'"
                            class="relative w-16 text-center py-2 border rounded-lg text-sm font-medium transition-colors">
                            {{ $n }}
                            <span x-show="sec.enabledBordirs.includes({{ $n }})"
                                class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-gray-300 text-gray-700 rounded-full text-[9px] font-bold flex items-center justify-center">x</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- ── Tambahkan Aspek Utama ── --}}
                <button type="button"
                    @click="tambahAspek(sIdx)"
                    x-show="!sec.showKombinasi || !sec.showBordir"
                    data-cy="btn-add-aspek-utama"
                    class="w-full border border-dashed border-gray-400 rounded-xl py-4 text-sm text-gray-500 hover:bg-gray-50 transition">
                    Tambahkan Aspek Utama
                </button>

    </div>
    </template>

    {{-- Add Section --}}
    <div class="mt-4">
        <button type="button" @click="addSection()"
            class="w-full border border-dashed border-gray-400 rounded-xl py-4 text-sm text-gray-500 hover:bg-gray-50 transition">
            Tambahkan Section
        </button>
    </div>

    {{-- ── Aspek Tambahan ── --}}
    <div class="flex flex-col gap-4 mt-4">

        {{-- Catatan --}}
        <div x-show="showCatatan"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col gap-3 relative">
            <button type="button" @click="showCatatan = false"
                class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-sm">X</button>
            <h3 class="text-xl font-bold">Catatan</h3>
            <p class="text-xs text-gray-400">Pelanggan dapat menuliskan catatan tambahan untuk pesanan ini.</p>
            <textarea name="catatan" rows="3"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-black outline-none resize-none"
                placeholder="Contoh: Tuliskan catatan pesanan di sini..."></textarea>
        </div>

        {{-- Upload --}}
        <div x-show="showUpload"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col gap-3 relative">
            <button type="button" @click="showUpload = false"
                class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-sm">X</button>
            <p class="text-sm font-medium text-gray-700">* Upload Desain, Badge &amp; keperluan lainnya</p>
            <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl py-10 cursor-pointer hover:bg-gray-50 transition">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <span class="text-sm text-gray-500">Choose a file or drag &amp; drop it here</span>
                <span class="text-xs text-gray-400">SVG, JPEG, PNG formats, up to 10MB</span>
                <span class="mt-2 px-6 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-600 bg-white hover:border-black transition">Browse File</span>
                <input type="file" name="upload_desain" accept=".svg,.jpg,.jpeg,.png" class="hidden">
            </label>
        </div>

        {{-- Ukuran --}}
        <div x-show="showUkuran"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col gap-4 relative">
            <button type="button" @click="showUkuran = false"
                class="absolute top-3 right-4 text-gray-400 hover:text-black font-bold text-sm">X</button>
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Ukuran</h3>
                <span class="text-xs text-gray-400">Panduan Ukuran</span>
            </div>
            <div class="grid grid-cols-3 gap-2 w-fit">
                @foreach(['XS','S','M','L','XL','XXL'] as $size)
                <span class="px-6 py-2 border border-gray-300 rounded-lg text-sm text-center text-gray-700">{{ $size }}</span>
                @endforeach
            </div>
            <button type="button"
                class="w-full border border-dashed border-gray-300 rounded-lg py-2 text-xs text-gray-400 hover:border-gray-500 transition">
                Edit Variasi Ukuran
            </button>
        </div>

        {{-- Button: shows while any block is still hidden --}}
        <button type="button"
            @click="tambahAspekTambahan()"
            x-show="!showCatatan || !showUpload || !showUkuran"
            class="w-full border border-dashed border-black rounded-xl py-4 text-sm text-black hover:bg-gray-50 transition">
            Tambahkan Aspek Tambahan
        </button>

    </div>

    <div class="mt-6">
        <button type="submit"
            data-cy="btn-save-create"
            class="w-full py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
            Simpan
        </button>
    </div>
    </form>
</div>
</div>

<script>
    function kustomForm() {
        const defaultSections = @json($defaultSections);
        const existingSections = @json($existingSections);

        function makeSection(name) {
            return {
                id: Date.now() + Math.random(),
                name,
                exists: existingSections.includes(name),
                showKombinasi: false,
                showBordir: false,
                enabledCounts: [1, 2, 3],
                enabledBordirs: [0, 1, 2, 3, 4, 5],
            };
        }

        const sections = defaultSections.map(n => makeSection(n));
        const firstAvail = sections.findIndex(s => !s.exists);
        return {
            sections,
            activeSection: firstAvail >= 0 ? firstAvail : 0,

            addSection() {
                const name = prompt('Nama section baru:');
                if (!name || !name.trim()) return;
                if (existingSections.includes(name.trim())) {
                    alert(`Section "${name.trim()}" sudah ada. Gunakan Edit untuk mengubahnya.`);
                    return;
                }
                this.sections.push(makeSection(name.trim()));
                this.activeSection = this.sections.length - 1;
            },

            removeSection(idx) {
                this.sections.splice(idx, 1);
                if (this.activeSection >= this.sections.length) {
                    this.activeSection = this.sections.length - 1;
                }
            },

            tambahAspek(idx) {
                const sec = this.sections[idx];
                if (!sec.showKombinasi) {
                    sec.showKombinasi = true;
                    return;
                }
                if (!sec.showBordir) {
                    sec.showBordir = true;
                }
            },

            toggleCount(idx, n) {
                const arr = this.sections[idx].enabledCounts;
                const i = arr.indexOf(n);
                i === -1 ? arr.push(n) : arr.splice(i, 1);
                arr.sort((a, b) => a - b);
            },

            toggleBordir(idx, n) {
                const arr = this.sections[idx].enabledBordirs;
                const i = arr.indexOf(n);
                i === -1 ? arr.push(n) : arr.splice(i, 1);
                arr.sort((a, b) => a - b);
            },

            showCatatan: false,
            showUpload: false,
            showUkuran: false,

            tambahAspekTambahan() {
                if (!this.showCatatan) {
                    this.showCatatan = true;
                    return;
                }
                if (!this.showUpload) {
                    this.showUpload = true;
                    return;
                }
                if (!this.showUkuran) {
                    this.showUkuran = true;
                }
            },

            submitForm(form) {
                form.querySelectorAll('.alpine-generated').forEach(el => el.remove());

                // Only submit the active section
                const sec = this.sections[this.activeSection];

                const add = (name, value) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    input.className = 'alpine-generated';
                    form.appendChild(input);
                };

                add('sections[0][name]', sec.name);
                add('sections[0][show_kombinasi]', sec.showKombinasi ? '1' : '0');
                add('sections[0][show_bordir]', sec.showBordir ? '1' : '0');

                sec.enabledCounts.forEach((c, i) => add(`sections[0][kombinasi_counts][${i}]`, c));
                sec.enabledBordirs.forEach((b, i) => add(`sections[0][bordir_options][${i}]`, b));

                add('show_catatan', this.showCatatan ? '1' : '0');
                add('show_upload', this.showUpload ? '1' : '0');
                add('show_ukuran', this.showUkuran ? '1' : '0');

                form.submit();
            }
        };
    }
</script>
@endsection