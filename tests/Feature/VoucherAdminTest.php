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

    public function it_rejects_creation_when_code_is_empty()
    {
        $response = $this->post('/admin/vouchers', [
            'code' => '',
            'discount_amount' => 15000,
            'valid_until' => '2026-12-31'
        ]);

        $response->assertStatus(422)
                 ->assertValidationErrors(['code']);
    }

    public function it_rejects_creation_if_voucher_code_already_exists()
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

    public function it_can_create_a_new_voucher_successfully()
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

    public function it_can_update_an_existing_voucher()
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
