<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrafficWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin_traffic',
            'email' => 'traffic@example.com',
            'role' => 'Admin',
            'password' => \Hash::make('password'),
        ]);

        $this->actingAs($admin);
    }

    public function test_traffic_page_handles_empty_dataset(): void
    {
        $response = $this->get(route('traffic'));

        $response->assertOk();
        $response->assertViewIs('pages.user.admin.traffic.index');
        $response->assertSee('Traffic Website');
    }
}
