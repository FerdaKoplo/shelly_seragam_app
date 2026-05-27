<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogPreOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_produk_stok_habis_tetap_tampilkan_opsi_preorder(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Kemeja Preorder',
            'deskripsi' => 'Produk untuk uji pre-order',
            'jenis_produk' => 'katalog',
        ]);

        $katalog = ProdukKatalog::create([
            'produk_id' => $produk->produk_id,
            'kategori' => 'Kemeja',
            'harga' => 120000,
            'stok' => 0,
            'status' => 'Habis',
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('Stok habis. Namun anda tetap bisa melakukan Pre-Order', false);
        $response->assertSee('Pre-Order Sekarang');
        $response->assertSee('Checkout Pre-Order');
    }

    public function test_produk_stok_tersedia_tetap_tampilkan_aksi_normal(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Kemeja Ready',
            'deskripsi' => 'Produk untuk uji stok tersedia',
            'jenis_produk' => 'katalog',
        ]);

        $katalog = ProdukKatalog::create([
            'produk_id' => $produk->produk_id,
            'kategori' => 'Kemeja',
            'harga' => 120000,
            'stok' => 10,
            'status' => 'Tersedia',
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('Stok: 10');
        $response->assertSee('Add To Cart');
        $response->assertSee('Checkout');
        $response->assertDontSee('Checkout Pre-Order');
    }
}
