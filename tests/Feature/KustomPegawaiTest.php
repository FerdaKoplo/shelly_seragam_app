<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\ProdukKustom;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KustomPegawaiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    private function loginAsPegawai()
    {
        User::firstOrCreate(
            ['username' => 'budi.santoso'],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budisantoso@example.com',
                'role' => 'Pegawai',
                'password' => Hash::make('pegawai')
            ]
        );

        $responsePegawai = $this->post('/login', [
            'username' => 'budi.santoso',
            'password' => 'pegawai',
        ]);

        $this->assertAuthenticated();
        $responsePegawai->assertRedirect(route('manage.transaksi'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsPegawai();
    }

    // TC-WBT-PGW003-01
    public function test_reject_aspect_name_duplication()
    {
        $produk = Produk::create([
            'nama_produk' => 'Kustom Variasi Kombinasi',
            'jenis_produk' => 'kustom',
            'deskripsi' => '-'
        ]);

        ProdukKustom::create([
            'produk_id' => $produk->produk_id,
            'spesifikasi_khusus' => 'Variasi Kombinasi Kain',
        ]);

        $payload = [
            'sections' => [
                [
                    'name' => 'Variasi Kombinasi Kain',
                    'show_kombinasi' => '0',
                    'show_bordir' => '0',
                ]
            ],
            'show_catatan' => '0',
            'show_upload' => '0',
            'show_ukuran' => '0',
        ];

        $response = $this->post(route('manage.kustom.store'), $payload);

        $response->assertSessionHas('error', 'Aspek Sudah Pernah Ditambahkan');

        $this->assertDatabaseCount('produk_kustom', 1);
    }

    public function test_reject_delete_if_transaction_still_presented()
    {
        $produk = Produk::create([
            'nama_produk' => 'Kustom Variasi Kombinasi',
            'jenis_produk' => 'kustom',
            'deskripsi' => '-'
        ]);

        $kustom = ProdukKustom::create([
            'produk_id' => $produk->produk_id,
            'spesifikasi_khusus' => 'Variasi Kombinasi Kain',
        ]);

        $pegawaiId = User::where('username', 'budi.santoso')->first()->user_id;

        $transaksiId = DB::table('transaksi')->insertGetId([
            'pegawai_id' => $pegawaiId,
            'nama_customer' => 'Test Customer',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Test Address',
            'no_resi_customer' => 'RESI-PGW-001',
            'tanggal_transaksi' => now(),
            'total_harga' => 100000,
            'status' => 'Paid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_transaksi_kustom')->insert([
            'transaksi_id' => $transaksiId,
            'tipe_kustom' => 'Variasi Kombinasi Kain',
            'quantity' => 1,
            'ukuran_dipilih' => 'L',
            'catatan' => '-',
            'detail_pilihan_kustomisasi' => '-',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete(route('manage.kustom.destroy', $kustom->kustom_id));

        $response->assertSessionHas('error', 'Aspek tidak dapat dihapus karena masih digunakan oleh transaksi aktif');

        $this->assertDatabaseHas('produk_kustom', [
            'kustom_id' => $kustom->kustom_id,
        ]);
    }



}
