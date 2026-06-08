<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class VoucherCustomerTest extends TestCase
{
    use RefreshDatabase;


    /** TC-WBT-CUS008-01 */
    public function test_apply_voucher_not_found()
    {
        $response = $this->postJson(route('voucher.validate'), [
            'kode' => 'RANDOM-INVALID'
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Voucher tidak ditemukan.'
        ]);
    }

    /** TC-WBT-CUS008-02 */
    public function test_apply_expired_voucher()
    {
        Voucher::factory()->create([
            'kode_voucher' => 'KADALUARSA2026',
            'status' => 'Aktif',
            'tanggal_mulai' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'tanggal_berakhir' => Carbon::now()->subDays(1)->format('Y-m-d'),
        ]);

        $response = $this->postJson(route('voucher.validate'), [
            'kode' => 'KADALUARSA2026'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Voucher sudah kedaluwarsa.'
        ]);
    }

    /** TC-WBT-CUS008-03 */
    public function test_apply_valid_voucher()
    {
        Voucher::factory()->create([
            'kode_voucher' => 'DISKON10',
            'jenis_voucher' => 'nominal',
            'nilai_diskon' => 10000,
            'nama_voucher' => 'Promo Diskon 10K',
            'deskripsi' => 'Diskon mantap',
            'status' => 'Aktif',
            'tanggal_mulai' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'tanggal_berakhir' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->postJson(route('voucher.validate'), [
            'kode' => 'DISKON10'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'voucher' => [
                'kode_voucher' => 'DISKON10',
                'jenis_voucher' => 'nominal',
                'nilai_diskon' => 10000,
                'nama_voucher' => 'Promo Diskon 10K',
                'deskripsi' => 'Diskon mantap',
            ]
        ]);
    }
}