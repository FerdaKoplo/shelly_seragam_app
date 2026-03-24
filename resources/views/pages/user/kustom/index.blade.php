@extends('layouts.user.layout')
@section('title', 'Manajemen Produk Kustom')

@section('content')
    <div class="flex justify-start mt-6 px-10 pb-20">
        <div class="w-full flex flex-col gap-6">

            <h1 class="text-[28px] font-bold text-black">Produk Kustomisasi</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $sections = $kustoms->pluck('spesifikasi_khusus')->unique()->values()->toArray();
            @endphp

            <div class=" flex items-center gap-3 flex-wrap"
                x-data="{ active: '{{ $sections[0] ?? '' }}' }">

                <div class="border border-black  py-3 rounded-2xl px-4 gap-4 flex pr-12">
                    <a href="{{ route('manage.kustom.create') }}"
                        class="inline-flex items-center py-1.5 px-14 bg-white border border-dashed border-black text-black rounded-lg font-medium text-lg hover:bg-gray-50 transition">
                        +
                    </a>

                    @foreach($sections as $sec)
                        <button type="button" @click="active = '{{ $sec }}'" :class="active === '{{ $sec }}'
                                                ? 'bg-black text-white border-black'
                                                : 'bg-white text-black border-gray-300 hover:border-black'"
                            class="px-10 py-1.5 border rounded-lg text-lg font-medium transition-colors">
                            {{ $sec }}
                        </button>
                    @endforeach
                </div>

                {{-- List filtered by active tab --}}
                @if($kustoms->isEmpty())
                    <div class="w-full flex items-center justify-center py-24">
                        <p class="text-xl text-gray-800">Belum Ada Data Aspek Kustomisasi</p>
                    </div>
                @else
                    <div class="w-full flex flex-col gap-3 mt-2">
                        @foreach($kustoms as $kustom)
                            <div x-show="active === '{{ $kustom->spesifikasi_khusus }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="border border-gray-200 rounded-xl p-5 flex items-center justify-between bg-white shadow-sm">

                                <div class="flex flex-col gap-1.5">
                                    <h2 class="font-bold text-base">Section {{ $kustom->spesifikasi_khusus }}</h2>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach($kustom->produk->detailProduks->filter(fn($d) => $d->pilihanDetails->isNotEmpty()) as $detail)
                                            <span
                                                class="text-xs bg-gray-100 border border-gray-200 rounded-full px-2.5 py-0.5 text-gray-600">
                                                {{ $detail->nama_detail }}
                                            </span>
                                        @endforeach
                                        @if($kustom->produk->detailProduks->isEmpty())
                                            <span class="text-xs text-gray-400 italic">Belum ada aspek</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('manage.kustom.edit', $kustom->kustom_id) }}"
                                        class="px-4 py-2 text-black font-bold rounded-lg hover:bg-yellow-500 transition text-sm">
                                        Edit
                                    </a>

                                    <button type="button" @click="$dispatch('open-delete-modal', { 
                                                                            url: '{{ route('manage.kustom.destroy', $kustom->kustom_id) }}', 
                                                                            message: 'Apa Anda yakin ingin menghapus section {{ $kustom->spesifikasi_khusus }}?'
                                                                        })"
                                        class="px-4 py-2  font-bold rounded-lg hover:bg-red-700 transition text-sm">
                                        Hapus
                                    </button>
                                    {{-- <form action="{{ route('manage.kustom.destroy', $kustom->kustom_id) }}" method="POST"
                                        onsubmit="return confirm('Hapus section {{ $kustom->spesifikasi_khusus }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition text-sm">
                                            Hapus
                                        </button>
                                    </form> --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection