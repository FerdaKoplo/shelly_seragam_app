<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutOrderSuccessTest extends TestCase
{
    /** TC-WBT-CUS006 — pesan setelah pembayaran / pesanan dibuat */
    public function test_checkout_success_query_redirects_with_cs_message(): void
    {
        $expected = 'Pesanan berhasil dibuat, Anda akan dihubungi oleh CS untuk konfirmasi dan finalisasi harga.';

        $response = $this->get('/checkout?checkout_success=1&type=katalog');

        $response->assertRedirect(route('checkout', ['type' => 'katalog']));
        $response->assertSessionHas('success', $expected);
    }
}
