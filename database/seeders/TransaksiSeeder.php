<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('id_ID'); // Use Indonesian locale for realistic data

        $userIds = DB::table('user')->pluck('user_id')->toArray();
        $productIds = DB::table('produk')->pluck('produk_id')->toArray();

        if (empty($userIds) || empty($productIds)) {
            $this->command->warn("Please seed 'user' and 'produk' tables first.");
            return;
        }

        foreach (range(1, 20) as $i) {

            $statusOptions = ["Created", "Paid", "Delivered", "Done"];

            $transaksiId = DB::table('transaksi')->insertGetId([
                'user_id' => $faker->randomElement($userIds),
                'nama_customer' => $faker->name,
                'no_hp_customer' => $faker->phoneNumber,
                'alamat_customer' => $faker->address,
                'no_resi_customer' => 'RESI-' . strtoupper($faker->bothify('??#####')),
                'status' => $faker->randomElement($statusOptions),
                'tanggal_transaksi' => $faker->dateTimeBetween('-1 month', 'now'),
                'total_harga' => 0, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $grandTotal = 0;

            $isCustomOrder = $faker->boolean(40); 

            if ($isCustomOrder) {
                $tipeOptions = ['Bundle', 'Atasan', 'Bawahan'];

                $orderKustomId = DB::table('order_transaksi_kustom')->insertGetId([
                    'transaksi_id' => $transaksiId,
                    'quantity' => $faker->numberBetween(1, 100),
                    'ukuran_dipilih' => $faker->randomElement(['S, M, L', 'All Size', 'Custom List']),
                    'tipe_kustom' => $faker->randomElement($tipeOptions),
                    'catatan' => $faker->sentence,
                    'detail_pilihan_kustomisasi' => json_encode(['kain' => 'cotton', 'sablon' => 'DTF']), // Example JSON text
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $grandTotal += $faker->numberBetween(500000, 5000000);

                for ($k = 0; $k < rand(1, 3); $k++) {
                    DB::table('attachment_transaksi_kustom')->insert([
                        'order_kustom_id' => $orderKustomId,
                        'path' => 'uploads/custom_designs/design_' . $faker->unique()->numberBetween(1, 1000) . '.jpg',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

            } else {
                for ($j = 0; $j < rand(1, 5); $j++) {
                    $qty = $faker->numberBetween(1, 5);
                    $price = $faker->numberBetween(50000, 200000);
                    $subtotal = $qty * $price;

                    DB::table('produk_transaksi')->insert([
                        'transaksi_id' => $transaksiId,
                        'produk_id' => $faker->randomElement($productIds),
                        'quantity' => $qty,
                        'size' => $faker->randomElement(['S', 'M', 'L', 'XL']),
                        'subtotal' => $subtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $grandTotal += $subtotal;
                }
            }

            DB::table('transaksi')
                ->where('transaksi_id', $transaksiId)
                ->update(['total_harga' => $grandTotal]);
        }
    }
}
