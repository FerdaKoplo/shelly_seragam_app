<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_voucher_validation_trims_and_normalizes_code(): void
    {
        Voucher::create([
            'nama_voucher' => 'Promo Akhir Tahun',
            'deskripsi' => 'Diskon khusus akhir tahun',
            'kode_voucher' => 'PROMO-2026',
            'nilai_diskon' => 10000,
            'jenis_voucher' => 'nominal',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_berakhir' => Carbon::tomorrow()->toDateString(),
            'status' => 'Aktif',
        ]);

        $response = $this->postJson('/api/voucher/validate', [
            'kode' => '  promo-2026  ',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'voucher' => [
                    'kode_voucher' => 'PROMO-2026',
                    'jenis_voucher' => 'nominal',
                    'nilai_diskon' => 10000,
                    'nama_voucher' => 'Promo Akhir Tahun',
                    'deskripsi' => 'Diskon khusus akhir tahun',
                ],
            ]);
    }

    public function test_voucher_validation_rejects_inactive_voucher(): void
    {
        Voucher::create([
            'nama_voucher' => 'Promo Nonaktif',
            'deskripsi' => 'Voucher nonaktif',
            'kode_voucher' => 'NONAKTIF',
            'nilai_diskon' => 5000,
            'jenis_voucher' => 'nominal',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_berakhir' => Carbon::tomorrow()->toDateString(),
            'status' => 'Habis',
        ]);

        $response = $this->postJson('/api/voucher/validate', [
            'kode' => 'nonaktif',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Voucher sudah tidak aktif.',
            ]);
    }
}
