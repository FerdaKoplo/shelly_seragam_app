<div x-data="{ 
        show: false, 
        message: 'Something went wrong', 
        title: 'Error',
        type: ''
    }" x-init="
        @if($errors->any())
            show = true;
            message = '{{ $errors->first() ?: 'Lengkapi Semua Data' }}';
        @endif
        @if(session('success'))
            show = true;
            message = '{{ session('success') }}';
            type = 'success';
        @endif

        window.addEventListener('notify', event => { 
            show = true; 
            message = event.detail; 
        });
    " x-show="show" x-transition.opacity.duration.300ms style="display: none;" id="notificationOverlay"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div @click.outside="show = false" :class="type === 'success' ? 'bg-green-100' : 'bg-warningSecondary'"
        class="relative w-[400px] rounded-2xl shadow-xl p-8 py-12 flex flex-col items-center justify-center text-center">
        <button id="btnDismiss" @click="show = false" :class="type === 'success' ? 'text-green-600' : 'text-warningPrimary'"
            class="absolute top-1 font-semibold right-4 hover:opacity-75 transition">
            <p class="font-inter">X</p>
        </button>
        <h2 :class="type === 'success' ? 'text-green-600' : 'text-warningPrimary'"
            class="font-semibold text-xl tracking-wide" x-text="message"></h2>
    </div>
    {{-- <div @click.outside="show = false"
        class="relative w-[400px] bg-warningSecondary rounded-2xl shadow-xl p-8 py-12 flex flex-col items-center justify-center text-center animate-bounce-in">
        <button @click="show = false"
            class="absolute top-1 font-semibold right-4 text-warningPrimary hover:opacity-75 transition">
            <p class="font-inter">
                X
            </p>

        </button>

        <h2 class="text-warningPrimary font-semibold text-xl tracking-wide" x-text="message"></h2>
    </div> --}}
</div>