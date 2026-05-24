<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_checkout_ditolak_jika_field_kosong(): void
    {
        session(['cart' => [
            1 => [
                'id' => 1,
                'quantity' => 1,
                'price' => 100000
            ]
        ]]);

        $payload = [
            'type'        => 'katalog',
            'full_name'   => '',
            'email'       => 'test@gmail.com',
            'phone'       => '082134567890',
            'address'     => 'Jl. Diponegoro No. 12',
            'city'        => 'Surabaya',
            'province'    => 'Jawa Timur',
            'postal_code' => '60241',
            'shipping_id' => 'reg',
            'notes'       => '',
        ];

        $response = $this->post(route('checkout'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['full_name']);
    }

    public function test_checkout_produk_kustom_diarahkan_tanpa_payment_gateway(): void
    {
        $payload = [
            'type'        => 'kustom',
            'full_name'   => 'Iwan Santoso',
            'email'       => 'iwan@gmail.com',
            'phone'       => '082134567891',
            'address'     => 'Jl. Jetis Kulon No 123',
            'city'        => 'Surabaya',
            'province'    => 'Jawa Timur',
            'postal_code' => '60241',
            'shipping_id' => 'reg',
            'notes'       => 'Produk kustom',
            // jika ada field khusus kustom (yg wajib) tambah disini
        ];

        $response = $this->post(route('checkout'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('checkout', ['type' => 'kustom']));
        $response->assertSessionHas('success', 'Pesanan berhasil dibuat, Anda akan dihubungi oleh CS untuk konfirmasi dan finalisasi harga.');
    }

    public function test_checkout_produk_katalog_diarahkan_ke_payment_gateway(): void {
        putenv('XENDIT_SECRET_KEY=dummy_secret_key_for_testing');

        $fakeInvoiceUrl = 'https://checkout.xendit.co/web/123456789';
        Http::fake([
            '*/v2/invoices' => Http::response([
                'invoice_url' => $fakeInvoiceUrl
            ], 200)
        ]);

        session(['cart' => [
            1 => [
                'id' => 1,
                'quantity' => 2,
                'price' => 150000
            ]
        ]]);

        $payload = [
            'type'           => 'katalog',
            'payment_method' => 'xendit',
            'full_name'      => 'Putra Ramdani',
            'email'          => 'putra@gmail.com',
            'phone'          => '082134567892',
            'address'        => 'Jl. Karah No 123',
            'city'           => 'Surabaya',
            'province'       => 'Jawa Timur',
            'postal_code'    => '60241',
            'shipping_id'    => 'exp',
            'notes'          => '',
        ];

        $response = $this->post(route('checkout'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($fakeInvoiceUrl);
    }
}
