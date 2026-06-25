@extends('layouts.user.layout')
@section('title', 'Edit - Voucher')
@section('content')
    <x-shared.notification />
    <div class="px-4 md:px-6 pb-12">
        <a data-cy="back-to-voucher-page" href="{{ route('manage.voucher') }}"
            class="flex items-center gap-4 text-sm mb-6 md:mb-8 w-fit hover:opacity-75 transition">
            <svg width="24" height="24" viewBox="0 0 41 41" fill="none" class="md:w-[30px] md:h-[30px]">
                <path
                    d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z"
                    fill="#323232" />
            </svg>
            <h1 class="font-roboto font-bold text-xl md:text-2xl text-[#323232]">Edit Voucher</h1>
        </a>

        <form x-data="{ jenis: '{{ old('jenis_voucher', $voucher->jenis_voucher ?? 'persentase') }}' }"
            data-cy="edit-voucher-form" action="{{ route('manage.voucher.update', $voucher->id) }}" method="POST"
            class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-28">
            @csrf
            @method('PUT')

            <div class="lg:col-span-7 space-y-6">

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900 text-sm md:text-base">Deskripsi Voucher</label>
                    <div class="relative">
                        <textarea data-cy="voucher-description-input" name="deskripsi" rows="5"
                            placeholder="Tambahkan Deskripsi Syarat & Ketentuan"
                            class="w-full border border-black rounded-md p-4 text-sm md:text-base placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black resize-none"
                            required>{{ old('deskripsi', $voucher->deskripsi) }}</textarea>
                        <span class="absolute top-4 right-4 text-red-500 text-xl">*</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="font-bold text-gray-900 text-sm md:text-base">Tanggal Mulai</label>
                        <div class="relative">
                            <input data-cy="voucher-start-date-input" type="date" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($voucher->tanggal_mulai)->format('Y-m-d')) }}"
                                class="w-full border border-black rounded-md px-4 py-3 text-sm md:text-base text-black focus:outline-none focus:ring-1 focus:ring-black"
                                required>
                            <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-bold text-gray-900 text-sm md:text-base">Tanggal Berakhir</label>
                        <div class="relative">
                            <input data-cy="voucher-end-date-input" type="date" name="tanggal_berakhir"
                                value="{{ old('tanggal_berakhir', \Carbon\Carbon::parse($voucher->tanggal_berakhir)->format('Y-m-d')) }}"
                                class="w-full border border-black rounded-md px-4 py-3 text-sm md:text-base text-black focus:outline-none focus:ring-1 focus:ring-black"
                                required>
                            <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900 text-sm md:text-base">Jenis Voucher</label>
                    <div class="relative">
                        <select x-model="jenis" data-cy="voucher-type-select" name="jenis_voucher"
                            class="w-full border border-black rounded-md px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-black appearance-none bg-white"
                            required>
                            <option data-cy="voucher-type-nominal" value="nominal" {{ old('jenis_voucher', $voucher->jenis_voucher) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                            <option data-cy="voucher-type-persentase" value="persentase" {{ old('jenis_voucher', $voucher->jenis_voucher) == 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-5 space-y-6 md:space-y-10">
                <div class="flex flex-col gap-5">

                    <div class="flex flex-col gap-2">
                        <label class="font-bold text-gray-900 text-sm md:text-base">Nama Voucher</label>
                        <div class="relative">
                            <input type="text" data-cy="voucher-name-input" name="nama_voucher"
                                value="{{ old('nama_voucher', $voucher->nama_voucher) }}"
                                placeholder="Contoh: Promo Akhir Tahun"
                                class="w-full border border-black rounded-md px-4 py-3 text-base md:text-lg font-bold placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-black"
                                required>
                            <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-bold text-gray-900 text-sm md:text-base">Kode Voucher <br
                                class="sm:hidden" /><span class="text-xs md:text-sm font-normal text-gray-500">(Kosongkan
                                untuk generate otomatis)</span></label>
                        <div class="relative">
                            <input type="text" data-cy="voucher-code-input" name="kode_voucher"
                                value="{{ old('kode_voucher', $voucher->kode_voucher) }}" placeholder="KODE-CUSTOM"
                                class="w-full border border-black rounded-md px-4 py-3 text-base md:text-lg font-mono uppercase placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                        @error('kode_voucher')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2 mt-2">
                        <label class="font-bold text-gray-900 text-sm md:text-base">Nilai Diskon</label>
                        <div class="relative flex items-center">

                            <span x-show="jenis === 'nominal'" x-cloak style="display: none;"
                                class="absolute left-4 text-xl md:text-3xl font-medium text-gray-500">Rp</span>

                            <input data-cy="voucher-discount-input"
                                onkeydown="if(event.key === '-') event.preventDefault();" type="number" name="nilai_diskon"
                                value="{{ old('nilai_diskon', $voucher->nilai_diskon ?? '') }}" placeholder="0"
                                :max="jenis === 'persentase' ? 100 : null"
                                class="w-full border border-black rounded-md py-3 text-2xl md:text-4xl font-normal placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-black"
                                :class="jenis === 'nominal' ? 'pl-16 pr-8' : 'pl-4 pr-12'" required>

                            <span x-show="jenis === 'persentase'" x-cloak style="display: none;"
                                class="absolute right-8 text-xl md:text-3xl font-medium text-gray-500">%</span>

                            <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                        </div>
                    </div>

                </div>

                <button type="submit" id="submitVoucher" data-cy="btn-submit-voucher"
                    class="w-full bg-[#323232] text-white py-4 rounded-md font-bold text-base md:text-lg hover:opacity-90 transition shadow-lg mt-4 lg:mt-8">
                    Perbarui Voucher
                </button>
            </div>
        </form>
    </div>
@endsection