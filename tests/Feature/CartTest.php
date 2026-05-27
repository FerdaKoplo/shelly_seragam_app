<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a product catalog item.
     */
    private function createKatalog(array $override = [])
    {
        $produk = Produk::create([
            'nama_produk' => $override['nama_produk'] ?? 'Rompi Kerja Premium',
            'deskripsi' => 'Deskripsi rompi berkualitas tinggi.',
            'jenis_produk' => 'katalog',
        ]);

        return ProdukKatalog::create([
            'produk_id' => $produk->produk_id,
            'kategori' => $override['kategori'] ?? 'Rompi',
            'harga' => $override['harga'] ?? 150000,
            'stok' => $override['stok'] ?? 10,
            'status' => 'Tersedia',
        ]);
    }

    /**
     * Test accessing the cart index shows empty state when cart session is empty.
     */
    public function test_cart_index_shows_empty_cart(): void
    {
        $response = $this->get(route('keranjang'));

        $response->assertOk();
        $response->assertViewIs('pages.guest.keranjang.index');
        $response->assertViewHas('items', []);
        $response->assertViewHas('subtotal', 0);
        $response->assertViewHas('notes', '');
    }

    /**
     * Test accessing the cart index displays session items and calculates subtotal correctly.
     */
    public function test_cart_index_shows_items_and_subtotal(): void
    {
        $response = $this->withSession([
            'cart' => [
                1 => [
                    'id' => 1,
                    'katalog_id' => 1,
                    'name' => 'Batik Parang',
                    'price' => 100000,
                    'quantity' => 2,
                    'image' => null,
                ],
                2 => [
                    'id' => 2,
                    'katalog_id' => 2,
                    'name' => 'Kaos Olahraga',
                    'price' => 75000,
                    'quantity' => 1,
                    'image' => 'uploads/catalog/kaos.png',
                ]
            ],
            'cart_notes' => 'Kirim ukuran L semua'
        ])->get(route('keranjang'));

        $response->assertOk();
        $response->assertViewIs('pages.guest.keranjang.index');
        $response->assertViewHas('items');
        $response->assertViewHas('subtotal', 275000); // (100000 * 2) + (75000 * 1)
        $response->assertViewHas('notes', 'Kirim ukuran L semua');

        $response->assertSee('Batik Parang');
        $response->assertSee('Kaos Olahraga');
        $response->assertSee('Kirim ukuran L semua');
    }

    /**
     * Test adding a new item to cart stores it in session properly.
     */
    public function test_cart_add_adds_new_item_to_session(): void
    {
        $katalog = $this->createKatalog([
            'nama_produk' => 'Rompi Eksklusif',
            'harga' => 150000
        ]);

        $response = $this->post(route('cart.add', $katalog->katalog_id), [
            'quantity' => 3
        ]);

        $response->assertRedirect(route('keranjang'));
        
        $this->assertEquals(3, session("cart.{$katalog->katalog_id}.quantity"));
        $this->assertEquals('Rompi Eksklusif', session("cart.{$katalog->katalog_id}.name"));
        $this->assertEquals(150000, session("cart.{$katalog->katalog_id}.price"));
    }

    /**
     * Test adding an already existing item to cart increments its quantity.
     */
    public function test_cart_add_increments_existing_item_quantity(): void
    {
        $katalog = $this->createKatalog([
            'nama_produk' => 'Rompi Eksklusif',
            'harga' => 150000
        ]);

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Eksklusif',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => null,
                ]
            ]
        ])->post(route('cart.add', $katalog->katalog_id), [
            'quantity' => 3
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertEquals(5, session("cart.{$katalog->katalog_id}.quantity")); // 2 + 3
    }

    /**
     * Test incrementing quantity of an item via cart.update route.
     */
    public function test_cart_update_increment_quantity(): void
    {
        $katalog = $this->createKatalog();

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Kerja Premium',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => null,
                ]
            ]
        ])->patch(route('cart.update', $katalog->katalog_id), [
            'action' => 'increment'
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertEquals(3, session("cart.{$katalog->katalog_id}.quantity"));
    }

    /**
     * Test decrementing quantity of an item via cart.update route.
     */
    public function test_cart_update_decrement_quantity(): void
    {
        $katalog = $this->createKatalog();

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Kerja Premium',
                    'price' => 150000,
                    'quantity' => 3,
                    'image' => null,
                ]
            ]
        ])->patch(route('cart.update', $katalog->katalog_id), [
            'action' => 'decrement'
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertEquals(2, session("cart.{$katalog->katalog_id}.quantity"));
    }

    /**
     * Test decrementing quantity removes the item from session when quantity reaches 0 or below.
     */
    public function test_cart_update_decrement_removes_item_when_quantity_reaches_zero(): void
    {
        $katalog = $this->createKatalog();

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Kerja Premium',
                    'price' => 150000,
                    'quantity' => 1,
                    'image' => null,
                ]
            ]
        ])->patch(route('cart.update', $katalog->katalog_id), [
            'action' => 'decrement'
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertFalse(session()->has("cart.{$katalog->katalog_id}"));
    }

    /**
     * Test updating to a custom direct quantity.
     */
    public function test_cart_update_custom_quantity(): void
    {
        $katalog = $this->createKatalog();

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Kerja Premium',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => null,
                ]
            ]
        ])->patch(route('cart.update', $katalog->katalog_id), [
            'quantity' => 8
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertEquals(8, session("cart.{$katalog->katalog_id}.quantity"));
    }

    /**
     * Test removing an item from the cart session.
     */
    public function test_cart_remove_removes_item_from_session(): void
    {
        $katalog = $this->createKatalog();

        $response = $this->withSession([
            'cart' => [
                $katalog->katalog_id => [
                    'id' => $katalog->katalog_id,
                    'katalog_id' => $katalog->katalog_id,
                    'name' => 'Rompi Kerja Premium',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => null,
                ]
            ]
        ])->delete(route('cart.remove', $katalog->katalog_id));

        $response->assertRedirect(route('keranjang'));
        $this->assertFalse(session()->has("cart.{$katalog->katalog_id}"));
    }

    /**
     * Test updating cart notes via regular request and JSON request.
     */
    public function test_cart_update_notes_saves_to_session(): void
    {
        // 1. Regular Request Redirects
        $response = $this->patch(route('cart.notes.update'), [
            'notes' => 'Tolong dibungkus kado'
        ]);

        $response->assertRedirect(route('keranjang'));
        $this->assertEquals('Tolong dibungkus kado', session('cart_notes'));

        // 2. JSON Request responds with JSON
        $responseJson = $this->patchJson(route('cart.notes.update'), [
            'notes' => 'Ganti instruksi pengiriman'
        ]);

        $responseJson->assertOk();
        $responseJson->assertJson(['ok' => true]);
        $this->assertEquals('Ganti instruksi pengiriman', session('cart_notes'));

        // 3. Empty note removes it from session
        $responseEmpty = $this->patch(route('cart.notes.update'), [
            'notes' => ''
        ]);

        $responseEmpty->assertRedirect(route('keranjang'));
        $this->assertFalse(session()->has('cart_notes'));
    }

    /**
     * Test clearing all cart items and notes from the session.
     */
    public function test_cart_clear_clears_all_session_data(): void
    {
        $response = $this->withSession([
            'cart' => [
                1 => [
                    'id' => 1,
                    'katalog_id' => 1,
                    'name' => 'Batik Parang',
                    'price' => 100000,
                    'quantity' => 2,
                    'image' => null,
                ]
            ],
            'cart_notes' => 'Kirim ukuran L semua'
        ])->delete(route('cart.clear'));

        $response->assertRedirect(route('keranjang'));
        $this->assertFalse(session()->has('cart'));
        $this->assertFalse(session()->has('cart_notes'));
    }
}
