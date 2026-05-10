<div x-data="{
        show: false,
        message: '',
        title: '',
        type: ''
    }" 
    x-init="
        @if($errors->any())
            show = true;
            title = 'Error';
            type = 'error';
            message = '{{ $errors->first() ?: 'Terjadi Kesalahan' }}';
        @endif
        
        @if(session('cart_success'))
            show = true;
            title = 'Berhasil';
            type = 'success';
            message = '{{ session('cart_success') }}';
        @endif

        window.addEventListener('notify', event => { 
            show = true;
            title = event.detail.title || 'Notifikasi';
            type = event.detail.type || 'success';
            message = event.detail.message || event.detail; 
        });
        
        $watch('show', value => {
            if (value) {
                setTimeout(() => show = false, 5000);
            }
        });
" class="fixed top-6 right-6 z-50">
    <div x-show="show" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-6"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 sm:translate-x-0"
        x-transition:leave-end="opacity-0 sm:translate-x-6"
        class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 p-4 pr-5 flex items-start gap-4 min-w-[340px] max-w-sm">

        <div class="flex-shrink-0 mt-0.5">
            <div x-show="type === 'success'" class="w-12 h-12 rounded-full bg-[#f0fdf4] flex items-center justify-center">
                <div class="w-9 h-9 rounded-full bg-[#dcfce7] flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#16a34a]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div x-show="type === 'error'" class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex-1 py-1">
            <h3 class="text-[15px] font-bold text-gray-900 leading-snug" x-text="title"></h3>
            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed" x-text="message"></p>
        </div>

        <button @click="show = false" type="button"
            class="text-gray-400 hover:text-gray-700 transition-colors p-1 mt-0.5 focus:outline-none">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>