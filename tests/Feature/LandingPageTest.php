<?php

namespace Tests\Feature;

use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_whatsapp_cta_url_is_generated_correctly_from_config(): void
    {
        $testNumber = '6287893385014';
        Config::set('services.whatsapp.number', $testNumber);

        $response = $this->get('/');
        $response->assertStatus(200);

        $expectedUrl = 'https://wa.me/' . $testNumber . '?text=';

        $response->assertSee($expectedUrl, false);


    }
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
