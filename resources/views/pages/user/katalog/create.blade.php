@extends('layouts.user.layout')
@section('title', 'Create - Katalog')
@section('content')
    <div class="px-6 pb-12">
        <a href="{{ route('manage.katalog') }}" class="flex items-center gap-4 text-sm mb-8 w-fit hover:opacity-75 transition">
            <svg width="30" height="30" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_936_3036)">
                    <path d="M35.875 18.7917H11.6679L17.7837 12.6587L15.375 10.25L5.125 20.5L15.375 30.75L17.7837 28.3413L11.6679 22.2083H35.875V18.7917Z" fill="#323232" />
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

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            @csrf

            <div class="lg:col-span-7 space-y-6">
                
                <div class="flex gap-4 h-[400px]">
                    <div class="w-24 h-24 flex-shrink-0 border-2  rounded-md flex items-center justify-center cursor-pointer hover:bg-blue-50 transition">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="#323232" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="flex-1 border-2 border-dashed border-gray-400 rounded-md relative flex flex-col items-center justify-center bg-white">
                        <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-black">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-4 opacity-70">
                            <svg width="120" height="120" viewBox="0 0 24 24" fill="currentColor" class="text-[#323232]">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/>
                            </svg>
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-[#323232]">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </div>

                        <p class="absolute bottom-4 text-sm text-gray-600 font-medium">Tambahkan Minimal 1 Gambar</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="font-medium text-[#323232]">Terjual :</label>
                    <input type="number" value="0" class="border border-black rounded px-2 py-1 w-20 text-center focus:outline-none">
                </div>

                <div class="relative">
                    <textarea 
                        name="deskripsi" 
                        rows="6" 
                        placeholder="Tambahkan Deskripsi" 
                        class="w-full border border-black rounded-md p-4 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black resize-none"
                    ></textarea>
                    <span class="absolute top-4 right-4 text-red-500 text-xl">*</span>
                </div>

                <div>
                    <input 
                        type="text" 
                        placeholder="Ketik Untuk Menambahkan Kategori......" 
                        class="w-full border border-black rounded-md p-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-1 focus:ring-black"
                    >
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                
                <div class="relative">
                    <input 
                        type="text" 
                        name="nama_produk" 
                        placeholder="Nama Produk" 
                        class="w-full border border-black rounded-md px-4 py-3 text-lg font-bold placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                    >
                    <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        name="harga" 
                        placeholder="Harga Produk" 
                        class="w-full border border-black rounded-md px-4 py-3 text-2xl font-normal placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                    >
                    <span class="absolute top-2 right-2 text-red-500 text-xl">*</span>
                </div>

                <div>
                    <input 
                        type="number" 
                        name="stok" 
                        placeholder="Stock" 
                        class="w-full border border-black rounded-md px-4 py-2 placeholder-black focus:outline-none focus:ring-1 focus:ring-black"
                    >
                </div>

                <div class="border border-gray-400 rounded-md p-4 relative bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg flex gap-1">Ukuran <span class="text-red-500">*</span></h3>
                        <button type="button" class="text-xs flex items-center gap-1 text-gray-600 hover:text-black">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 12h20M2 12l4 4m-4-4l4-4"/> </svg>
                            Panduan Ukuran
                        </button>
                    </div>
                    
                    <button type="button" class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                        Tambahkan Variasi Ukuran
                    </button>
                </div>

                <div class="border border-gray-400 rounded-md p-4 relative bg-white">
                    <button type="button" class="absolute top-2 right-3 text-gray-500 hover:text-red-500 font-bold">X</button>
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Varian Warna</h3>
                    </div>
                    
                    <button type="button" class="w-full border-2 border-dashed border-gray-300 rounded-full py-2 text-gray-500 text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                        Tambahkan Variasi Warna
                    </button>
                </div>

                <button type="submit" class="w-full bg-[#323232] text-white py-3 rounded-md font-medium hover:opacity-90 transition mt-4">
                    Tambahkan Produk
                </button>

            </div>
        </form>
    </div>
@endsection