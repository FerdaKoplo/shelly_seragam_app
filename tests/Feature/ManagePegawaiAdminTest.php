<?php

namespace Tests\Feature;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManagePegawaiAdminTest extends TestCase
{
    use RefreshDatabase;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }

    // TC-WBT-ADM002-01 
    public function test_store_fails_when_nama_is_empty()
    {
        $response = $this->post(route('manage.pegawai.store'), [
            'nama' => '', 
            'username' => 'dewi.kusuma',
            'password' => 'Pegawai@2026!',
            'status' => 'Active',
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->assertDatabaseMissing('user', [
            'username' => 'dewi.kusuma'
        ]);
    }

    // TC-WBT-ADM002-02
    public function test_store_fails_when_username_is_duplicated()
    {
        User::create([
            'nama' => 'Existing User',
            'username' => 'budi.santoso',
            'email' => 'budi.existing@example.com',
            'role' => 'Pegawai',
            'password' => Hash::make('password'),
            'status' => 'Active'
        ]);

        $response = $this->post(route('manage.pegawai.store'), [
            'nama' => 'Budi Santoso',
            'username' => 'budi.santoso', 
            'password' => 'Test@1234',
            'status' => 'Active',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    // TC-WBT-ADM002-03
    public function test_update_can_toggle_status_to_inactive()
    {
        $pegawai = User::create([
            'nama' => 'Ahmad Fauzi',
            'username' => 'ahmad.fauzi',
            'email' => 'ahmad@example.com',
            'role' => 'Pegawai',
            'password' => Hash::make('password'),
            'status' => 'Active'
        ]);

        $response = $this->put(route('manage.pegawai.update', $pegawai->user_id), [
            'nama' => 'Ahmad Fauzi',
            'username' => 'ahmad.fauzi',
            'status' => 'Inactive',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('user', [
            'user_id' => $pegawai->user_id,
            'status' => 'Inactive'
        ]);
    }

    // TC-WBT-ADM002-04
    public function test_destroy_fails_when_user_has_active_transactions()
    {
        $pegawai = User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi.santoso_trx',
            'email' => 'buditrx@example.com',
            'role' => 'Pegawai',
            'password' => Hash::make('password'),
            'status' => 'Active'
        ]);

        DB::table('transaksi')->insert([
            'user_id' => $pegawai->user_id,
            'nama_customer' => 'Customer Test',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Alamat Test',
            'no_resi_customer' => 'RESI123456',
            'tanggal_transaksi' => '2026-01-01',
            'total_harga' => 150000,
        ]);

        $response = $this->delete(route('manage.pegawai.destroy', $pegawai->user_id));

        $response->assertSessionHas('error', 'Pegawai tidak dapat dihapus karena memiliki transaksi aktif');
        
        $this->assertDatabaseHas('user', [
            'user_id' => $pegawai->user_id
        ]);
    }

}
