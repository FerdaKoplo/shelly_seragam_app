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

        User::updateOrCreate(
            ['username' => 'admin'], 
            [
                'nama' => 'Test User Admin',
                'email' => 'test@example.com',
                'role' => 'Admin',
                'status' => 'Active',
                'password' => Hash::make('admin')
            ]
        );

        // Pegawai
        User::updateOrCreate(
            ['username' => 'budi.santoso'], 
            [
                'nama' => 'Budi Santoso',
                'email' => 'budisantoso@example.com',
                'role' => 'Pegawai',
                'status' => 'Active',
                'password' => Hash::make('pegawai')
            ]
        );
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
    public function login_fails_when_password_incorrect()
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

    /** @test */
    public function login_fails_when_password_empty()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function login_fails_when_username_not_found()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'not_exist_user',
            'password' => 'anypassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function login_fails_if_user_not_active()
    {
        $user = User::where('username', 'admin')->first();
        $user->update(['status' => 'Inactive']);

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }
}
