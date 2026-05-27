<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use App\Models\FotoProdukKatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a product catalog item.
     */
    private function createKatalog(array $override = [])
    {
        $produk = Produk::create([
            'nama_produk' => $override['nama_produk'] ?? 'Kaos Olahraga Premium',
            'deskripsi' => $override['deskripsi'] ?? 'Kaos bahan berkualitas tinggi, adem, cocok untuk olahraga.',
            'jenis_produk' => $override['jenis_produk'] ?? 'katalog',
        ]);

        return ProdukKatalog::create([
            'produk_id' => $produk->produk_id,
            'kategori' => $override['kategori'] ?? 'Kaos',
            'harga' => $override['harga'] ?? 75000,
            'stok' => $override['stok'] ?? 25,
            'status' => $override['status'] ?? 'Tersedia',
        ]);
    }

    /**
     * Test catalog detail page displays correctly.
     */
    public function test_katalog_detail_displays_product_info(): void
    {
        $katalog = $this->createKatalog([
            'nama_produk' => 'Batik Premium Parang',
            'harga' => 125000,
            'kategori' => 'Batik',
            'deskripsi' => 'Batik dengan motif Parang klasik bernilai seni tinggi.',
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertViewIs('pages.guest.katalog.detail');
        $response->assertViewHas('item');

        $response->assertSee('Batik Premium Parang');
        $response->assertSee('Rp125.000');
        $response->assertSee('Batik dengan motif Parang klasik bernilai seni tinggi.');
        $response->assertSee('#Batik');
        $response->assertSee('#katalog');
    }

    /**
     * Test catalog detail page returns 404 for non-existent product catalog ID.
     */
    public function test_katalog_detail_returns_404_for_non_existent_product(): void
    {
        $response = $this->get(route('product.show', 9999));

        $response->assertStatus(404);
    }

    /**
     * Test catalog detail page displays correct information when stock is 0 (Pre-Order state).
     */
    public function test_katalog_detail_shows_preorder_state_when_stock_is_zero(): void
    {
        $katalog = $this->createKatalog([
            'nama_produk' => 'Seragam Kerja Custom PO',
            'stok' => 0,
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('Stok habis. Namun anda tetap bisa melakukan Pre-Order');
        $response->assertSee('Pre-Order Sekarang');
        $response->assertSee('Checkout Pre-Order');
        $response->assertDontSee('Add To Cart');
        $response->assertDontSee('Stok: 0');
    }

    /**
     * Test catalog detail page displays normal checkout actions when stock is greater than 0.
     */
    public function test_katalog_detail_shows_ready_state_when_stock_is_greater_than_zero(): void
    {
        $katalog = $this->createKatalog([
            'nama_produk' => 'Jas Almamater Ready',
            'stok' => 12,
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('Stok: 12');
        $response->assertSee('Add To Cart');
        $response->assertSee('Checkout');
        $response->assertDontSee('Stok habis. Namun anda tetap bisa melakukan Pre-Order');
        $response->assertDontSee('Pre-Order Sekarang');
        $response->assertDontSee('Checkout Pre-Order');
    }

    /**
     * Test catalog detail page displays custom images when they exist.
     */
    public function test_katalog_detail_displays_images_if_exist(): void
    {
        $katalog = $this->createKatalog();

        FotoProdukKatalog::create([
            'produk_id' => $katalog->produk_id,
            'path' => 'uploads/catalog/test_jersey_1.jpg',
        ]);

        FotoProdukKatalog::create([
            'produk_id' => $katalog->produk_id,
            'path' => 'uploads/catalog/test_jersey_2.jpg',
        ]);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('uploads/catalog/test_jersey_1.jpg');
        $response->assertSee('uploads/catalog/test_jersey_2.jpg');
    }

    /**
     * Test catalog detail page displays fallback/picsum images if no custom images exist in the database.
     */
    public function test_katalog_detail_displays_fallback_images_if_no_images(): void
    {
        $katalog = $this->createKatalog();

        // Ensure no images exist in database for this product
        $this->assertDatabaseCount('foto_produk_katalog', 0);

        $response = $this->get(route('product.show', $katalog->katalog_id));

        $response->assertOk();
        $response->assertSee('https://picsum.photos/id/1/1080');
        $response->assertSee('https://picsum.photos/id/2/1080');
        $response->assertSee('https://picsum.photos/id/3/1080');
        $response->assertSee('https://picsum.photos/id/4/1080');
    }
}
