<?php

namespace Tests\Feature;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageTransaksiPegawaiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $pegawai = User::factory()->create([
            'role' => 'Pegawai',
        ]);

        $this->actingAs($pegawai);
    }

    /** * TC-WBT-PGW004-01
     */
    public function test_pegawai_rejects_invalid_status_transition_skipping_steps()
    {
        $transaksi = Transaksi::factory()->create([
            'status' => 'Created',
            'no_resi_customer' => null
        ]);

        $response = $this->put(route('manage.transaksi.update', $transaksi->transaksi_id), [
            'status' => 'Done',
            'no_resi_customer' => 'RESI-PEGAWAI-123'
        ]);

        $response->assertSessionHasErrors('status');

        $this->assertEquals('Created', $transaksi->fresh()->status);
    }

    /** * TC-WBT-PGW004-02
     */
    public function test_pegawai_resi_is_required_when_status_changed_to_delivered()
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
}
