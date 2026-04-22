<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManagePegawaiAdminTest extends TestCase
{
    use RefreshDatabase;

     private function loginAsAdmin()
    {
        User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin',
            'email' => 'test@example.com',
            'role' => 'Admin',
            'password' => Hash::make('admin')
        ]);
        $responseAdmin = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertAuthenticated();
        $responseAdmin->assertRedirect(route('statistik.transaksi'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }

}
