<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class KatalogPegawaiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;


    private function loginAsPegawai()
    {
        User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi.santoso',
            'email' => 'budisantoso@example.com',
            'role' => 'Pegawai',
            'password' => Hash::make('pegawai')
        ]);
        $responsePegawai = $this->post('/login', [
            'username' => 'budi.santoso',
            'password' => 'pegawai',
        ]);

        $this->assertAuthenticated();
        $responsePegawai->assertRedirect(route('manage.transaksi'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsPegawai();
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

    // TC-WBT-PGW003-02

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
            'kategori' => 'Polo Shirt Hitam',
            'harga' => 115000,
            'stok' => 15,
            'status' => 'Tersedia',
            'deskripsi' => 'Polo shirt PE premium',
        ]);

        $this->assertDatabaseHas('produk_katalog', [
            'produk_id' => $katalog->produk_id,
            'kategori' => 'Polo Shirt Hitam',
            'harga' => 115000,
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

    // TC-WBT-PGW002-01

        public function test_validate_server_side_validation_price_field_non_numeric(): void
    {
        $response = $this->from(route('manage.katalog.create'))
            ->post(route('manage.katalog.store'), [
                'nama_produk' => 'Celana Test',
                'kategori' => 'celana',
                'harga' => 'seratus dua puluh ribu',
                'stok' => 10,
                'deskripsi' => 'Test deskripsi',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('harga');

    }


}
