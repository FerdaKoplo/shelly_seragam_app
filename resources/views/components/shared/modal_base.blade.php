@props(['name', 'title', 'maxWidth' => '2xl'])

@php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
    ][$maxWidth];
@endphp

<div x-data="{
        show: false,
        name: '{{ $name }}',
        closeModal() {
            this.show = false;
            document.body.style.overflowY = 'auto'; 
        }
    }" x-on:open-modal.window="if ($event.detail === name) { show = true; document.body.style.overflowY = 'hidden'; }"
    x-on:close-modal.window="if ($event.detail === name) { closeModal(); }" x-on:keydown.escape.window="closeModal()"
    x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="closeModal()" aria-hidden="true">
    </div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden  bg-white text-left shadow-xl transition-all sm:my-8 w-full {{ $maxWidthClass }}"
            @click.stop>
            <button type="button" @click="closeModal()"
                class="absolute top-1 text-2xl right-5 z-10 p-1 text-gray-900 hover:text-gray-500 transition-colors">
                X
            </button>
            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>