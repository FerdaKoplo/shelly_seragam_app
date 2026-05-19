<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
    }
    public function test_if_product_stock_is_0_or_negative(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Kemeja PDL Lengan Panjang',
            'deskripsi' => 'Test deskripsi',
            'jenis_produk' => 'katalog',
        ]);

        $katalog = ProdukKatalog::create([
            'produk_id' => $produk->produk_id ?? $produk->id,
            'kategori' => 'Kemeja',
            'harga' => 150000,
            'stok' => 10,
            'status' => 'Tersedia',
        ]);

        $response = $this->post(route('cart.add', ['katalog_id' => $katalog->katalog_id ?? $katalog->id]), [
            'quantity' => 0,
        ]);

        $response->assertSessionHasErrors([
            'quantity' => 'Kuantitas harus lebih dari 0'
        ]);

        $cart = session('cart', []);
        $this->assertArrayNotHasKey($katalog->katalog_id ?? $katalog->id, $cart);
    }
}
