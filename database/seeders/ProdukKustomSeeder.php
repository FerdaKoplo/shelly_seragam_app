<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukKustomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Ambil array produk_id
        $kustomProductIds = DB::table('produk')
            ->where('jenis_produk', 'kustom')
            ->pluck('produk_id')
            ->toArray();

        if (empty($kustomProductIds)) {
            $this->command->warn("Tidak ada produk kustom ditemukan. Jalankan ProdukSeeder terlebih dahulu.");
            return;
        }

        // 2. Tentukan nama section statis yang ingin dibuat (bukan random)
        $sectionsToSeed = ['Bundle', 'Atasan', 'Bawahan'];
        $seededCount = 0;

        foreach ($sectionsToSeed as $index => $sectionName) {
            // Pastikan masih ada sisa produk_id untuk dipasangkan dengan section ini
            if (!isset($kustomProductIds[$index])) {
                break;
            }

            $produkId = $kustomProductIds[$index];

            // 3. CEK DUPLIKASI BERDASARKAN NAMA SECTION
            // Jika "Bawahan" sudah ada di database, lewati.
            $sudahAda = DB::table('produk_kustom')->where('spesifikasi_khusus', $sectionName)->exists();

            if ($sudahAda) {
                continue;
            }

            // Insert ke produk_kustom
            DB::table('produk_kustom')->insert([
                'produk_id' => $produkId,
                'spesifikasi_khusus' => $sectionName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // === Data Detail Kombinasi ===
            $detailKombinasiId = DB::table('detail_produk')->insertGetId([
                'produk_id' => $produkId,
                'nama_detail' => 'Jumlah Kombinasi Kain',
                'deskripsi_detail' => 'Pilih berapa banyak jenis kain yang akan dikombinasikan pada pakaian.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $opsiKombinasi = [
                ['opsi' => '1 Kombinasi', 'harga' => 0],
                ['opsi' => '2 Kombinasi', 'harga' => 35000],
                ['opsi' => '3 Kombinasi', 'harga' => 70000],
            ];
            $this->insertPilihan($detailKombinasiId, $opsiKombinasi, $now);

            // === Data Detail Material ===
            $detailMaterialId = DB::table('detail_produk')->insertGetId([
                'produk_id' => $produkId,
                'nama_detail' => 'Pilihan Material Kain',
                'deskripsi_detail' => 'Pilih bahan utama yang akan digunakan untuk jahitan.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $opsiMaterial = [
                ['opsi' => 'Standar', 'harga' => 0],
                ['opsi' => 'Katun', 'harga' => 20000],
                ['opsi' => 'Woll', 'harga' => 50000],
                ['opsi' => 'Nylon', 'harga' => 15000],
                ['opsi' => 'Kaos', 'harga' => -10000],
                ['opsi' => 'Kargo', 'harga' => 30000],
                ['opsi' => 'Satin', 'harga' => 45000],
                ['opsi' => 'Polyester', 'harga' => 10000],
                ['opsi' => 'Batik', 'harga' => 60000],
            ];
            $this->insertPilihan($detailMaterialId, $opsiMaterial, $now);

            // === Data Detail Bordir ===
            $detailBordirId = DB::table('detail_produk')->insertGetId([
                'produk_id' => $produkId,
                'nama_detail' => 'Jumlah Titik Bordir',
                'deskripsi_detail' => 'Tentukan berapa banyak lokasi bordir (logo/tulisan) pada pakaian.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $opsiBordir = [
                ['opsi' => '0', 'harga' => 0],
                ['opsi' => '1', 'harga' => 15000],
                ['opsi' => '2', 'harga' => 30000],
                ['opsi' => '3', 'harga' => 45000],
                ['opsi' => '4', 'harga' => 60000],
                ['opsi' => '5', 'harga' => 75000],
            ];
            $this->insertPilihan($detailBordirId, $opsiBordir, $now);

            $seededCount++;
        }

        if ($seededCount > 0) {
            $this->command->info($seededCount . ' section kustom (Bundle, Atasan, Bawahan) berhasil di-seed tanpa duplikat.');
        } else {
            $this->command->info('Semua section kustom sudah ter-seed sebelumnya. Tidak ada duplikasi yang dibuat.');
        }
    }

    private function insertPilihan($detailId, $opsiArray, $time)
    {
        $insertData = [];
        foreach ($opsiArray as $item) {
            $insertData[] = [
                'detail_produk_id' => $detailId,
                'opsi' => $item['opsi'],
                'pengaruh_harga' => $item['harga'],
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }
        DB::table('pilihan_detail_produk')->insert($insertData);
    }
}
