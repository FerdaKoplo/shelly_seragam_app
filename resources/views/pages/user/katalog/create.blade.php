@extends('layouts.user.layout')
@section('title', 'Create - Katalog')
@section('content')
    <div class="px-6 pb-12" x-data>
        
        <a href="{{ route('manage.katalog') }}"
            class="flex items-center gap-4 text-sm mb-8 w-fit hover:opacity-75 transition">
            <svg width="30" height="30" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_936_3036)">
                    <path
                        d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z"
                        fill="#323232" />
                </g>
                <defs>
                    <clipPath id="clip0_936_3036">
                        <rect width="41" height="41" fill="white" />
                    </clipPath>
                </defs>
            </svg>
            <h1 class="font-roboto font-bold text-2xl text-[#323232]">
                Tambahkan Produk Baru
            </h1>
        </a>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-28">
            @csrf

            <div class="lg:col-span-7 space-y-6">
                <div class="flex gap-2 h-[600px]">
                    <div class="w-28 h-[4.5rem] flex-shrink-0 border-2 border-[#D9D9D9] rounded-md flex items-center justify-center cursor-pointer transition">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="#323232" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="flex-1 border-2 border-dashed border-[#D9D9D9] rounded-md relative flex flex-col items-center justify-center bg-white">
                        <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-black">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="flex items-center relative">
                            <svg width="320" height="220" viewBox="0 0 24 24" fill="currentColor" class="text-black">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z" />
                            </svg>
                            <div class="absolute right-0 top-10">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-black">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </div>
                        </div>
                        <p class="absolute bottom-4 text-sm text-gray-600 font-medium">Tambahkan Minimal 1 Gambar</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="font-medium text-[#323232]">Terjual :</label>
                    <input type="number" value="0" class="border border-black rounded px-2 py-1 w-20 text-center focus:outline-none">
                </div>

                <div class="flex flex-col gap-2">
                    <div class="relative">
                        <textarea name="deskripsi" rows="6" placeholder="Tambahkan Deskripsi" class="w-full border border-black rounded-md p-4 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black resize-none"></textarea>
                        <span class="absolute top-4 right-4 text-red-500 text-xl">*</span>
                    </div>
                    <div>
                        <input type="text" placeholder="Ketik Untuk Menambahkan Kategori......" class="w-full border border-black rounded-md p-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-28">
                <div class="flex flex-col gap-3">
                    <div class="relative">
                        <input type="text" name="nama_produk" placeholder="Nama Produk" class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold placeholder-black focus:outline-none focus:ring-1 focus:ring-black">
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="harga" placeholder="Harga Produk" class="w-full border border-black rounded-md px-4 py-3 text-4xl font-normal placeholder-black focus:outline-none focus:ring-1 focus:ring-black">
                        <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div>
                        <input type="number" name="stok" placeholder="Stock" class="w-full border border-black rounded-md px-4 py-2 placeholder-black focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="border-2 border-gray-400 rounded-md p-4 relative bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex gap-1">Ukuran <span class="text-red-500">*</span></h3>
                            
                            <button 
                                type="button" 
                                @click="$dispatch('open-modal', 'modal-size-guide')" 
                                class="text-xs flex items-center gap-2 text-gray-600 hover:text-black">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_936_3015)">
                                        <path d="M18.375 5.25H2.625C1.6625 5.25 0.875 6.0375 0.875 7V14C0.875 14.9625 1.6625 15.75 2.625 15.75H18.375C19.3375 15.75 20.125 14.9625 20.125 14V7C20.125 6.0375 19.3375 5.25 18.375 5.25ZM18.375 14H2.625V7H4.375V10.5H6.125V7H7.875V10.5H9.625V7H11.375V10.5H13.125V7H14.875V10.5H16.625V7H18.375V14Z" fill="#323232" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_936_3015"><rect width="21" height="21" fill="white" /></clipPath>
                                    </defs>
                                </svg>
                                Panduan Ukuran
                            </button>
                        </div>

                        <button 
                            type="button"
                            @click="$dispatch('open-modal', 'modal-size-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            Tambahkan Variasi Ukuran
                        </button>
                    </div>

                    <div class="border-2 border-gray-400 rounded-md p-4 relative bg-white">
                        <button type="button" class="absolute top-2 right-3 text-gray-500 hover:text-red-500 font-bold">X</button>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg">Varian Warna</h3>
                        </div>

                        <button 
                            type="button"
                            @click="$dispatch('open-modal', 'modal-color-variation')"
                            class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                            Tambahkan Variasi Warna
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#323232] text-white py-3 rounded-md font-medium hover:opacity-90 transition">
                    Tambahkan Produk
                </button>
            </div>
        </form>

        <x-user.katalog.modals.tambahkan_ukuran/>
        <x-user.katalog.modals.variasi_warna/>
        {{-- <x-modal.color-variation /> --}}
        
    </div>
@endsection