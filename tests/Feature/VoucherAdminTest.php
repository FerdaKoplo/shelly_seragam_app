<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class VoucherAdminTest extends TestCase
{
    use RefreshDatabase;

    /** TC-WBT-ADM007-01 */
    public function test_store_fails_if_code_already_exists()
    {
        Voucher::factory()->create([
            'code' => 'PROMOSELLER'
        ]);

        $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
            'code' => 'PROMOSELLER',
            'amount' => 10000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseCount('vouchers', 1);
    }

    /** TC-WBT-ADM007-02 */
    public function test_store_success()
    {
        $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
            'code' => 'NEWPROMO',
            'amount' => 10000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertRedirect(action([\App\Http\Controllers\VoucherController::class, 'index']));

        $this->assertDatabaseHas('vouchers', [
            'code' => 'NEWPROMO',
            'amount' => 10000
        ]);
    }

    /** TC-WBT-ADM007-03 */
    public function test_update_returns_404_if_not_found()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->put(action([\App\Http\Controllers\VoucherController::class, 'update'], 9999), [
            'code' => 'UPDATE1',
            'amount' => 10000
        ]);
    }

    /** TC-WBT-ADM007-04 */
    public function test_update_success()
    {
        $voucher = Voucher::factory()->create();

        $response = $this->put(
            action([\App\Http\Controllers\VoucherController::class, 'update'], $voucher->id),
            [
                'code' => $voucher->code,
                'amount' => 20000,
                'valid_until' => $voucher->valid_until
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'amount' => 20000
        ]);
    }

    /** TC-WBT-ADM007-05 */
    public function test_destroy_success()
    {
        $voucher = Voucher::factory()->create();

        $response = $this->delete(
            action([\App\Http\Controllers\VoucherController::class, 'destroy'], $voucher->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseMissing('vouchers', [
            'id' => $voucher->id
        ]);
    }

    /** TC-WBT-ADM007-06 */
    public function test_store_fails_if_code_empty()
    {
        $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
            'code' => '',
            'amount' => 15000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseCount('vouchers', 0);
    }
}