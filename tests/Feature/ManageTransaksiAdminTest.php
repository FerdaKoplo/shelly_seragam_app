<?php

namespace Tests\Feature;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageTransaksiAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create([
            'role' => 'Admin',
        ]);

        $this->actingAs($admin);
    }

    /* TC-WBT-ADM005-01*/
    public function test_rejects_invalid_status_transition_skipping_steps()
    {
        $transaksi = Transaksi::factory()->create([
            'status' => 'Created',
            'no_resi_customer' => null
        ]);

        $response = $this->put(route('manage.transaksi.update', $transaksi->transaksi_id), [
            'status' => 'Done',
            'no_resi_customer' => 'RESI123'
        ]);

        $response->assertSessionHasErrors('status');

        $this->assertEquals('Created', $transaksi->fresh()->status);
    }

    /** * TC-WBT-ADM005-02
     */
    public function test_resi_is_required_when_status_changed_to_delivered()
    {
        $transaksi = Transaksi::factory()->create([
            'status' => 'Paid',
            'no_resi_customer' => null
        ]);

        $response = $this->put(route('manage.transaksi.update', $transaksi->transaksi_id), [
            'status' => 'Delivered',
            'no_resi_customer' => ''
        ]);

        $response->assertSessionHasErrors('no_resi_customer');

        $this->assertEquals('Paid', $transaksi->fresh()->status);
    }

    /** * TC-WBT-ADM005-03
     */

    public function test_export_returns_error_when_no_data_found()
    {
        Transaksi::query()->delete();

        $this->assertDatabaseCount('transaksi', 0);

        $response = $this->get(route('statistik.transaksi.export', [
            'bulan' => '10'
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak Ada Data Untuk Diexport');
    }

    public function test_export_success_when_data_exists()
    {
        Transaksi::factory()->create([
            'tanggal_transaksi' => now()->format('Y-m-d')
        ]);

        $response = $this->get(route('statistik.transaksi.export', [
            'bulan' => now()->format('m')
        ]));

        $response->assertStatus(200);

        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

}
