<?php

namespace App\Services;

use App\Models\CheckoutOrder;
use App\Models\OrderTransaksiKustom;
use App\Models\ProdukKatalog;
use App\Models\ProdukTransaksi;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class CheckoutTransaksiService
{
    public function ensureTransaksiFromCheckoutOrder(CheckoutOrder $order): Transaksi
    {
        return DB::transaction(function () use ($order) {
            $statusMap = [
                'CREATED' => 'Created',
                'PAID' => 'Paid',
                'PENDING' => 'Created', // Treat pending as created/unpaid
                'SETTLED' => 'Paid',    // Explicitly treat settled as paid
            ];

            $transaksiStatus = $statusMap[strtoupper((string) $order->status)] ?? 'Created';

            $transaksi = Transaksi::query()->firstOrNew([
                'checkout_external_id' => $order->external_id,
            ]);

            //SETA
            // FORCE STATS: If it's a catalog product and the order checkout state is paid, ensure it evaluates as 'Paid'
            if ($order->type === 'katalog' && strtoupper((string) $order->status) === 'PAID') {
                $transaksiStatus = 'Paid';
            }

            $transaksi->fill([
                'pegawai_id' => $transaksi->pegawai_id, // keep whatever assigned by admin
                'nama_customer' => (string) $order->customer_name,
                'no_hp_customer' => (string) $order->customer_phone,
                'alamat_customer' => trim(implode(', ', array_filter([
                    (string) $order->address,
                    (string) $order->city,
                    (string) $order->province,
                    (string) $order->postal_code,
                ]))),
                'no_resi_customer' => $transaksi->no_resi_customer ?? '-',
                'status' => $transaksiStatus,
                'tanggal_transaksi' => ($order->paid_at?->toDateString()) ?? now()->toDateString(),
                'total_harga' => (float) ($order->total ?? $order->subtotal ?? 0),
            ]);

            //SETA
            // Force state overwrite if model dirty state check fails on existing records
            if ($transaksi->exists) {
                $transaksi->status = $transaksiStatus;
            }
            $transaksi->save();

            if ($order->type === 'katalog') {
                $this->syncProdukTransaksi($transaksi, (array) $order->items);
            }

            if ($order->type === 'kustom') {
                $this->ensureKustomOrderRow($transaksi, (array) $order->items, (string) $order->notes);
            }

            return $transaksi;
        });
    }

    private function syncProdukTransaksi(Transaksi $transaksi, array $items): void
    {
        foreach ($items as $item) {
            $katalogId = (int) ($item['katalog_id'] ?? $item['id'] ?? 0);
            if ($katalogId <= 0) {
                continue;
            }

            $katalog = ProdukKatalog::query()->find($katalogId);
            if (!$katalog) {
                continue;
            }

            $produkId = (int) $katalog->produk_id;
            if ($produkId <= 0) {
                continue;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = (int) ($item['price'] ?? 0);
            $size = (string) ($item['size'] ?? '-');

            ProdukTransaksi::query()->firstOrCreate(
                [
                    'transaksi_id' => $transaksi->transaksi_id,
                    'produk_id' => $produkId,
                    'size' => $size,
                ],
                [
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                ]
            );
        }
    }

    private function ensureKustomOrderRow(Transaksi $transaksi, array $items, string $notes): void
    {
        if (OrderTransaksiKustom::query()->where('transaksi_id', $transaksi->transaksi_id)->exists()) {
            return;
        }

        $payload = $items;
        $quantity = (int) data_get($payload, 'total_quantity', data_get($payload, 'quantity', 1));
        $size = (string) data_get($payload, 'size', '-');
        $category = (string) data_get($payload, 'category', data_get($payload, 'type', 'kustom'));

        OrderTransaksiKustom::query()->create([
            'transaksi_id' => $transaksi->transaksi_id,
            'quantity' => max(1, $quantity),
            'ukuran_dipilih' => $size !== '' ? $size : '-',
            'tipe_kustom' => $category !== '' ? $category : 'kustom',
            'catatan' => $notes !== '' ? $notes : '-',
            'detail_pilihan_kustomisasi' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
