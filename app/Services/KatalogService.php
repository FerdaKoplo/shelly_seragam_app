<?php

namespace App\Services;

use App\Models\DetailProduk;
use App\Models\FotoProdukKatalog;
use App\Models\PilihanDetailProduk;
use Storage;

class KatalogService
{
    public function syncVariations(int $produkId, array $variations): void
    {
        DetailProduk::where('produk_id', $produkId)->delete();

        $variationsCollection = collect($variations);
        $types = ['ukuran', 'warna'];

        foreach ($types as $type) {
            $items = $variationsCollection->where('type', $type);

            if ($items->isEmpty()) {
                continue;
            }

            $detail = DetailProduk::create([
                'produk_id' => $produkId,
                'nama_detail' => ucfirst($type),
                'deskripsi_detail' => 'Variasi ' . ucfirst($type),
            ]);

            $pilihanData = $items->map(function ($item) use ($detail) {
                $data = json_decode($item['data'], true);
                unset($data['type']);

                return [
                    'detail_produk_id' => $detail->detail_produk_id,
                    'opsi' => json_encode($data),
                    'pengaruh_harga' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            PilihanDetailProduk::insert($pilihanData);
        }
    }

    public function uploadPhotos(int $produkId, array $fotos): void
    {
        $fotoData = [];

        foreach ($fotos as $photo) {
            $path = $photo->store('uploads/produk', 'public');

            $fotoData[] = [
                'produk_id' => $produkId,
                'path' => $path,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        FotoProdukKatalog::insert($fotoData);
    }

    public function removePhotos(array $photoIds): void
    {
        $photosToDelete = FotoProdukKatalog::whereIn('id', $photoIds)->get();

        foreach ($photosToDelete as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }
    }
}