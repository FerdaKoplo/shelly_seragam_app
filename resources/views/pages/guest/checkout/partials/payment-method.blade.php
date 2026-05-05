<div class="space-y-4" x-data="{ method: 'xendit' }">
    <h2 class="text-xl font-bold">Pembayaran</h2>
    
    {{-- Payment Method Selection Box --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="border border-emerald-100 bg-emerald-50 rounded-xl overflow-hidden cursor-pointer">
            <div class="bg-emerald-100 p-4 font-semibold text-gray-700 flex justify-between items-center">
                <span class="flex items-center gap-2">
                    <input type="radio" name="payment_method_picker" value="xendit" x-model="method">
                    <span>Xendit</span>
                </span>
                <img src="" alt="Xendit" class="h-4">
            </div>

            <div class="p-6 text-center flex flex-col items-center justify-center">
                <p class="text-gray-500 max-w-xs mx-auto">
                    Setelah menekan tombol <strong x-text="type === 'katalog' ? '“Bayar”' : '“Buat Pesanan”'"></strong> anda akan dialihkan ke halaman pembayaran Xendit.
                </p>
            </div>
        </label>
    </div>

    {{-- Hidden Inputs for Backend Processing --}}
    <input type="hidden" name="payment_method" :value="method">
    <input type="hidden" name="total_amount" :value="total">
    {{-- Pass the cart items or custom data as JSON --}}
    <input type="hidden" name="order_payload" :value="type === 'katalog' ? JSON.stringify(items) : JSON.stringify(customData)">
</div>
