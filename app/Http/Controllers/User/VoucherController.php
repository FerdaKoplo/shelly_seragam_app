<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Date;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'nama_voucher' => 'required|string|max:255',
            'kode_voucher' => 'nullable|string|unique:vouchers,kode_voucher',
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat '
        ]);

        $kodeVoucher = $request->filled('kode_voucher')
            ? $validated['kode_voucher']
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
        $validated = $request->validate([
            'nama_voucher' => 'required|string|max:255',
            'kode_voucher' => 'nullable|string|unique:vouchers,kode_voucher,' . $id,
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric',
            'tanggal_mulai' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|date_format:Y-m-d|after:tanggal_mulai',
            'jenis_voucher' => ['required', Rule::in(Voucher::JENIS_VOUCHER)],
        ], [
            'kode_voucher.unique' => 'Voucher Dengan Kode Yang Sama Sudah Dibuat '
        ]);


        $kodeVoucher = $request->filled('kode_voucher')
            ? $validated['kode_voucher']
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

    public function deactiveVoucher($id)
    {
        $voucher = Voucher::with('katalog.produk')->findOrFail($id);

        $voucher->update([
            'status' => 'Habis'
        ]);

        return back()->with('sucess', 'Voucher Berhasil Dinonaktifkan');
    }

    public function destroy($id)
    {
        $voucher = Voucher::with('katalog.produk')->findOrFail($id);
        $voucher->delete();

        return redirect()->route('manage.voucher.destroy')->with('success', 'Voucher Berhasil Dihapus');

    }


    // helper
    private function randomizeVoucherCodeName(string $userInput, int $randomLength = 5): string
    {
        $cleanPrefix = Str::upper(Str::slug($userInput));

        $randomStringVoucher = Str::upper(Str::random($randomLength));

        return $cleanPrefix . '-' . $randomStringVoucher;
    }

}
