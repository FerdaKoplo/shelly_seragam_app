@extends('layouts.user.layout')
@section('title', 'Manajemen Produk Kustom')

@section('content')
    <div class="flex justify-start mt-6 px-4 md:px-10 pb-20">
        <div class="w-full flex flex-col gap-6">

            <h1 class="text-2xl md:text-[28px] font-bold text-black">Produk Kustomisasi</h1>

            @php
                $sections = $kustoms->pluck('spesifikasi_khusus')->unique()->values()->toArray();
            @endphp

            <div class="w-full" x-data="{ active: '{{ $sections[0] ?? '' }}' }">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div
                        class="border border-black py-2 md:py-3 rounded-2xl px-3 md:px-4 gap-3 md:gap-4 flex overflow-x-auto custom-scrollbar">
                        <a  data-cy="btn-add-kustom" href="{{ route('manage.kustom.create') }}"
                            class="inline-flex items-center justify-center py-1.5 px-6 md:px-14 bg-white border border-dashed border-black text-black rounded-lg font-medium text-lg hover:bg-gray-50 transition shrink-0">
                            +
                        </a>

                        @foreach($sections as $sec)
                            <button data-cy="section-tab-{{ $sec }}'" @click="active = '{{ $sec }}'" :class="active === '{{ $sec }}'
                                            ? 'bg-black text-white border-black'
                                            : 'bg-white text-black border-gray-300 hover:border-black'"
                                class="px-6 md:px-10 py-1.5 border rounded-lg text-base md:text-lg font-medium transition-colors whitespace-nowrap shrink-0">
                                {{ $sec }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($kustoms->isEmpty())
                    <div class="w-full flex items-center justify-center py-24">
                        <p class="text-lg md:text-xl text-gray-800">Belum Ada Data Aspek Kustomisasi</p>
                    </div>
                @else
                    <div class="w-full flex flex-col gap-3 mt-6">
                        @foreach($kustoms as $kustom)
                            <div x-show="active === '{{ $kustom->spesifikasi_khusus }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="border border-gray-200 rounded-xl p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between bg-white shadow-sm gap-4">

                                <div class="flex flex-col gap-1.5">
                                    <h2 class="font-bold text-sm md:text-base">Section {{ $kustom->spesifikasi_khusus }}</h2>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach($kustom->produk->detailProduks->filter(fn($d) => $d->pilihanDetails->isNotEmpty()) as $detail)
                                            <span data-cy="kustom-item"
                                                class="text-[10px] md:text-xs bg-gray-100 border border-gray-200 rounded-full px-2.5 py-0.5 text-gray-600">
                                                {{ $detail->nama_detail }}
                                            </span>
                                        @endforeach
                                        @if($kustom->produk->detailProduks->isEmpty())
                                            <span class="text-xs text-gray-400 italic">Belum ada aspek</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 border-t md:border-t-0 pt-3 md:pt-0">
                                    <a data-cy="btn-edit-kustom" href="{{ route('manage.kustom.edit', $kustom->kustom_id) }}"
                                        class="flex-1 md:flex-none text-center px-4 py-2 text-black font-bold rounded-lg hover:bg-yellow-500 transition text-sm">
                                        Edit
                                    </a>

                                    <button data-cy="btn-delete-kustom" type="button" @click="$dispatch('open-delete-modal', { 
                                                        url: '{{ route('manage.katalog.destroy', $kustom->kustom_id) }}', 
                                                        message: 'Apa Anda yakin ingin menghapus section {{ $kustom->spesifikasi_khusus }}?'
                                                    })"
                                        class="flex-1 md:flex-none px-4 py-2 font-bold rounded-lg hover:bg-red-700 hover:text-white transition text-sm text-red-600 md:text-black">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>
@endsection