<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed users manually to test
        User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin',
            'email' => 'test@example.com',
            'role' => 'Admin',
            'password' => Hash::make('admin')
        ]);

        User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi.santoso',
            'email' => 'budisantoso@example.com',
            'role' => 'Pegawai',
            'password' => Hash::make('pegawai')
        ]);
    }

    /** @test */
    public function logs_in_admin_and_pegawai_correctly()
    {
        // Admin login
        $responseAdmin = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertAuthenticated();
        $responseAdmin->assertRedirect(route('statistik.transaksi'));

        auth()->logout(); // reset session 

        // Pegawai login
        $responsePegawai = $this->post('/login', [
            'username' => 'budi.santoso',
            'password' => 'pegawai',
        ]);

        $this->assertAuthenticated();
        $responsePegawai->assertRedirect(route('manage.transaksi'));
    }

    /** @test */
    public function login_fails_for_invalid_credentials()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function login_fails_when_input_is_empty()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['username', 'password']);
    }
}
