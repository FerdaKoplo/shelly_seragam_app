<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'pegawai_id' => User::factory(),

            'nama_customer' => fake()->name(),
            'no_hp_customer' => fake()->phoneNumber(),
            'alamat_customer' => fake()->address(),

            'no_resi_customer' => null,

            'status' => 'Created',
            'tanggal_transaksi' => now()->format('Y-m-d'),
            'total_harga' => fake()->randomFloat(0, 50000, 500000),

            'checkout_external_id' => fake()->uuid(),
        ];
    }
}
