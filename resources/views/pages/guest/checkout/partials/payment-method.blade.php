<div class="space-y-4" x-data="{ method: 'midtrans' }">
    <h2 class="text-xl font-bold">Pembayaran</h2>
    
    {{-- Payment Method Selection Box --}}
    <div class="border border-blue-100 bg-blue-50 rounded-xl overflow-hidden">
        <div class="bg-blue-100 p-4 font-semibold text-gray-700 flex justify-between items-center">
            <span>Payments via Midtrans</span>
            <img src="" alt="Midtrans" class="h-4">
        </div>
        
        <div class="p-10 text-center flex flex-col items-center justify-center">
            <p class="text-gray-500 max-w-xs mx-auto">
                Setelah menekan tombol <strong x-text="type === 'katalog' ? '“Bayar”' : '“Buat Pesanan”'"></strong> anda akan dialihkan ke Pembayaran via Midtrans
            </p>
        </div>
    </div>

    {{-- Hidden Inputs for Backend Processing --}}
    <input type="hidden" name="payment_method" value="midtrans">
    <input type="hidden" name="total_amount" :value="total">
    {{-- Pass the cart items or custom data as JSON --}}
    <input type="hidden" name="order_payload" :value="type === 'katalog' ? JSON.stringify(items) : JSON.stringify(customData)">
</div>