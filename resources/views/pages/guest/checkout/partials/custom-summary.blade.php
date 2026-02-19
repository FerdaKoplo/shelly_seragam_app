<div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
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
        <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-gray-200 w-fit">
            <div class="w-10 h-10 bg-gray-200 rounded"></div>
            <span class="text-sm" x-text="customData.file"></span>
        </div>
    </div>
</div>