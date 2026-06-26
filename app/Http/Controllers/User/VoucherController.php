<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Date;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Str;
class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with('katalog.produk');

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
            'kode_voucher' => 'required|string|unique:vouchers,kode_voucher',
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric|gte:0|min:1',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after_or_equal:today|after_or_equal:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.required' => 'Kode voucher wajib diisi.',
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat.',
            'nilai_diskon.gte' => 'Nilai diskon tidak boleh negatif.',
            'nilai_diskon.min' => 'Nilai diskon minimal adalah 1.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari hari ini.',

        ]);

        $kodeVoucher = $this->normalizeVoucherCode($validated['kode_voucher']);

        Voucher::create([
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
            'kode_voucher' => 'required|string|unique:vouchers,kode_voucher,' . $id,
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric|gte:0|min:1',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after_or_equal:today|after_or_equal:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.required' => 'Kode voucher wajib diisi.', 
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat.',
            'nilai_diskon.gte' => 'Nilai diskon tidak boleh negatif.',
            'nilai_diskon.min' => 'Nilai diskon minimal adalah 1.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari hari ini.',
        ]);

        $kodeVoucher = $this->normalizeVoucherCode($validated['kode_voucher']);

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

    public function deactiveVoucher($id)
    {

        try {
            $voucher = Voucher::with('katalog.produk')->findOrFail($id);

            $voucher->update([
                'status' => 'Habis'
            ]);

            return back()->with('success', 'Voucher Berhasil Dinonaktifkan');

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
    private function normalizeVoucherCode(string $code): string
    {
        return Str::upper(trim($code));
    }

}
