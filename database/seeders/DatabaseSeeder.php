<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin',
            'email' => 'test@example.com',
            'role' => 'Admin',
            'password' => Hash::make('admin')
        ]);

        \App\Models\User::create([
            'nama' => 'Test User Pegawai',
            'username' => 'pegawai',
            'email' => 'testpgw@example.com',
            'role' => 'Admin',
            'password' => Hash::make('pegawai')
        ]);

        $this->call([
            ProdukSeeder::class,
        ]);
    }
}
