<x-shared.modal_base data-cy="size-guide-modal" name="modal-panduan-ukuran" maxWidth="2xl" :showCloseButton="true">
    <div x-data="{
        {{-- This mock data should be passed from your Product Model in the backend --}}
        measurements: [
            { size: 'XS', sleeve: '83.5', chest: '59.5', length: '68' },
            { size: 'S',  sleeve: '85.2', chest: '62.0', length: '70' },
            { size: 'M',  sleeve: '87.0', chest: '65.5', length: '72' },
            { size: 'L',  sleeve: '88.9', chest: '69.9', length: '74' },
            { size: 'XL', sleeve: '90.5', chest: '73.0', length: '76' },
            { size: 'XXL',sleeve: '92.0', chest: '76.5', length: '78' }
        ],
        unit: 'CM'
    }">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-zinc-900">Panduan Ukuran</h2>
            <p class="text-sm text-zinc-500 mt-1">Semua ukuran diukur dalam satuan <span x-text="unit"></span>.</p>
        </div>

        <div class="overflow-x-auto -mx-6 sm:mx-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 border-y border-zinc-200">
                        <th class="py-3 px-6 font-semibold text-zinc-700">Ukuran</th>
                        <th class="py-3 px-4 font-semibold text-zinc-700 text-center">Lebar Dada</th>
                        <th class="py-3 px-4 font-semibold text-zinc-700 text-center">Panjang Lengan</th>
                        <th class="py-3 px-4 font-semibold text-zinc-700 text-center">Panjang Baju</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <template x-for="item in measurements" :key="item.size">
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-4 px-6 font-bold text-zinc-900" x-text="item.size"></td>
                            <td class="py-4 px-4 text-center text-zinc-600" x-text="item.chest"></td>
                            <td class="py-4 px-4 text-center text-zinc-600" x-text="item.sleeve"></td>
                            <td class="py-4 px-4 text-center text-zinc-600" x-text="item.length"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="mt-8 p-4 bg-zinc-50 rounded-lg flex gap-4 items-start border border-zinc-100">
            <div class="bg-white p-2 rounded shadow-sm">
                <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-zinc-900">Cara Mengukur</h4>
                <p class="text-xs text-zinc-500 leading-relaxed mt-1">
                    Letakkan pakaian favorit Anda secara mendatar dan ukur dari satu sisi ketiak ke ketiak lainnya untuk mendapatkan "Lebar Dada".
                </p>
            </div>
        </div>

        
    </div>
</x-shared.modal_base>