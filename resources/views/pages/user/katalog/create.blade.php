@extends('layouts.user.layout')
@section('title', 'Create - Katalog')

@section('content')
    <x-shared.notification />
    <div class="px-4 md:px-6 pb-12" x-data="{
        images: [],
        variations: [],

        handleImageUpload(event) {
            const files = event.target.files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    this.images.push({
                        file: files[i],
                        url: URL.createObjectURL(files[i])
                    });
                }
            }
        },
        removeImage(index) {
            this.images.splice(index, 1);
        },

        addVariation(type, data) {
            this.variations.push({ type: type, ...data });
        },
        removeVariation(index) {
            this.variations.splice(index, 1);
        }
    }" @add-size.window="addVariation('ukuran', $event.detail)" @add-color.window="addVariation('warna', $event.detail)">

        {{-- Header --}}
        <a href="{{ route('manage.katalog') }}"
            class="flex items-center gap-4 text-sm mb-6 md:mb-8 w-fit hover:opacity-75 transition">
            <svg width="24" height="24" viewBox="0 0 41 41" fill="none" class="md:w-[30px] md:h-[30px]">
                <path
                    d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z"
                    fill="#323232" />
            </svg>
            <h1 class="font-roboto font-bold text-xl md:text-2xl text-[#323232]">Tambahkan Produk Baru</h1>
        </a>

        <form action="{{ route('manage.katalog.store') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-20">
            @csrf

            <template x-for="(v, index) in variations" :key="index">
                <div style="display: none;">
                    <input type="hidden" :name="`variations[${index}][type]`" :value="v.type">
                    <input type="hidden" :name="`variations[${index}][data]`" :value="JSON.stringify(v)">
                </div>
            </template>

            {{-- LEFT COLUMN: Images & Description --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Image Gallery Section --}}
                <div class="flex flex-col-reverse md:flex-row gap-4 h-auto md:h-[500px] lg:h-[600px]">
                    {{-- Thumbnails --}}
                    <div
                        class="w-full md:w-28 flex flex-row md:flex-col gap-3 overflow-x-auto md:overflow-y-auto pb-2 md:pb-0">
                        <template x-for="(img, index) in images" :key="index">
                            <div
                                class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 border-2 border-gray-200 rounded-md overflow-hidden relative group">
                                <img :src="img.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(index)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 md:opacity-0 md:group-hover:opacity-100 transition">
                                    <svg width="12" height="12" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <div @click="$refs.fileInput.click()"
                            class="w-20 h-20 md:w-28 md:h-20 flex-shrink-0 border-2 border-[#D9D9D9] rounded-md flex items-center justify-center cursor-pointer hover:bg-gray-50 transition">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="#323232" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>

                    {{-- Main Preview --}}
                    <div
                        class="flex-1 border-2 border-dashed border-[#D9D9D9] rounded-md relative flex flex-col items-center justify-center bg-white overflow-hidden min-h-[300px]">
                        <input type="file" name="fotos[]" multiple class="hidden" x-ref="fileInput"
                            @change="handleImageUpload">

                        <template x-if="images.length > 0">
                            <img :src="images[images.length - 1].url" class="w-full h-full object-contain">
                        </template>

                        <template x-if="images.length === 0">
                            <div class="flex flex-col items-center p-4 text-center">
                                <div class="relative">
                                    <svg class="w-32 h-32 md:w-64 md:h-64 text-gray-200" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 5V19M5 12H19" stroke="#323232" stroke-width="3"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-sm font-medium mt-2">Tambahkan Minimal 1 Gambar</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Description & Category --}}
                <div class="flex flex-col gap-4 mt-4">
                    <div class="relative">
                        <textarea name="deskripsi" rows="6" placeholder="Tambahkan Deskripsi"
                            class="w-full border border-black rounded-md p-4 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black resize-none"
                            required></textarea>
                        <span class="absolute top-4 right-4 text-red-500 text-xl">*</span>
                    </div>
                    <div>
                        <input type="text" name="kategori" placeholder="Ketik Untuk Menambahkan Kategori......"
                            class="w-full border border-black rounded-md p-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Price, Stock, Variations --}}
            <div class="lg:col-span-5 space-y-6 md:space-y-10">
                <div class="flex flex-col gap-3">
                    <div class="relative">
                        <input type="text" name="nama_produk" placeholder="Nama Produk"
                            class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="harga" placeholder="Harga Produk"
                            class="w-full border border-black rounded-md px-4 py-3 text-2xl md:text-4xl font-normal placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Stok</label>
                        <input type="number" name="stok" placeholder="Stock"
                            class="w-full border border-black rounded-md px-4 py-2 placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                    </div>

                    {{-- Size Variation --}}
                    <div class="border-2 border-gray-400 rounded-md p-4 relative bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex gap-1">Ukuran <span class="text-red-500">*</span></h3>
                            <button type="button"
                                class="text-[10px] md:text-xs flex items-center gap-1 md:gap-2 text-gray-600 hover:text-black">
                                <svg width="18" height="18" viewBox="0 0 21 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.375 5.25H2.625C1.6625 5.25 0.875 6.0375 0.875 7V14C0.875 14.9625 1.6625 15.75 2.625 15.75H18.375C19.3375 15.75 20.125 14.9625 20.125 14V7C20.125 6.0375 19.3375 5.25 18.375 5.25ZM18.375 14H2.625V7H4.375V10.5H6.125V7H7.875V10.5H9.625V7H11.375V10.5H13.125V7H14.875V10.5H16.625V7H18.375V14Z"
                                        fill="#323232" />
                                </svg>
                                Panduan Ukuran
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-3">
                            <template x-for="(v, i) in variations.filter(item => item.type === 'ukuran')" :key="i">
                                <div data-cy="size-item"
                                    class="bg-gray-100 border border-gray-300 px-3 py-1 rounded-full text-xs md:text-sm flex items-center gap-2">
                                    <span x-text="v.name"></span>
                                    <span class="text-xs text-gray-500" x-text="`(${v.chest}/${v.sleeve})`"></span>
                                    <button type="button" @click="removeVariation(variations.indexOf(v))"
                                        class="text-red-500 hover:text-red-700 font-bold">×</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" data-cy="open-size-modal" @click="$dispatch('open-modal', 'modal-size-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            Tambahkan Variasi Ukuran
                        </button>
                    </div>

                    {{-- Color Variation --}}
                    <div class="border-2 border-gray-400 rounded-md p-4 relative bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg">Varian Warna</h3>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-3">
                            <template x-for="(v, i) in variations.filter(item => item.type === 'warna')" :key="i">
                                <div
                                    class="flex items-center gap-2 bg-gray-50 border border-gray-200 pr-2 rounded-full overflow-hidden">
                                    <div class="w-8 h-8" :style="`background-color: ${v.hex}`"></div>
                                    <span class="text-xs font-mono" x-text="v.hex"></span>
                                    <button type="button" @click="removeVariation(variations.indexOf(v))"
                                        class="text-gray-400 hover:text-red-500 font-bold px-1">×</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="$dispatch('open-modal', 'modal-color-variation')" data-cy="open-color-modal"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            Tambahkan Variasi Warna
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitProduk"
                    class="w-full bg-[#323232] text-white py-4 rounded-md font-bold hover:opacity-90 transition shadow-lg mt-6">
                    Tambahkan Produk
                </button>
            </div>
        </form>
    </div>

    <x-user.katalog.modals.variasi_ukuran />
    <x-user.katalog.modals.variasi_warna />
@endsection