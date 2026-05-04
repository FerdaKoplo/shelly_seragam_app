<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class VoucherCustomerTest extends TestCase
{
    use RefreshDatabase;

    /** TC-WBT-CUS006-TDD-01 */
    public function test_apply_voucher_not_found()
    {
        $response = $this->post(
            action([\App\Http\Controllers\ApplyVoucherController::class, 'apply']),
            [
                'code' => 'RANDOM-INVALID',
                'price' => 50000
            ]
        );

        $response->assertSessionHasErrors('code');
    }

    /** TC-WBT-CUS006-TDD-02 */
    public function test_apply_expired_voucher()
    {
        Voucher::factory()->create([
            'code' => 'KADALUARSA2026',
            'valid_until' => Carbon::yesterday()
        ]);

        $response = $this->post(
            action([\App\Http\Controllers\ApplyVoucherController::class, 'apply']),
            [
                'code' => 'KADALUARSA2026',
                'price' => 50000
            ]
        );

        $response->assertSessionHasErrors('code');
    }

    /** TC-WBT-CUS006-TDD-03 */
    public function test_apply_valid_voucher()
    {
        Voucher::factory()->create([
            'code' => 'DISKON10',
            'amount' => 10000,
            'valid_until' => Carbon::tomorrow()
        ]);

        $response = $this->post(
            action([\App\Http\Controllers\ApplyVoucherController::class, 'apply']),
            [
                'code' => 'DISKON10',
                'price' => 50000
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'final_price' => 40000
        ]);
    }
}