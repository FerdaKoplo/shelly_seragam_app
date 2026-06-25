<x-shared.modal_base name="modal-deactivate-confirmation" maxWidth="md" :showCloseButton="false">
    <div x-data="{ url: '' }" @set-deactivate-url.window="url = $event.detail"
        class="flex flex-col items-center justify-center gap-6 text-center pb-2">

        <h2 class="font-roboto text-black mb-8 px-2 leading-snug">
            Apa Anda Yakin Untuk Menonaktifkan Voucher Ini?
        </h2>

        <div class="mb-10 text-[#323232]">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22ZM12 20C7.58172 20 4 16.4183 4 12C4 10.1585 4.6213 8.46162 5.65586 7.05831L16.9417 18.3441C15.5384 19.3787 13.8415 20 12 20ZM18.3441 16.9417L7.05831 5.65586C8.46162 4.6213 10.1585 4 12 4C16.4183 4 20 7.58172 20 12C20 13.8415 19.3787 15.5384 18.3441 16.9417Z" fill="currentColor"/>
            </svg>
        </div>

        <div class="w-full space-y-3">
            <button type="button" @click="closeModal()" id="deactivateModalDismiss"
                class="w-full py-2.5 rounded-md border border-black text-black font-medium hover:bg-gray-50 transition">
                Batal
            </button>

            <form :action="url" method="POST" class="w-full" id="deactivateModalConfirm">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="w-full py-2.5 rounded-md bg-[#323232] text-white font-medium hover:opacity-90 transition shadow-md">
                    Nonaktifkan
                </button>
            </form>
        </div>
    </div>
</x-shared.modal_base>