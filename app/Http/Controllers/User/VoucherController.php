<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Date;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
// use Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with('katalog.produk');

        // filter search voucher by kode and nama produk
        if ($request->filled('search')) {
            $query->whereLike(['kode_voucher', 'katalog.produk.nama_produk'], $request->search);
        }

        if ($request->filled('jenis_voucher')) {
            switch ($request->jenis_voucher) {
                case 'persentase':
                    $query->where('status', 'persentase');
                    break;

                case 'nomimal':
                    $query->where('status', 'nominal');
                    break;

                default:
                    break;
            }
        }
        $vouchers = $query->orderBy('created_at', 'desc')->paginate(10)->appends(request()->except('page'));

        return view('pages.user.admin.manage-voucher.index', compact('vouchers'));


    }

    public function create()
    {
        return view('pages.user.admin.manage-voucher.create');
    }

    public function store(Request $request)
    {
        if (is_string($request->kode_voucher)) {
            $request->merge([
                'kode_voucher' => trim($request->kode_voucher),
            ]);
        }

        $validated = $request->validate([
            'nama_voucher' => 'required|string|max:255',
            'kode_voucher' => 'nullable|string|unique:vouchers,kode_voucher',
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric|gt:0',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat ',
            'nilai_diskon.gt' => 'Nilai diskon tidak bisa negatif atau nol.',
        ]);

        $kodeVoucher = $request->filled('kode_voucher')
            ? $this->normalizeVoucherCode($validated['kode_voucher'])
            : $this->randomizeVoucherCodeName($validated['nama_voucher']);

        $voucher = Voucher::create([
            'nama_voucher' => $validated['nama_voucher'],
            'kode_voucher' => $kodeVoucher,
            'deskripsi' => $validated['deskripsi'],
            'nilai_diskon' => $validated['nilai_diskon'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
            'jenis_voucher' => $validated['jenis_voucher'],
        ]);

        return redirect()->route('manage.voucher.store')->with('success', 'Voucher Berhasil Ditambahkan');

    }

    public function edit($id)
    {
        $voucher = Voucher::with('katalog.produk')->findOrFail($id);
        return view('pages.user.admin.manage-voucher.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        if (is_string($request->kode_voucher)) {
            $request->merge([
                'kode_voucher' => trim($request->kode_voucher),
            ]);
        }

        $validated = $request->validate([
            'nama_voucher' => 'required|string|max:255',
            'kode_voucher' => 'nullable|string|unique:vouchers,kode_voucher,' . $id,
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric|gt:0',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat ',
            'nilai_diskon.gt' => 'Nilai diskon tidak bisa negatif atau nol.',
        ]);

        $kodeVoucher = $request->filled('kode_voucher')
            ? $this->normalizeVoucherCode($validated['kode_voucher'])
            : $this->randomizeVoucherCodeName($validated['nama_voucher']);

        $voucher = Voucher::findOrFail($id);

        $voucher->update([
            'nama_voucher' => $validated['nama_voucher'],
            'kode_voucher' => $kodeVoucher,
            'deskripsi' => $validated['deskripsi'],
            'nilai_diskon' => $validated['nilai_diskon'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
            'jenis_voucher' => $validated['jenis_voucher'],
        ]);

        return redirect()->route('manage.voucher')->with('success', 'Voucher Berhasil Diperbarui');
    }

    // API: Validasi voucher
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
        ]);

        $kodeVoucher = $this->normalizeVoucherCode($request->kode);

        $voucher = Voucher::whereRaw('UPPER(TRIM(kode_voucher)) = ?', [$kodeVoucher])
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak ditemukan.'
            ], 404);
        }

        if ($voucher->status !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah tidak aktif.'
            ], 422);
        }

        $today = now()->toDateString();

        if ($voucher->tanggal_mulai > $today) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher belum aktif.'
            ], 422);
        }

        if ($voucher->tanggal_berakhir < $today) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kedaluwarsa.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'voucher' => [
                'kode_voucher' => $voucher->kode_voucher,
                'jenis_voucher' => $voucher->jenis_voucher,
                'nilai_diskon' => $voucher->nilai_diskon,
                'nama_voucher' => $voucher->nama_voucher,
                'deskripsi' => $voucher->deskripsi,
            ]
        ]);
    }

    public function deactiveVoucher($id)
    {

        try {
            $voucher = Voucher::with('katalog.produk')->findOrFail($id);

            $voucher->update([
                'status' => 'Habis'
            ]);

            return back()->with('sucess', 'Voucher Berhasil Dinonaktifkan');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus menonaktifkan voucher.');
        }
    }

    public function destroy($id)
    {
        $voucher = Voucher::with('katalog.produk')->findOrFail($id);
        $voucher->delete();

        return back()->with('success', 'Voucher Berhasil Dihapus');

    }


    // helper
    private function randomizeVoucherCodeName(string $userInput, int $randomLength = 5): string
    {
        $cleanPrefix = Str::upper(Str::slug($userInput));

        $randomStringVoucher = Str::upper(Str::random($randomLength));

        return $cleanPrefix . '-' . $randomStringVoucher;
    }

    private function normalizeVoucherCode(string $code): string
    {
        return Str::upper(trim($code));
    }

}
