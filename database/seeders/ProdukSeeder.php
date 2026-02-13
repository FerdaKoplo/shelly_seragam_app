<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'nama' => 'Seragam SMA Lengan Panjang',
                'deskripsi' => 'Bahan katun oxford, sejuk dan tidak mudah kusut. Standar nasional.',
                'jenis' => 'katalog',
                'harga' => 125000,
                'stok' => 100,
                'kategori' => 'Seragam Sekolah',
                'foto' => 'uploads/produk/seragam_sma.jpg'
            ],
            [
                'nama' => 'Kemeja PDH Navy',
                'deskripsi' => 'Bahan American Drill, cocok untuk seragam kantor atau organisasi.',
                'jenis' => 'katalog',
                'harga' => 185000,
                'stok' => 50,
                'kategori' => 'Seragam Kantor',
                'foto' => 'uploads/produk/pdh_navy.jpg'
            ],
            [
                'nama' => 'Kaos Polo Polos Hitam',
                'deskripsi' => 'Bahan Lacoste CVC, nyaman dipakai sehari-hari.',
                'jenis' => 'katalog',
                'harga' => 95000,
                'stok' => 200,
                'kategori' => 'Kaos',
                'foto' => 'uploads/produk/polo_hitam.jpg'
            ],
            // Example of a 'kustom' product (Usually doesn't have fixed catalog price/stock yet, but entered in parent table)
            [
                'nama' => 'Jasa Jahit Jas Almamater',
                'deskripsi' => 'Pemesanan khusus jas almamater dengan bordir logo universitas.',
                'jenis' => 'kustom',
                'harga' => null, // Kustom might not have catalog price
                'stok' => null,
                'kategori' => null,
                'foto' => null
            ]
        ];

        foreach ($items as $item) {
            $now = Carbon::now();

            $produkId = DB::table('produk')->insertGetId([
                'nama_produk' => $item['nama'],
                'deskripsi' => $item['deskripsi'],
                'jenis_produk' => $item['jenis'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($item['jenis'] === 'katalog') {
                DB::table('produk_katalog')->insert([
                    'produk_id' => $produkId,
                    'kategori' => $item['kategori'],
                    'harga' => $item['harga'],
                    'stok' => $item['stok'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('foto_produk_katalog')->insert([
                    'produk_id' => $produkId,
                    'path' => $item['foto'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
