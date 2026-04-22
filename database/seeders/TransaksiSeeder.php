<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
$faker = Faker::create('id_ID');

        // 1. GET EXISTING PEGAWAI IDs
        $pegawaiIds = DB::table('user')->where('role', 'Pegawai')->pluck('user_id')->toArray();

        // Fallback: If no Pegawai exists yet, create one so the seeder doesn't break
        if (empty($pegawaiIds)) {
            $dummyId = DB::table('user')->insertGetId([
                'nama' => 'Dummy Pegawai',
                'username' => 'dummy.pegawai.' . rand(1, 100),
                'email' => 'dummy' . rand(1, 100) . '@example.com',
                'role' => 'Pegawai',
                'status' => 'Active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $pegawaiIds = [$dummyId];
        }

        $productIds = DB::table('produk')->pluck('produk_id')->toArray();

        foreach (range(1, 20) as $i) {

            $statusOptions = ["Created", "Paid", "Delivered", "Done"];

            $transaksiId = DB::table('transaksi')->insertGetId([
                'pegawai_id' => $faker->randomElement($pegawaiIds), // <-- ADDED THIS LINE
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
                    'detail_pilihan_kustomisasi' => json_encode(['kain' => 'cotton', 'sablon' => 'DTF']), 
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
