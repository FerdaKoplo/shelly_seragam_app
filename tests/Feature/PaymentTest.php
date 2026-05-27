<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    /**
     * Helper to authenticate as Admin.
     */
    private function loginAsAdmin()
    {
        $this->admin = User::create([
            'nama' => 'Test User Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'Admin',
            'password' => \Hash::make('admin')
        ]);

        $this->actingAs($this->admin);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }

    /**
     * Test transaction dashboard renders successfully and displays transaction list.
     */
    public function test_payment_dashboard_displays_transactions(): void
    {
        $transaksi = Transaksi::create([
            'user_id' => $this->admin->user_id,
            'nama_customer' => 'Customer Andi',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Jl. Merdeka No. 10',
            'no_resi_customer' => 'RESI001',
            'status' => 'Created',
            'tanggal_transaksi' => '2026-05-27',
            'total_harga' => 250000,
        ]);

        \DB::table('order_transaksi_kustom')->insert([
            'transaksi_id' => $transaksi->transaksi_id,
            'quantity' => 10,
            'ukuran_dipilih' => 'M',
            'tipe_kustom' => 'Bundle',
            'catatan' => 'Bahan katun premium',
            'detail_pilihan_kustomisasi' => 'Bordir depan belakang',
        ]);

        $response = $this->get(route('manage.transaksi'));

        $response->assertOk();
        $response->assertViewIs('pages.user.transaksi.index');
        $response->assertViewHas('transaksis');
        $response->assertSee('Customer Andi');
        $response->assertSee('RESI001');
        $response->assertSee('250.000');
    }

    /**
     * Test dashboard transaction searching matches by customer name or transaction ID.
     */
    public function test_payment_dashboard_search_filter(): void
    {
        $t1 = Transaksi::create([
            'user_id' => $this->admin->user_id,
            'nama_customer' => 'Budi Sudarsono',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Alamat Budi',
            'no_resi_customer' => 'RESI-BUDI',
            'status' => 'Paid',
            'tanggal_transaksi' => '2026-05-27',
            'total_harga' => 120000,
        ]);

        $t2 = Transaksi::create([
            'user_id' => $this->admin->user_id,
            'nama_customer' => 'Cynthia Bella',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Alamat Cynthia',
            'no_resi_customer' => 'RESI-CYNTHIA',
            'status' => 'Created',
            'tanggal_transaksi' => '2026-05-27',
            'total_harga' => 500000,
        ]);

        // Search for Budi
        $responseBudi = $this->get(route('manage.transaksi', ['search' => 'Budi']));
        $responseBudi->assertOk();
        $responseBudi->assertSee('Budi Sudarsono');
        $responseBudi->assertDontSee('Cynthia Bella');

        // Search for Cynthia
        $responseCynthia = $this->get(route('manage.transaksi', ['search' => 'Cynthia']));
        $responseCynthia->assertOk();
        $responseCynthia->assertSee('Cynthia Bella');
        $responseCynthia->assertDontSee('Budi Sudarsono');
    }

    /**
     * Test storing a valid new transaction saves it correctly in the database.
     */
    public function test_payment_store_saves_valid_transaction(): void
    {
        $response = $this->post(route('manage.transaksi.store'), [
            'nama_customer' => 'Hendra Setiawan',
            'no_hp_customer' => '089988776655',
            'alamat_customer' => 'Jl. Sudirman No. 45',
            'no_resi_customer' => 'RESI-NEW-100',
            'status' => 'Paid',
            'tanggal_transaksi' => '27-05-2026',
            'total_harga' => 350000,
        ]);

        $response->assertRedirect(route('manage.transaksi'));
        $response->assertSessionHas('success', 'Transaksi berhasil ditambahkan.');

        $this->assertDatabaseHas('transaksi', [
            'nama_customer' => 'Hendra Setiawan',
            'no_hp_customer' => '089988776655',
            'no_resi_customer' => 'RESI-NEW-100',
            'status' => 'Paid',
            'tanggal_transaksi' => '2026-05-27', // formats appropriately via controller
            'total_harga' => 350000,
        ]);
    }

    /**
     * Test validation failure triggers when creating a transaction with empty values.
     */
    public function test_payment_store_validation_errors(): void
    {
        $response = $this->from(route('manage.transaksi'))->post(route('manage.transaksi.store'), [
            'nama_customer' => '',
            'no_hp_customer' => '',
            'alamat_customer' => '',
            'no_resi_customer' => '',
            'status' => 'InvalidStatus',
            'tanggal_transaksi' => 'not-a-date',
            'total_harga' => -50,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('manage.transaksi'));
        $response->assertSessionHasErrors([
            'nama_customer',
            'no_hp_customer',
            'alamat_customer',
            'no_resi_customer',
            'status',
            'tanggal_transaksi',
            'total_harga'
        ]);
    }

    /**
     * Test updating a transaction successfully modifies its status and resi number.
     */
    public function test_payment_update_modifies_status_and_resi(): void
    {
        $transaksi = Transaksi::create([
            'user_id' => $this->admin->user_id,
            'nama_customer' => 'Dian Sastro',
            'no_hp_customer' => '08123456789',
            'alamat_customer' => 'Alamat Dian',
            'no_resi_customer' => 'RESI-DRAFT',
            'status' => 'Created',
            'tanggal_transaksi' => '2026-05-27',
            'total_harga' => 150000,
        ]);

        $response = $this->from(route('manage.transaksi'))->put(route('manage.transaksi.update', $transaksi->transaksi_id), [
            'no_resi_customer' => 'RESI-PAID-OK',
            'status' => 'Paid'
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('transaksi', [
            'transaksi_id' => $transaksi->transaksi_id,
            'no_resi_customer' => 'RESI-PAID-OK',
            'status' => 'Paid'
        ]);
    }

    /**
     * Test fetching shipping cost from RajaOngkir Service using Faked HTTP endpoints.
     */
    public function test_payment_get_ongkir_via_rajaongkir(): void
    {
        Http::fake([
            '*/cost' => Http::response([
                'rajaongkir' => [
                    'results' => [
                        [
                            'code' => 'jne',
                            'name' => 'Jalur Nugraha Ekakurir (JNE)',
                            'costs' => [
                                [
                                    'service' => 'REG',
                                    'description' => 'Layanan Reguler',
                                    'cost' => [
                                        ['value' => 15000, 'etd' => '2-3', 'note' => '']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson(route('manage.transaksi.get-ongkir', [
            'destination' => 444,
            'weight' => 1000,
            'courier' => 'jne'
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'code' => 'jne',
            'service' => 'REG',
            'value' => 15000
        ]);
    }

    /**
     * Test tracking shipping waybill via RajaOngkir Service using Faked HTTP endpoints.
     */
    public function test_payment_check_resi_via_rajaongkir(): void
    {
        Http::fake([
            '*/waybill' => Http::response([
                'rajaongkir' => [
                    'result' => [
                        'delivered' => true,
                        'summary' => [
                            'waybill_number' => 'RESI123456',
                            'courier_name' => 'JNE',
                            'status' => 'DELIVERED'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson(route('manage.transaksi.check-resi', [
            'resi' => 'RESI123456',
            'kurir' => 'jne'
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'waybill_number' => 'RESI123456',
            'status' => 'DELIVERED'
        ]);
    }
}
