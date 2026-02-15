<x-shared.modal_base name="modal-archive-confirmation" maxWidth="md" :showCloseButton="false">
    <div x-data="{ url: '' }" @set-archive-url.window="url = $event.detail"
        class="flex flex-col items-center justify-center gap-6 text-center pb-2">

        <h2 class="font-roboto text-black mb-8 px-2 leading-snug">
            Apa Anda Yakin Untuk Mengarsipkan Produk?
        </h2>

        <div class="mb-10 text-[#323232]">
            <svg width="100" height="100" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.5401 5.3262H9.27647V2.6631H6.70214V5.3262H4.4385L7.98931 8.87701L11.5401 5.3262ZM14.2032 0H1.76652C0.781176 0 0 0.79893 0 1.7754V14.2032C0 15.1797 0.781176 15.9786 1.76652 15.9786H14.2032C15.1797 15.9786 15.9786 15.1797 15.9786 14.2032V1.7754C15.9786 0.79893 15.1797 0 14.2032 0ZM14.2032 14.2032H1.7754V11.5401H4.93562C5.54813 12.5965 6.68439 13.3155 7.99818 13.3155C9.31198 13.3155 10.4394 12.5965 11.0607 11.5401H14.2032V14.2032ZM14.2032 9.76471H9.77358C9.77358 10.7412 8.97465 11.5401 7.99818 11.5401C7.02171 11.5401 6.22278 10.7412 6.22278 9.76471H1.7754L1.76652 1.7754H14.2032V9.76471Z"
                    fill="currentColor" />
            </svg>
        </div>

        <div class="w-full space-y-3">
            <button type="button" @click="closeModal()"
                class="w-full py-2.5 rounded-md border border-black text-black font-medium hover:bg-gray-50 transition">
                Batal
            </button>

            <form :action="url" method="POST" class="w-full">
                @csrf
                @method('PUT')
                <button type="submit"
                    class="w-full py-2.5 rounded-md bg-[#323232] text-white font-medium hover:opacity-90 transition shadow-md">
                    Arsipkan
                </button>
            </form>
        </div>
    </div>
</x-shared.modal_base>