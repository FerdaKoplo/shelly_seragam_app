<x-shared.modal_base name="modal-delete-confirmation" maxWidth="md" :showCloseButton="false">
    <div x-data="{ url: '' }" @set-archive-url.window="url = $event.detail"
        class="flex flex-col items-center justify-center gap-6 text-center pb-2">

        <h2 class="font-roboto text-black mb-8 px-2 leading-snug">
            Apa Anda Yakin Untuk Menghapus Produk?
        </h2>

        <div class="mb-10 text-[#323232]">
            <svg width="100" height="100" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0.973384 15.5741C0.973384 16.6449 1.84943 17.5209 2.92015 17.5209H10.7072C11.7779 17.5209 12.654 16.6449 12.654 15.5741V3.89354H0.973384V15.5741ZM13.6274 0.973384H10.2205L9.24715 0H4.38023L3.40684 0.973384H0V2.92015H13.6274V0.973384Z"
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
                @method('DELETE')
                <button type="submit"
                    class="w-full py-2.5 rounded-md bg-[#323232] text-white font-medium hover:opacity-90 transition shadow-md">
                    Hapu
                </button>
            </form>
        </div>
    </div>
</x-shared.modal_base>