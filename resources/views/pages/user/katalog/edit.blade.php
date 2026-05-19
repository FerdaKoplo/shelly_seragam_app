@extends('layouts.user.layout')
@section('title', 'Edit - Katalog')

@section('content')
    <x-shared.notification />
    <div class="px-4 md:px-6 pb-12" x-data="{
            existingImages: {{ Js::from($katalog->fotos) }}, 
            newImages: [],
            deletedPhotoIds: [],
            variations: {{ Js::from($existingVariations) }},

            handleImageUpload(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        this.newImages.push({
                            file: files[i],
                            url: URL.createObjectURL(files[i])
                        });
                    }
                }
            },
            removeNewImage(index) {
                this.newImages.splice(index, 1);
            },
            removeExistingImage(index, id) {
                this.deletedPhotoIds.push(id);
                this.existingImages.splice(index, 1);
            },
            addVariation(type, data) {
                this.variations.push({ type: type, ...data });
            },
            removeVariation(index) {
                this.variations.splice(index, 1);
            }
        }" @add-size.window="addVariation('ukuran', $event.detail)"
        @add-color.window="addVariation('warna', $event.detail)">

        <a href="{{ route('manage.katalog') }}"
            class="flex items-center gap-4 text-sm mb-6 md:mb-8 w-fit hover:opacity-75 transition">
            <svg width="24" height="24" viewBox="0 0 41 41" fill="none" class="md:w-[30px] md:h-[30px]">
                <path
                    d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z"
                    fill="#323232" />
            </svg>
            <h1 class="font-roboto font-bold text-xl md:text-2xl text-[#323232]">Edit Produk</h1>
        </a>

        <form action="{{ route('manage.katalog.update', $katalog->produk_id) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-20">
            @csrf
            @method('PUT')

            <template x-for="(v, index) in variations" :key="index">
                <div style="display: none;">
                    <input type="hidden" :name="`variations[${index}][type]`" :value="v.type">
                    <input type="hidden" :name="`variations[${index}][data]`" :value="JSON.stringify(v)">
                </div>
            </template>

            <template x-for="id in deletedPhotoIds">
                <div style="display: none;">
                    <input type="hidden" name="deleted_photos[]" :value="id">
                </div>
            </template>

            <div class="lg:col-span-7 space-y-6">

                <div class="flex flex-col-reverse md:flex-row gap-4 h-auto md:h-[500px] lg:h-[600px]">

                    <div
                        class="w-full md:w-28 flex flex-row md:flex-col gap-3 overflow-x-auto md:overflow-y-auto pb-2 md:pb-0">
                        <template x-for="(img, index) in existingImages" :key="'db-'+img.id">
                            <div
                                class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 border-2 border-blue-200 rounded-md overflow-hidden relative group">
                                <img :src="`{{ asset('storage') }}/${img.path}`" class="w-full h-full object-cover">
                                <button type="button" @click="removeExistingImage(index, img.id)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 transition">
                                    <svg width="12" height="12" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <template x-for="(img, index) in newImages" :key="'new-'+index">
                            <div
                                class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 border-2 border-green-200 rounded-md overflow-hidden relative group">
                                <img :src="img.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeNewImage(index)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 transition">
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

                    <div
                        class="flex-1 border-2 border-dashed border-[#D9D9D9] rounded-md relative flex flex-col items-center justify-center bg-white overflow-hidden min-h-[300px]">
                        <input type="file" name="fotos[]" multiple class="hidden" x-ref="fileInput"
                            @change="handleImageUpload">

                        <template x-if="newImages.length > 0">
                            <img :src="newImages[newImages.length - 1].url" class="w-full h-full object-contain">
                        </template>

                        <template x-if="newImages.length === 0 && existingImages.length > 0">
                            <img :src="`{{ asset('storage') }}/${existingImages[0].path}`" class="w-full h-full object-contain">
                        </template>

                        <template x-if="newImages.length === 0 && existingImages.length === 0">
                            <p class="text-gray-400 font-medium">Belum ada gambar</p>
                        </template>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <textarea name="deskripsi" rows="6"
                        class="w-full border border-black rounded-md p-4 focus:outline-none focus:ring-1 focus:ring-black resize-none"
                        required>{{ $katalog->produk->deskripsi }}</textarea>
                    <input type="text" name="kategori" value="{{ $katalog->kategori }}"
                        class="w-full border border-black rounded-md p-3 text-sm focus:outline-none focus:ring-1 focus:ring-black"
                        required>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-8">
                <div class="flex flex-col gap-3">
                    <input type="text" name="nama_produk" value="{{ $katalog->produk->nama_produk }}"
                        class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold focus:outline-none focus:ring-1 focus:ring-black"
                        required>
                    <input type="number" name="harga" value="{{ $katalog->harga }}"
                        class="w-full border border-black rounded-md px-4 py-3 text-2xl md:text-4xl focus:outline-none focus:ring-1 focus:ring-black"
                        required>
                </div>

                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Stok</label>
                        <input type="number" name="stok" value="{{ $katalog->stok }}"
                            class="w-full border border-black rounded-md px-4 py-2 focus:outline-none focus:ring-1 focus:ring-black"
                            required>
                    </div>

                    {{-- Size Variations --}}
                    <div class="border-2 border-gray-400 rounded-md p-4 bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg">Ukuran <span class="text-red-500">*</span></h3>
                            <button type="button"
                                class="text-[10px] md:text-xs flex items-center gap-1 text-gray-600 hover:text-black">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M2 12h20M2 12l4 4m-4-4l4-4" />
                                </svg>
                                Panduan
                            </button>
                        </div>

                        <div x-show="variations.filter(i => i.type === 'ukuran').length > 0" data-cy="size-item"
                            class="flex flex-wrap gap-2 mb-4">
                            <template x-for="(v, i) in variations" :key="i">
                                <template x-if="v.type === 'ukuran'">
                                    <div
                                        class="bg-gray-100 border border-gray-300 px-3 py-1.5 rounded-md text-xs md:text-sm flex items-center gap-2">
                                        <span class="font-medium" x-text="v.name"></span>
                                        <span class="text-xs text-gray-500" x-text="`(${v.chest}/${v.sleeve})`"></span>
                                        <button type="button" @click="removeVariation(variations.indexOf(v))"
                                            class="text-red-500 font-bold">×</button>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <button type="button" @click="$dispatch('open-modal', 'modal-size-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            <span
                                x-text="variations.filter(i => i.type === 'ukuran').length > 0 ? 'Edit Variasi Ukuran' : 'Tambahkan Variasi Ukuran'"></span>
                        </button>
                    </div>

                    {{-- Color Variations --}}
                    <div class="border-2 border-gray-400 rounded-md p-4 bg-white">
                        <h3 class="font-bold text-lg mb-4">Varian Warna</h3>
                        <div x-show="variations.filter(i => i.type === 'warna').length > 0"
                            class="flex flex-wrap gap-2 mb-4">
                            <template x-for="(v, i) in variations" :key="i">
                                <template x-if="v.type === 'warna'">
                                    <div
                                        class="flex items-center gap-2 bg-gray-50 border border-gray-200 pr-2 pl-1 py-1 rounded-full">
                                        <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm"
                                            :style="`background-color: ${v.hex}`"></div>
                                        <span class="text-xs font-mono text-gray-600" x-text="v.hex"></span>
                                        <button type="button" @click="removeVariation(variations.indexOf(v))"
                                            class="text-red-500 font-bold">×</button>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <button type="button" @click="$dispatch('open-modal', 'modal-color-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            <span
                                x-text="variations.filter(i => i.type === 'warna').length > 0 ? 'Edit Variasi Warna' : 'Tambahkan Variasi Warna'"></span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit"
                        class="w-full bg-[#323232] text-white py-4 rounded-md font-bold hover:opacity-90 transition shadow-lg">
                        Simpan Perubahan
                    </button>
                    <button type="button" data-cy="btn-archive-produk-edit"
                        class="w-full border border-black py-4 rounded-md font-bold hover:bg-gray-50 transition">
                        Arsip Produk
                    </button>
                </div>
            </div>
        </form>
    </div>

    <x-user.katalog.modals.variasi_ukuran />
    <x-user.katalog.modals.variasi_warna />
@endsection