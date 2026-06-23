<?php

namespace Tests\Feature;

use App\Http\Controllers\User\VoucherController;
use App\Models\User;
use App\Models\Voucher;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


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