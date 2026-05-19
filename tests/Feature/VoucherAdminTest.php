<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoucherAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    private function loginAsAdmin()
    {
        User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin',
            'email' => 'test@example.com',
            'role' => 'Admin',
            'password' => Hash::make('admin')
        ]);
        $responseAdmin = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertAuthenticated();
        $responseAdmin->assertRedirect(route('statistik.transaksi'));
    }

    public function setup(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }

    public function test_it_rejects_creation_when_code_is_empty()
    {
        $response = $this->post('/admin/vouchers', [
            'code' => '',
            'discount_amount' => 15000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertStatus(422)
                 ->assertValidationErrors(['code']);
    }

    public function test_it_rejects_creation_if_voucher_code_already_exists()
    {
        Voucher::factory()->create(['code' => 'PROMO2026']);

        $response = $this->post('/admin/vouchers', [
            'code' => 'PROMO2026',
            'discount_amount' => 15000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertStatus(422)
                 ->assertValidationErrors(['code']);
    }

    public function test_it_can_create_a_new_voucher_successfully()
    {
        $response = $this->post('/admin/vouchers', [
            'code' => 'KILAT50',
            'discount_amount' => 50000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertStatus(201); // Created
        $this->assertDatabaseHas('vouchers', [
            'code' => 'KILAT50',
            'discount_amount' => 50000
        ]);
    }

    public function test_it_can_update_an_existing_voucher()
    {
        $voucher = Voucher::factory()->create([
            'code' => 'AWAL',
            'discount_amount' => 10000
        ]);

        $response = $this->put('/admin/vouchers/' . $voucher->id, [
            'code' => 'AWAL', 
            'discount_amount' => 25000, 
            'valid_until' => '2026-12-31'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'discount_amount' => 25000
        ]);
    }

    public function it_can_delete_a_voucher()
    {
        $voucher = Voucher::factory()->create();

        $response = $this->delete('/admin/vouchers/' . $voucher->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vouchers', [
            'id' => $voucher->id
        ]);
    }
}
// <?php

// namespace Tests\Feature;

// use Tests\TestCase;
// use App\Models\Voucher;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Carbon\Carbon;

// class VoucherAdminTest extends TestCase
// {
//     use RefreshDatabase;

//     /** TC-WBT-ADM007-01 */
//     public function test_store_fails_if_code_already_exists()
//     {
//         Voucher::factory()->create([
//             'code' => 'PROMOSELLER'
//         ]);

//         $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
//             'code' => 'PROMOSELLER',
//             'amount' => 10000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertSessionHasErrors('code');
//         $this->assertDatabaseCount('vouchers', 1);
//     }

//     /** TC-WBT-ADM007-02 */
//     public function test_store_success()
//     {
//         $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
//             'code' => 'NEWPROMO',
//             'amount' => 10000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertRedirect(action([\App\Http\Controllers\VoucherController::class, 'index']));

//         $this->assertDatabaseHas('vouchers', [
//             'code' => 'NEWPROMO',
//             'amount' => 10000
//         ]);
//     }

//     /** TC-WBT-ADM007-03 */
//     public function test_update_returns_404_if_not_found()
//     {
//         $this->withoutExceptionHandling();

//         $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

//         $this->put(action([\App\Http\Controllers\VoucherController::class, 'update'], 9999), [
//             'code' => 'UPDATE1',
//             'amount' => 10000
//         ]);
//     }

//     /** TC-WBT-ADM007-04 */
//     public function test_update_success()
//     {
//         $voucher = Voucher::factory()->create();

//         $response = $this->put(
//             action([\App\Http\Controllers\VoucherController::class, 'update'], $voucher->id),
//             [
//                 'code' => $voucher->code,
//                 'amount' => 20000,
//                 'valid_until' => $voucher->valid_until
//             ]
//         );

//         $response->assertRedirect();

//         $this->assertDatabaseHas('vouchers', [
//             'id' => $voucher->id,
//             'amount' => 20000
//         ]);
//     }

//     /** TC-WBT-ADM007-05 */
//     public function test_destroy_success()
//     {
//         $voucher = Voucher::factory()->create();

//         $response = $this->delete(
//             action([\App\Http\Controllers\VoucherController::class, 'destroy'], $voucher->id)
//         );

//         $response->assertRedirect();

//         $this->assertDatabaseMissing('vouchers', [
//             'id' => $voucher->id
//         ]);
//     }

//     /** TC-WBT-ADM007-06 */
//     public function test_store_fails_if_code_empty()
//     {
//         $response = $this->post(action([\App\Http\Controllers\VoucherController::class, 'store']), [
//             'code' => '',
//             'amount' => 15000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertSessionHasErrors('code');
//         $this->assertDatabaseCount('vouchers', 0);
//     }
// }