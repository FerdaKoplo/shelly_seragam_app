<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KustomisasiCustomerTest extends TestCase
{
    public function test_kustomisasi_page_renders_with_required_aspek_controls(): void
    {
        $response = $this->get(route('kustom'));

        $response->assertOk();
        $response->assertViewIs('pages.guest.kustom.index');
        $response->assertSee('Produk Kustom');
        $response->assertSee('Section Atasan');
        $response->assertSee('Section Bawahan');
        $response->assertSee('Kombinasi Jenis Kain');
        $response->assertSee('Jumlah Titik Bordir');
        $response->assertSee('Catatan');
        $response->assertSee('Ukuran');
        $response->assertSee('Upload Design, Badge & keperluan lainnya', false);
        $response->assertSee('up to 5MB per file');
    }

    public function test_kustomisasi_upload_rejects_file_larger_than_5mb(): void
    {
        $design = UploadedFile::fake()->create('badge.png', 5121, 'image/png');

        $response = $this->from(route('checkout'))->post(route('checkout'), [
            'type' => 'kustom',
            'design_files' => [$design],
            'total_quantity' => 1,
            'category' => 'bundle',
            'estimated_total' => 1750000,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('design_files.0');
    }
}
