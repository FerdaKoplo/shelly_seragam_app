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

        $kategoris = ['Seragam Sekolah', 'Seragam Kantor', 'Kaos', 'Jaket', 'Celana', 'Aksesoris'];
        
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
                    'stok' => $faker->numberBetween(0, 200),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('foto_produk_katalog')->insert([
                    'produk_id' => $produkId,
                    'path' => 'uploads/produk/' . \Str::slug($kategori) . '-' . $faker->numberBetween(1, 10) . '.jpg',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
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
