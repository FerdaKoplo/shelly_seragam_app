<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use App\Models\User;
use Database\Factories\UserFactory;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KatalogAdminTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }


    private function createKatalog(array $override = [])
    {
        $produk = Produk::create([
            'nama_produk' => $override['nama_produk'] ?? 'Rompi Lapangan',
            'deskripsi' => 'Test deskripsi',
            'jenis_produk' => 'katalog',
        ]);

        return ProdukKatalog::create([
            'produk_id' => $produk->produk_id,
            'kategori' => $override['kategori'] ?? 'Rompi',
            'harga' => $override['harga'] ?? 150000,
            'stok' => $override['stok'] ?? 10,
            'status' => $override['status'] ?? 'Tersedia',
        ]);
    }

    public function test_create_katalog()
    {
        $response = $this->post(route('manage.katalog.store'), [
            'nama_produk' => 'Kemeja Test',
            'kategori' => 'Kaos',
            'harga' => 150000,
            'stok' => 10,
            'deskripsi' => 'Test deskripsi',
        ]);

        $response->assertRedirect(route('manage.katalog'));

        $this->assertDatabaseHas('produk_katalog', [
            'kategori' => 'Kaos',
            'harga' => 150000,
            'stok' => 10,
            'status' => 'Tersedia',
        ]);
    }

    public function test_update_katalog()
    {
        $katalog = $this->createKatalog();

        $katalog->update([
            'kategori' => 'Kemeja',
            'harga' => 200000,
            'stok' => 15,
            'status' => 'Tersedia',
            'deskripsi' => 'Test deskripsi',
        ]);

        $this->assertDatabaseHas('produk_katalog', [
            'produk_id' => $katalog->produk_id,
            'kategori' => 'Kemeja',
            'harga' => 200000,
            'stok' => 15,
        ]);
    }

    public function test_archive_katalog()
    {

        $katalog = $this->createKatalog();


        $response = $this->put(route('manage.katalog.archive', $katalog->produk_id));

        $response->assertRedirect(route('manage.katalog'));
        $this->assertDatabaseHas('produk_katalog', [
            'produk_id' => $katalog->produk_id,
            'status' => 'Arsip',
        ]);
    }

    public function test_delete_katalog()
    {
        $katalog = $this->createKatalog(['status' => 'Arsip']);

        $katalog->delete();

        $this->assertDatabaseMissing('produk_katalog', [
            'produk_id' => $katalog->produk_id,
        ]);
    }

    // TC-WBT-ADM003-01
    public function test_validate_server_side_validation_price_field_non_numeric(): void
    {
        $response = $this->from(route('manage.katalog.create'))
            ->post(route('manage.katalog.store'), [
                'nama_produk' => 'Kemeja Test',
                'kategori' => 'Kaos',
                'harga' => 'seratus lima puluh ribu',
                'stok' => 10,
                'deskripsi' => 'Test deskripsi',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('harga');

    }

    // TC-WBT-ADM003-02
    public function test_validate_input_stock_value_negative(): void
    {
        $response = $this->from(route('manage.katalog.create'))
            ->post(route('manage.katalog.store'), [
                'nama_produk' => 'Kemeja Test',
                'kategori' => 'Kaos',
                'harga' => 150000,
                'stok' => -5,
                'deskripsi' => 'Test deskripsi',
            ]);

        $response->assertSessionHasErrors('stok');
    }

    // TC-WBT-ADM003-03
    public function test_validate_status_item_only_archived_when_active(): void
    {
        $katalog = $this->createKatalog(['status' => 'Arsip']);

        $response = $this->from(route('manage.katalog'))
            ->put(route('manage.katalog.archive', $katalog->produk_id));

        $response->assertRedirect(route('manage.katalog'));
        $response->assertSessionHas('error', 'Item sudah dalam status arsip.');
    }

    // TC-WBT-ADM003-04
    public function test_validate_item_only_can_be_deleted_when_archived(): void
    {
        $katalog = $this->createKatalog(['status' => 'Tersedia']);
        $response = $this->from(route('manage.katalog'))
            ->delete(route('manage.katalog.destroy', $katalog->produk_id));

        $response->assertSessionHas('error', 'Hanya item terarsip yang boleh dihapus.');

        $this->assertDatabaseHas('produk_katalog', [
            'produk_id' => $katalog->produk_id,
        ]);
    }
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
