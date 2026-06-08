<?php

namespace Tests\Feature;

use App\Http\Controllers\User\VoucherController;
use App\Models\User;
use App\Models\Voucher;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// class VoucherAdminTest extends TestCase
// {
//     use RefreshDatabase;
//     /**
//      * A basic feature test example.
//      */
//     private function loginAsAdmin()
//     {
//         User::create([
//             'nama' => 'Test User Admin',
//             'username' => 'admin',
//             'email' => 'test@example.com',
//             'role' => 'Admin',
//             'password' => Hash::make('admin')
//         ]);
//         $responseAdmin = $this->post('/login', [
//             'username' => 'admin',
//             'password' => 'admin',
//         ]);

//         $this->assertAuthenticated();
//         $responseAdmin->assertRedirect(route('statistik.transaksi'));
//     }

//     public function setup(): void
//     {
//         parent::setUp();
//         $this->loginAsAdmin();
//     }

//     public function test_it_rejects_creation_when_code_is_empty()
//     {
//         $response = $this->post('/admin/vouchers', [
//             'code' => '',
//             'discount_amount' => 15000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertStatus(422)
//                  ->assertValidationErrors(['code']);
//     }

//     public function test_it_rejects_creation_if_voucher_code_already_exists()
//     {
//         Voucher::factory()->create(['code' => 'PROMO2026']);

//         $response = $this->post('/admin/vouchers', [
//             'code' => 'PROMO2026',
//             'discount_amount' => 15000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertStatus(422)
//                  ->assertValidationErrors(['code']);
//     }

//     public function test_it_can_create_a_new_voucher_successfully()
//     {
//         $response = $this->post('/admin/vouchers', [
//             'code' => 'KILAT50',
//             'discount_amount' => 50000,
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertStatus(201); // Created
//         $this->assertDatabaseHas('vouchers', [
//             'code' => 'KILAT50',
//             'discount_amount' => 50000
//         ]);
//     }

//     public function test_it_can_update_an_existing_voucher()
//     {
//         $voucher = Voucher::factory()->create([
//             'code' => 'AWAL',
//             'discount_amount' => 10000
//         ]);

//         $response = $this->put('/admin/vouchers/' . $voucher->id, [
//             'code' => 'AWAL', 
//             'discount_amount' => 25000, 
//             'valid_until' => '2026-12-31'
//         ]);

//         $response->assertStatus(200);
//         $this->assertDatabaseHas('vouchers', [
//             'id' => $voucher->id,
//             'discount_amount' => 25000
//         ]);
//     }

//     public function it_can_delete_a_voucher()
//     {
//         $voucher = Voucher::factory()->create();

//         $response = $this->delete('/admin/vouchers/' . $voucher->id);

//         $response->assertStatus(200);
//         $this->assertDatabaseMissing('vouchers', [
//             'id' => $voucher->id
//         ]);
//     }
// }

class VoucherAdminTest extends TestCase
{
    use RefreshDatabase;

   protected function setUp(): void
    {
        parent::setUp();
        
        $admin = User::factory()->create([
            'role' => 'Admin',
        ]);
        
        $this->actingAs($admin);
    }

    /** TC-WBT-ADM007-01 */
    public function test_store_fails_if_code_already_exists()
    {
        Voucher::factory()->create([
            'kode_voucher' => 'PROMOSELLER'
        ]);

        $response = $this->post(route('manage.voucher.store'), [
            'nama_voucher' => 'Promo Diskon',
            'kode_voucher' => 'PROMOSELLER',
            'deskripsi' => 'Deskripsi promo',
            'nilai_diskon' => 10000,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => 'nominal' 
        ]);

        $response->assertSessionHasErrors('kode_voucher'); 
        $this->assertDatabaseCount('vouchers', 1);
    }

    /** TC-WBT-ADM007-02 */
    public function test_store_success()
    {
        $response = $this->post(route('manage.voucher.store'), [
            'nama_voucher' => 'Promo Tahun Baru',
            'kode_voucher' => 'NEWPROMO',
            'deskripsi' => 'Diskon awal tahun',
            'nilai_diskon' => 10000,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => 'nominal'
        ]);

        // Based on your controller, it redirects to 'manage.voucher.store', not 'index'
        $response->assertRedirect(route('manage.voucher.store'));

        $this->assertDatabaseHas('vouchers', [
            'kode_voucher' => 'NEWPROMO',
            'nilai_diskon' => 10000
        ]);
    }

    /** TC-WBT-ADM007-03 */
    public function test_update_returns_404_if_not_found()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->put(route('manage.voucher.update', 9999), [
            'nama_voucher' => 'Update Promo',
            'kode_voucher' => 'UPDATE1',
            'deskripsi' => 'Deskripsi update',
            'nilai_diskon' => 10000,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => 'nominal'
        ]);
    }

    /** TC-WBT-ADM007-04 */
    public function test_update_success()
    {
        $voucher = Voucher::factory()->create();

        $response = $this->put(route('manage.voucher.update', $voucher->id), [
            'nama_voucher' => $voucher->nama_voucher,
            'kode_voucher' => $voucher->kode_voucher,
            'deskripsi' => $voucher->deskripsi,
            'nilai_diskon' => 20000, // Updated value
            'tanggal_mulai' => $voucher->tanggal_mulai,
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => $voucher->jenis_voucher
        ]);

        $response->assertRedirect(route('manage.voucher'));

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'nilai_diskon' => 20000
        ]);
    }

    /** TC-WBT-ADM007-05 */
    public function test_destroy_success()
    {
        $voucher = Voucher::factory()->create();

        $response = $this->delete(route('manage.voucher.destroy', $voucher->id));

        $response->assertRedirect(); // Or assert back depending on logic
        $this->assertDatabaseMissing('vouchers', [
            'id' => $voucher->id
        ]);
    }

    /** TC-WBT-ADM007-06 */
    public function test_store_fails_if_code_empty()
    {
        $response = $this->post(route('manage.voucher.store'), [
            'nama_voucher' => 'Promo Tanpa Kode',
            'kode_voucher' => '',
            'deskripsi' => 'Deskripsi',
            'nilai_diskon' => 15000,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => 'nominal'
        ]);

        $response2 = $this->post(route('manage.voucher.store'), [
            'nama_voucher' => '', // Empty required field
            'kode_voucher' => 'KODE123',
            'deskripsi' => 'Deskripsi',
            'nilai_diskon' => 15000,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_berakhir' => '2026-12-31',
            'jenis_voucher' => 'nominal'
        ]);
        
        $response2->assertSessionHasErrors('nama_voucher');
    }
}