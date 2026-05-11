<div data-cy="custom-summary" class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
    <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>
    <div class="space-y-2 text-xl text-gray-700 mb-8">
        <p x-text="customData.title"></p>
        <p x-text="'Jumlah: ' + customData.qty"></p>
        <p x-text="'Tipe: ' + customData.type"></p>
    </div>

    <hr class="mb-6 border-gray-300">

    <div class="flex justify-between items-center mb-6">
        <span class="text-xl">Estimasi Harga:</span>
        <span class="text-2xl font-bold" x-text="formatCurrency(customData.price)"></span>
    </div>

    <div class="space-y-2">
        <p class="font-bold">Lampiran:</p>

        <template x-if="(customData.attachments || []).length > 0">
            <div class="space-y-2">
                <template x-for="(attachment, idx) in customData.attachments" :key="idx">
                    <div  data-cy="attachment-item" class="flex items-center gap-3 bg-white p-3 rounded-lg border border-gray-200">
                        <template x-if="/\.(png|jpg|jpeg)$/i.test(attachment.url)">
                            <img :src="attachment.url" alt="Lampiran"
                                class="w-10 h-10 rounded object-cover border" />
                        </template>

                        <template x-if="!/\.(png|jpg|jpeg)$/i.test(attachment.url)">
                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-600">
                                FILE
                            </div>
                        </template>

                        <div class="flex flex-col">
                            <span class="text-sm" x-text="attachment.name"></span>
                            <a data-cy="attachment-link" class="text-xs underline text-gray-600" :href="attachment.url" target="_blank">
                                Lihat detail
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="(customData.attachments || []).length === 0">
            <div class="text-sm text-gray-500">
                Tidak ada lampiran.
            </div>
        </template>
    </div>
</div>
