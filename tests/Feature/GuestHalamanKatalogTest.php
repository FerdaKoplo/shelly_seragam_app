<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestHalamanKatalogTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    private function productSeeder()
    {
        $this->seed(\Database\Seeders\ProdukSeeder::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->productSeeder();
    }

    public function test_verifikasi_query_tidak_cocok(): void
    {
        $keyword = 'xyzabc999';
        $response = $this->get(route('katalog', ['search' => $keyword]));

        $response->assertStatus(200);
        $response->assertViewHas('katalog', function ($katalog) {
            return $katalog->isEmpty();
        });

        $response->assertSeeText('Produk Tidak Ditemukan');
    }

    public function test_verifikasi_filter_gabungan_empty(): void
    {
        $queryParams = [
            'filter_kategori' => 'Bawahan',
            'filter_ukuran'   => 'XXXL',
            'filter_status'   => 'ready',
        ];
        $response = $this->get(route('katalog', $queryParams));

        $response->assertStatus(200);
        $response->assertViewHas('katalog', function ($katalog) {
            return $katalog->isEmpty();
        });

        $response->assertSeeText('Produk Tidak Ditemukan');
    }

    public function test_verifikasi_detail_id_produk_tidak_ada(): void
    {
        $invalidId = 99999;

        $response = $this->get("/katalog/detail/{$invalidId}");

        $response->assertStatus(404);
        $response->assertNotFound();
    }
}
