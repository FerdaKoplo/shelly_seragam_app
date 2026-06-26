<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class RedeemVoucherController extends Controller
{
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
        ]);

        $kodeVoucher = $this->normalizeVoucherCode($request->kode);
        $voucher = Voucher::whereRaw('UPPER(TRIM(kode_voucher)) = ?', [$kodeVoucher])->first();

        $error = $this->checkVoucherValidity($voucher);

        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error['message'],
            ], $error['status']);
        }

        return response()->json([
            'success' => true,
            'voucher' => [
                'kode_voucher' => $voucher->kode_voucher,
                'jenis_voucher' => $voucher->jenis_voucher,
                'nilai_diskon' => $voucher->nilai_diskon,
                'nama_voucher' => $voucher->nama_voucher,
                'deskripsi' => $voucher->deskripsi,
            ],
        ]);
    }

    private function checkVoucherValidity(?Voucher $voucher): ?array
    {
        $today = now()->toDateString();

        return match (true) {
            !$voucher => ['message' => 'Voucher tidak ditemukan.', 'status' => 404],
            $voucher->status !== 'Aktif' => ['message' => 'Voucher sudah tidak aktif.', 'status' => 422],
            $voucher->tanggal_mulai > $today => ['message' => 'Voucher belum aktif.', 'status' => 422],
            $voucher->tanggal_berakhir < $today => ['message' => 'Voucher sudah kedaluwarsa.', 'status' => 422],
            default => null,
        };

    }

    private function normalizeVoucherCode(string $kode): string
    {
        return strtoupper(trim($kode));
    }
}
