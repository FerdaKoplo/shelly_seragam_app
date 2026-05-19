@extends('layouts.user.layout')
@section('title', 'Create - Voucher')

@section('content')
<x-shared.notification />
<div class="px-6 pb-12">
    <a href="{{ route('manage.voucher') }}"
        class="flex items-center gap-4 text-sm mb-8 w-fit hover:opacity-75 transition">
        <svg width="30" height="30" viewBox="0 0 41 41" fill="none">
            <path d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z" fill="#323232" />
        </svg>
        <h1 class="font-roboto font-bold text-2xl text-[#323232]">Tambahkan Voucher Baru</h1>
    </a>

    <form data-cy="create-voucher-form" action="{{ route('manage.voucher.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-28">
        @csrf
        {{-- Left Column (7 Spans) --}}
        <div class="lg:col-span-7 space-y-6">

            <div class="flex flex-col gap-2">
                <label class="font-bold text-gray-900">Deskripsi Voucher</label>
                <div class="relative">
                    <textarea name="deskripsi" rows="5" placeholder="Tambahkan Deskripsi Syarat & Ketentuan"
                        data-cy="voucher-description-input" class="w-full border border-black rounded-md p-4 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black resize-none"
                        required>{{ old('deskripsi') }}</textarea>
                    <span class="absolute top-4 right-4 text-red-500 text-xl">*</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900">Tanggal Mulai</label>
                    <div class="relative">
                        <input  data-cy="voucher-start-date-input" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                            class="w-full border border-black rounded-md px-4 py-3 text-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900">Tanggal Berakhir</label>
                    <div class="relative">
                        <input  data-cy="voucher-end-date-input" type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}"
                            class="w-full border border-black rounded-md px-4 py-3 text-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-bold text-gray-900">Jenis Voucher</label>
                <div class="relative">
                    <select data-cy="voucher-type-select" name="jenis_voucher" class="w-full border border-black rounded-md px-4 py-3 focus:outline-none focus:ring-1 focus:ring-black appearance-none" required>
                        <option value="" disabled selected>Pilih Jenis Diskon</option>
                        <option data-cy="voucher-type-nominal" value="nominal" {{ old('jenis_voucher') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option data-cy="voucher-type-persentase" value="persentase" {{ old('jenis_voucher') == 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column (5 Spans) --}}
        <div class="lg:col-span-5 space-y-10">
            <div class="flex flex-col gap-5">

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900">Nama Voucher</label>
                    <div class="relative">
                        <input data-cy="voucher-name-input" type="text" name="nama_voucher" value="{{ old('nama_voucher') }}" placeholder="Contoh: Promo Akhir Tahun"
                            class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900">Kode Voucher <span class="text-sm font-normal text-gray-500">(Kosongkan untuk generate otomatis)</span></label>
                    <div class="relative">
                        <input type="text" data-cy="voucher-code-input" name="kode_voucher" value="{{ old('kode_voucher') }}" placeholder="KODE-CUSTOM"
                            class="w-full border border-black rounded-md px-4 py-3 text-lg font-mono uppercase placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                    @error('kode_voucher')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    <label class="font-bold text-gray-900">Nilai Diskon</label>
                    <div class="relative">
                        <input data-cy="voucher-discount-input" onkeydown="if(event.key === '-') event.preventDefault();" type="number" name="nilai_diskon" value="{{ old('nilai_diskon') }}" placeholder="0"
                            class="w-full border border-black placeholder:text-gray-400 rounded-md px-4 py-3 text-4xl font-normal placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>

            </div>

            <button type="submit" id="submitVoucher" data-cy="btn-submit-voucher"
                class="w-full bg-[#323232] text-white py-4 rounded-md font-bold text-lg hover:opacity-90 transition shadow-lg mt-8">
                Tambahkan Voucher
            </button>
        </div>
    </form>
</div>
@endsection