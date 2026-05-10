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
        $faker = Faker::create('id_ID');

        $now = Carbon::now();

        $kustomProductIds = DB::table('produk')
            ->where('jenis_produk', 'kustom')
            ->pluck('produk_id');


        if (empty($kustomProductIds)) {
            $this->command->warn("Tidak ada produk kustom ditemukan. Jalankan ProdukSeeder terlebih dahulu.");
            return;
        }

        foreach ($kustomProductIds as $produkId) {

            DB::table('produk_kustom')->insert([
                'produk_id' => $produkId,
                'spesifikasi_khusus' => $faker->randomElement(['Bundle', 'Atasan', 'Bawahan']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $detailKombinasiId = DB::table('detail_produk')->insertGetId([
                'produk_id' => $produkId,
                'nama_detail' => 'Jumlah Kombinasi Kain',
                'deskripsi_detail' => 'Pilih berapa banyak jenis kain yang akan dikombinasikan pada pakaian.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $opsiKombinasi = [
                ['opsi' => '1 Kombinasi', 'harga' => 0],
                ['opsi' => '2 Kombinasi', 'harga' => 35000], // Menambah kombinasi menambah biaya jahit
                ['opsi' => '3 Kombinasi', 'harga' => 70000],
            ];
            $this->insertPilihan($detailKombinasiId, $opsiKombinasi, $now);


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
                ['opsi' => 'Kaos', 'harga' => -10000], // Harga bisa lebih murah dari standar
                ['opsi' => 'Kargo', 'harga' => 30000],
                ['opsi' => 'Satin', 'harga' => 45000],
                ['opsi' => 'Polyester', 'harga' => 10000],
                ['opsi' => 'Batik', 'harga' => 60000],
            ];
            $this->insertPilihan($detailMaterialId, $opsiMaterial, $now);


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
        }

        $this->command->info(count($kustomProductIds) . ' produk kustom beserta detail UI-nya berhasil di-seed.');
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
