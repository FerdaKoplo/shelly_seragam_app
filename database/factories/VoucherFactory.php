<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_voucher' => fake()->words(3, true),
            'kode_voucher' => strtoupper(fake()->unique()->bothify('PROMO-####')),
            'deskripsi' => fake()->sentence(),
            'nilai_diskon' => fake()->randomElement([10000, 25000, 50000]),
            'jenis_voucher' => fake()->randomElement(['nominal', 'persentase']),
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Aktif',
        ];
    }
}
