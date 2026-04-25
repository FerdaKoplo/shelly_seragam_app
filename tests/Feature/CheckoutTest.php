<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    public function test_checkout_get_maps_cart_session_items_and_notes(): void
    {
        $response = $this->withSession([
            'cart' => [
                10 => [
                    'id' => 10,
                    'katalog_id' => 10,
                    'name' => 'Rompi Lapangan',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => 'uploads/catalog/rompi.png',
                ],
            ],
            'cart_notes' => 'Tolong kirim cepat',
        ])->get(route('checkout'));

        $response->assertOk();
        $response->assertViewIs('pages.guest.checkout.checkout');
        $response->assertViewHas('type', 'katalog');
        $response->assertViewHas('checkoutNotes', 'Tolong kirim cepat');
        $response->assertViewHas('items', function (array $items) {
            if (count($items) !== 1) {
                return false;
            }

            $item = $items[0];

            return $item['katalog_id'] === 10
                && $item['name'] === 'Rompi Lapangan'
                && $item['price'] === 150000
                && $item['quantity'] === 2
                && str_contains($item['image_url'], '/storage/uploads/catalog/rompi.png');
        });
    }

    public function test_checkout_post_uploads_supported_design_file(): void
    {
        Storage::fake('public');
        $design = UploadedFile::fake()->image('design.png');

        $response = $this->post(route('checkout'), [
            'type' => 'kustom',
            'design_files' => [$design],
            'total_quantity' => 8,
            'category' => 'atasan',
            'estimated_total' => 880000,
            'notes' => 'Tambahkan bordir nama',
        ]);

        $response->assertOk();
        Storage::disk('public')->assertExists('uploads/kustom/' . $design->hashName());
        $response->assertViewHas('type', 'kustom');
        $response->assertViewHas('customData', function (array $customData) {
            if (count($customData['attachments']) !== 1) {
                return false;
            }

            $attachment = $customData['attachments'][0];

            return $attachment['name'] === 'design.png'
                && $attachment['extension'] === 'png'
                && str_contains($attachment['url'], '/storage/uploads/kustom/');
        });
    }

    public function test_checkout_post_rejects_unsupported_design_file_extension(): void
    {
        $design = UploadedFile::fake()->create('design.pdf', 100, 'application/pdf');

        $response = $this->from(route('checkout'))->post(route('checkout'), [
            'type' => 'kustom',
            'design_files' => [$design],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('design_files.0');
    }
}
