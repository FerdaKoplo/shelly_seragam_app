<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\PilihanDetailProduk;
use App\Models\Produk;
use App\Models\ProdukKustom;
use Illuminate\Support\Facades\DB;
// use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageKustomisasiController extends Controller
{
    const DETAIL_KOMBINASI = 'Jumlah Kombinasi Kain';
    const DETAIL_BORDIR = 'Jumlah Titik Bordir';
    const DETAIL_CATATAN = 'Catatan';
    const DETAIL_UPLOAD = 'Upload Desain';
    const DETAIL_UKURAN = 'Ukuran';


    public function index()
    {
        try {
            $kustoms = ProdukKustom::with('produk.detailProduks.pilihanDetails')
                ->latest()
                ->get();
        } catch (\Exception $e) {
            Log::error('index kustom gagal', ['error' => $e->getMessage()]);
            $kustoms = collect();
            session()->flash('error', 'Gagal memuat data: ' . $e->getMessage());
        }

        return view('pages.user.kustom.index', compact('kustoms'));
    }

    public function create()
    {
        return view('pages.user.kustom.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:100',
            'sections.*.show_kombinasi' => 'nullable|in:0,1',
            'sections.*.kombinasi_counts' => 'nullable|array',
            'sections.*.kombinasi_counts.*' => 'integer|in:1,2,3',
            'sections.*.show_bordir' => 'nullable|in:0,1',
            'sections.*.bordir_options' => 'nullable|array',
            'show_catatan' => 'nullable|in:0,1',
            'show_upload' => 'nullable|in:0,1',
            'show_ukuran' => 'nullable|in:0,1',
            'sections.*.bordir_options.*' => 'integer|between:0,5',
        ]);

        $submittedNames = collect($request->input('sections'))->pluck('name')->toArray();
        $isDuplicate = ProdukKustom::whereIn('spesifikasi_khusus', $submittedNames)->exists();

        if ($isDuplicate) {
            return back()->withInput()->with('error', 'Aspek Sudah Pernah Ditambahkan');
        }

        DB::beginTransaction();
        try {
            foreach ($request->input('sections') as $sec) {
                // Section sekarang BEBAS ditambahkan berkali-kali dan bebas duplikat!
                $produk = Produk::create([
                    'nama_produk' => 'Kustom ' . $sec['name'],
                    'deskripsi' => '',
                    'jenis_produk' => 'kustom',
                ]);

                ProdukKustom::create([
                    'produk_id' => $produk->produk_id,
                    'spesifikasi_khusus' => $sec['name'],
                ]);

                $sec['show_catatan'] = $request->input('show_catatan', '0');
                $sec['show_upload'] = $request->input('show_upload', '0');
                $sec['show_ukuran'] = $request->input('show_ukuran', '0');

                $this->saveDetails($produk->produk_id, collect(), $sec);
            }

            DB::commit();
            return redirect()->route('manage.kustom')->with('success', 'Produk kustom berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store kustom gagal', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kustom = ProdukKustom::with('produk')->findOrFail($id);

        $kustoms = ProdukKustom::with('produk.detailProduks.pilihanDetails')
            ->where('produk_id', $kustom->produk_id)
            ->get();

        return view('pages.user.kustom.edit', compact('kustoms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:100',
            'sections.*.kustom_id' => 'nullable|exists:produk_kustom,kustom_id',
            'sections.*.show_kombinasi' => 'nullable|in:0,1',
            'sections.*.kombinasi_counts' => 'nullable|array',
            'sections.*.kombinasi_counts.*' => 'integer|in:1,2,3',
            'sections.*.show_bordir' => 'nullable|in:0,1',
            'sections.*.bordir_options' => 'nullable|array',
            'show_catatan' => 'nullable|in:0,1',
            'show_upload' => 'nullable|in:0,1',
            'show_ukuran' => 'nullable|in:0,1',
            'sections.*.bordir_options.*' => 'integer|between:0,5',
        ]);



        DB::beginTransaction();
        try {
            foreach ($request->input('sections') as $sec) {
                $kustomId = $sec['kustom_id'] ?? null;

                if ($kustomId) {
                    $kustom = ProdukKustom::with('produk.detailProduks.pilihanDetails')->findOrFail($kustomId);
                    $details = $kustom->produk->detailProduks->keyBy('nama_detail');

                    $sec['show_catatan'] = $request->input('show_catatan', '0');
                    $sec['show_upload'] = $request->input('show_upload', '0');
                    $sec['show_ukuran'] = $request->input('show_ukuran', '0');
                    $this->saveDetails($kustom->produk->produk_id, $details, $sec);
                } else {
                    $produk = Produk::create([
                        'nama_produk' => 'Kustom ' . $sec['name'],
                        'deskripsi' => '',
                        'jenis_produk' => 'kustom',
                    ]);
                    ProdukKustom::create([
                        'produk_id' => $produk->produk_id,
                        'spesifikasi_khusus' => $sec['name'],
                    ]);
                    $sec['show_catatan'] = $request->input('show_catatan', '0');
                    $sec['show_upload'] = $request->input('show_upload', '0');
                    $sec['show_ukuran'] = $request->input('show_ukuran', '0');
                    $this->saveDetails($produk->produk_id, collect(), $sec);
                }
            }

            DB::commit();
            return redirect()->route('manage.kustom')->with('success', 'Produk kustom berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update kustom gagal', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $kustom = ProdukKustom::findOrFail($id);
            $isInUse = DB::table('order_transaksi_kustom')
                ->where('tipe_kustom', $kustom->spesifikasi_khusus)
                ->exists();

            if ($isInUse) {
                return back()->with('error', 'Aspek tidak dapat dihapus karena masih digunakan oleh transaksi aktif');
            }

            if ($kustom->produk) {
                $kustom->produk->delete();
            }

            $kustom->delete();
            return redirect()->route('manage.kustom')->with('success', 'Produk kustom berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Destroy kustom gagal', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // helpers
    private function saveDetails(int $produkId, $existing, array $sec): void
    {
        $showKombinasi = ($sec['show_kombinasi'] ?? '0') === '1';
        $showBordir = ($sec['show_bordir'] ?? '0') === '1';

        $counts = $showKombinasi ? array_values(array_unique($sec['kombinasi_counts'] ?? [])) : [];
        $bordirs = $showBordir ? array_values(array_unique($sec['bordir_options'] ?? [])) : [];

        $this->upsertMulti($produkId, $existing, self::DETAIL_KOMBINASI, array_map('strval', $counts));
        $this->upsertMulti($produkId, $existing, self::DETAIL_BORDIR, array_map('strval', $bordirs));

        $showCatatan = ($sec['show_catatan'] ?? '0') === '1';
        $showUpload = ($sec['show_upload'] ?? '0') === '1';
        $showUkuran = ($sec['show_ukuran'] ?? '0') === '1';

        $this->toggleDetail($produkId, $existing, self::DETAIL_CATATAN, $showCatatan);
        $this->toggleDetail($produkId, $existing, self::DETAIL_UPLOAD, $showUpload);
        $this->toggleDetail($produkId, $existing, self::DETAIL_UKURAN, $showUkuran);
    }

    private function upsertMulti(int $produkId, $existing, string $namaDetail, array $values): void
    {
        if ($existing->has($namaDetail)) {
            $detail = $existing[$namaDetail];
            $detail->pilihanDetails()->delete();
        } else {
            $detail = DetailProduk::create([
                'produk_id' => $produkId,
                'nama_detail' => $namaDetail,
                'deskripsi_detail' => '-',
            ]);
        }

        foreach ($values as $val) {
            PilihanDetailProduk::create([
                'detail_produk_id' => $detail->detail_produk_id,
                'opsi' => $val,
                'pengaruh_harga' => 0,
            ]);
        }
    }

    private function toggleDetail(int $produkId, $existing, string $namaDetail, bool $enabled): void
    {
        if ($enabled) {
            $this->upsertSingle($produkId, $existing, $namaDetail, '1');
        } elseif ($existing->has($namaDetail)) {
            $existing[$namaDetail]->delete();
        }
    }

    private function upsertSingle(int $produkId, $existing, string $namaDetail, ?string $value): void
    {
        if ($existing->has($namaDetail)) {
            $detail = $existing[$namaDetail];
            $detail->pilihanDetails()->delete();
        } else {
            $detail = DetailProduk::create([
                'produk_id' => $produkId,
                'nama_detail' => $namaDetail,
                'deskripsi_detail' => '-',
            ]);
        }

        if ($value !== null && $value !== '') {
            PilihanDetailProduk::create([
                'detail_produk_id' => $detail->detail_produk_id,
                'opsi' => $value,
                'pengaruh_harga' => 0,
            ]);
        }
    }
}
