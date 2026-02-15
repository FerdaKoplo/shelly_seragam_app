@extends('layouts.user.layout')
@section('title', 'Edit - Katalog')

@section('content')
    <div class="px-6 pb-12 " x-data="{
                            // 1. Initialize with DB data
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

                            // Remove NEW image (not yet in DB)
                            removeNewImage(index) {
                                this.newImages.splice(index, 1);
                            },

                            // Remove EXISTING image (track ID to delete in backend)
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
            class="flex items-center gap-4 text-sm mb-8 w-fit hover:opacity-75 transition">
            <svg width="30" height="30" viewBox="0 0 41 41" fill="none">
                <path
                    d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z"
                    fill="#323232" />
            </svg>
            <h1 class="font-roboto font-bold text-2xl text-[#323232]">Edit Produk</h1>
        </a>

        <form action="{{ route('manage.katalog.update', $katalog->produk_id) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-28">
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

                <div class="flex gap-3 h-[600px]">

                    <div class="w-28 flex flex-col gap-5 overflow-y-auto">
                        <template x-for="(img, index) in existingImages" :key="'db-'+img.id">
                            <div
                                class="w-28 h-28 flex-shrink-0 border-2 border-blue-200 rounded-md overflow-hidden relative group">
                                <img :src="`{{ asset('storage') }}/${img.path}`" class="w-full h-full object-cover">
                                {{-- <button type="button" @click="removeExistingImage(index, img.id)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">×</button>
                                --}}

                                <button type="button" @click="removeExistingImage(index, img.id)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg width="12" height="12" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <template x-for="(img, index) in newImages" :key="'new-'+index">
                            <div
                                class="w-28 h-28 flex-shrink-0 border-2 border-green-200 rounded-md overflow-hidden relative group">
                                <img :src="img.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeNewImage(index)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">×</button>
                            </div>
                        </template>

                        <div @click="$refs.fileInput.click()"
                            class="w-28 h-20 flex-shrink-0 border-2 border-[#D9D9D9] rounded-md flex items-center justify-center cursor-pointer hover:bg-gray-50 transition">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="#323232" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>

                    <div
                        class="flex-1 border-2 border-dashed border-[#D9D9D9] rounded-md relative flex flex-col items-center justify-center bg-white overflow-hidden">
                        <input type="file" name="fotos[]" multiple class="hidden" x-ref="fileInput"
                            @change="handleImageUpload">

                        <template x-if="newImages.length > 0">
                            <img :src="newImages[newImages.length - 1].url" class="w-full h-full object-contain">
                        </template>

                        <template x-if="newImages.length === 0 && existingImages.length > 0">
                            <img :src="`{{ asset('storage') }}/${existingImages[0].path}`"
                                class="w-full h-full object-contain">
                        </template>

                        <template x-if="newImages.length === 0 && existingImages.length === 0">
                            <p class="text-gray-400">Belum ada gambar</p>
                        </template>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="relative">
                        <textarea name="deskripsi" rows="6" class="w-full border border-black rounded-md p-4"
                            required>{{ $katalog->produk->deskripsi }}</textarea>
                    </div>
                    <div>
                        <input type="text" name="kategori" value="{{ $katalog->kategori }}"
                            class="w-full border border-black rounded-md p-3" required>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-10">
                <div class="flex flex-col gap-3">
                    <div class="relative">
                        <input type="text" name="nama_produk" value="{{ $katalog->produk->nama_produk }}"
                            class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold" required>
                    </div>
                    <div class="relative">
                        <input type="number" name="harga" value="{{ $katalog->harga }}"
                            class="w-full border border-black rounded-md px-4 py-3 text-4xl" required>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div>
                        <input type="number" name="stok" value="{{ $katalog->stok }}"
                            class="w-full border border-black rounded-md px-4 py-2" required>
                    </div>

                    <div class="border border-gray-400 rounded-md p-4 relative bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex gap-1">Ukuran <span class="text-red-500">*</span></h3>
                            <button type="button" class="text-xs flex items-center gap-2 text-gray-600 hover:text-black">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path d="M2 12h20M2 12l4 4m-4-4l4-4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Panduan Ukuran
                            </button>
                        </div>

                        <div x-show="variations.filter(i => i.type === 'ukuran').length > 0" class="mb-3">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(v, i) in variations" :key="i">
                                    <template x-if="v.type === 'ukuran'">
                                        <div
                                            class="bg-gray-100 border border-gray-300 px-3 py-1.5 rounded-md text-sm flex items-center gap-2">
                                            <span class="font-medium" x-text="v.name"></span>
                                            <span class="text-xs text-gray-500" x-text="`(${v.chest}/${v.sleeve})`"></span>
                                            <button type="button" @click="removeVariation(variations.indexOf(v))"
                                                class="text-gray-400 hover:text-red-500 ml-1">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="3">
                                                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>


                        <button type="button" @click="$dispatch('open-modal', 'modal-size-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition"
                            :class="variations.filter(i => i.type === 'ukuran').length > 0 ? 'mt-2' : ''">
                            <span
                                x-text="variations.filter(i => i.type === 'ukuran').length > 0 ? 'Edit Variasi Ukuran' : 'Tambahkan Variasi Ukuran'"></span>
                        </button>
                    </div>

                    <div class="border border-gray-400 rounded-md p-4 relative bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg">Varian Warna</h3>
                        </div>

                        <div x-show="variations.filter(i => i.type === 'warna').length > 0" class="mb-3">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(v, i) in variations" :key="i">
                                    <template x-if="v.type === 'warna'">
                                        <div
                                            class="flex items-center gap-2 bg-gray-50 border border-gray-200 pr-2 pl-1 py-1 rounded-full overflow-hidden">
                                            <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm"
                                                :style="`background-color: ${v.hex}`"></div>
                                            <span class="text-xs font-mono text-gray-600" x-text="v.hex"></span>
                                            <button type="button" @click="removeVariation(variations.indexOf(v))"
                                                class="text-gray-400 hover:text-red-500 ml-1">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="3">
                                                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>

                        <button type="button" @click="$dispatch('open-modal', 'modal-color-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition"
                            :class="variations.filter(i => i.type === 'warna').length > 0 ? 'mt-2' : ''">
                            <span
                                x-text="variations.filter(i => i.type === 'warna').length > 0 ? 'Edit Variasi Warna' : 'Tambahkan Variasi Warna'"></span>
                        </button>
                    </div>
                </div>


                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="w-full bg-[#323232] text-white py-3 rounded-md font-medium hover:opacity-90 transition">
                        Simpan Perubahan
                    </button>
                    {{-- placeholder for arsip --}}
                    <button type="button"
                        class="w-full border border-black py-3 rounded-md font-medium hover:bg-gray-50 transition">
                        Arsipkan Produk
                    </button>
                </div>
            </div>
        </form>

        <x-user.katalog.modals.variasi_ukuran />
        <x-user.katalog.modals.variasi_warna />
    </div>
@endsection