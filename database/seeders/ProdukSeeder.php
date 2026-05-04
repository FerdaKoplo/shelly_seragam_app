<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $kategoris = ['Atasan', 'Seragam Sekolah', 'Seragam Kantor', 'Kaos', 'Jaket', 'Celana', 'Aksesoris'];

        // optional: avoid duplicate sizes in same product
        $selectedUkuran = [
            ['name' => 'XS', 'sleeve' => 85, 'chest' => 60],
            ['name' => 'S', 'sleeve' => 86, 'chest' => 62],
            ['name' => 'M', 'sleeve' => 88, 'chest' => 65],
            ['name' => 'L', 'sleeve' => 89, 'chest' => 70],
            ['name' => 'XL', 'sleeve' => 90, 'chest' => 75],
            ['name' => 'XXL', 'sleeve' => 92, 'chest' => 80],
        ];


        for ($i = 0; $i < 100; $i++) {
            $now = Carbon::now();

            $jenis = $faker->boolean(90) ? 'katalog' : 'kustom';

            $produkId = DB::table('produk')->insertGetId([
                'nama_produk' => $this->generateProductName($faker, $jenis),
                'deskripsi' => $faker->paragraph(2),
                'jenis_produk' => $jenis,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($jenis === 'katalog') {
                $kategori = $faker->randomElement($kategoris);

                DB::table('produk_katalog')->insert([
                    'produk_id' => $produkId,
                    'kategori' => $kategori,
                    'harga' => $faker->numberBetween(50, 500) * 1000,
                    'stok' => $faker->numberBetween(0, 20),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $detailId = DB::table('detail_produk')->insertGetId([
                    'produk_id' => $produkId,
                    'nama_detail' => 'Ukuran',
                    'deskripsi_detail' => 'Variasi ukuran produk',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($selectedUkuran as $ukuran) {
                    DB::table('pilihan_detail_produk')->insert([
                        'detail_produk_id' => $detailId,
                        'opsi' => json_encode([
                            'name' => $ukuran['name'],
                            'sleeve' => (string) $ukuran['sleeve'],
                            'chest' => (string) $ukuran['chest'],
                        ]),
                        'pengaruh_harga' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
                // DB::table('foto_produk_katalog')->insert([
                //     'produk_id' => $produkId,
                //     'path' => 'uploads/produk/' . \Str::slug($kategori) . '-' . $faker->numberBetween(1, 10) . '.jpg',
                //     'created_at' => $now,
                //     'updated_at' => $now,
                // ]);
            }
        }
    }

    private function generateProductName($faker, $jenis)
    {
        if ($jenis === 'kustom') {
            return 'Jasa Jahit ' . $faker->randomElement(['Jas Almamater', 'Seragam Komunitas', 'Baju PDH Custom', 'Wearpack Safety']);
        }

        $adjectives = ['Lengan Panjang', 'Lengan Pendek', 'Premium', 'Polos', 'Motif Kotak', 'Oversize', 'Slim Fit'];
        $items = ['Kemeja', 'Kaos Polo', 'Celana Chino', 'Rok Rempel', 'Blazer', 'Rompi', 'Jaket Bomber'];
        $materials = ['Katun', 'Drill', 'Oxford', 'Canvas', 'Denim'];

        return $faker->randomElement($items) . ' ' .
            $faker->randomElement($materials) . ' ' .
            $faker->randomElement($adjectives);
    }
}
