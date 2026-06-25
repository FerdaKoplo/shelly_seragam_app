<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\ProdukKatalog;
use App\Services\KatalogService;
use DB;
use Illuminate\Http\Request;
use Log;
use Storage;

class KatalogProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukKatalog::with('produk', 'fotos');

        if ($request->filled('search')) {
            $search = $request->search;

            if ($request->filled('search')) {
                $search = $request->search;

                $query->whereHas('produk', function ($subQ) use ($search) {
                    $subQ->where('nama_produk', 'like', '%' . $search . '%');
                });
            }
        }
        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }

        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        if ($request->filled('filter_status')) {
            switch ($request->filter_status) {
                case 'ready':
                    $query->where('stok', '>', 0);
                    break;
                case 'empty':
                    $query->where('stok', '=', 0);
                    break;
                case 'archived':
                    $query->where('status', 'Arsip');
                    break;
                default:
                    $query->where('stok', '>=', 0)->where('status', 'Tersedia');
                    break;
            }
        } else {
            $query->where('stok', '>=', 0);
        }

        $categories = ProdukKatalog::select('kategori')->distinct()->pluck('kategori');
        $katalog = $query->paginate(18)->appends(request()->except('page'));

        return view('pages.user.katalog.index', compact('katalog', 'categories'));
    }

    public function create()
    {
        return view('pages.user.katalog.create');
    }

    public function store(Request $request, KatalogService $katalogService)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:10000|max:5000000',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'variations' => 'nullable|array'
        ], [
            'harga.min' => 'Harga minimal adalah Rp 10.000',
            'harga.max' => 'Harga maksimal adalah Rp 5.000.000',
            'harga.numeric' => 'Format angka tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $produk = Produk::create([
                'nama_produk' => $validated['nama_produk'],
                'deskripsi' => $validated['deskripsi'],
                'jenis_produk' => 'katalog',
            ]);

            ProdukKatalog::create([
                'produk_id' => $produk->produk_id,
                'kategori' => $validated['kategori'],
                'harga' => $validated['harga'],
                'stok' => $validated['stok'],
            ]);

            if ($request->has('variations')) {
                $katalogService->syncVariations($produk->produk_id, $request->variations);
            }

            if ($request->hasFile('fotos')) {
                $katalogService->uploadPhotos($produk->produk_id, $request->file('fotos'));
            }

            DB::commit();

            return redirect()->route('manage.katalog')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->withErrors(['msg' => 'Gagal menambahkan produk: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $katalog = ProdukKatalog::where('produk_id', $id)
            ->with(['produk.detailProduks.pilihanDetails', 'fotos'])
            ->firstOrFail();

        $existingVariations = [];

        foreach ($katalog->produk->detailProduks as $detail) {
            $type = strtolower($detail->nama_detail);

            foreach ($detail->pilihanDetails as $pilihan) {

                $data = $pilihan->opsi;

                if (!is_array($data)) {
                    $data = json_decode($data, true) ?? [];
                }

                $data['type'] = $type;
                $existingVariations[] = $data;
            }
        }

        return view('pages.user.katalog.edit', compact('katalog', 'existingVariations'));
    }

    public function update(Request $request, $id, KatalogService $katalogService)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:10000|max:5000000',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'deleted_photos' => 'nullable|array',
            'variations' => 'nullable|array'
        ], [
            'harga.min'     => 'Harga minimal adalah Rp 10.000',
            'harga.max'     => 'Harga maksimal adalah Rp 5.000.000',
            'harga.numeric' => 'Format angka tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $katalog = ProdukKatalog::where('produk_id', $id)->firstOrFail();
            $produk = $katalog->produk;

            $produk->update([
                'nama_produk' => $validated['nama_produk'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            $katalog->update([
                'kategori' => $validated['kategori'],
                'harga' => $validated['harga'],
                'stok' => $validated['stok'],
            ]);

            if ($request->has('variations')) {
                $katalogService->syncVariations($produk->produk_id, $request->variations);
            }

            if ($request->has('deleted_photos')) {
                $katalogService->removePhotos($request->deleted_photos);
            }

            if ($request->hasFile('fotos')) {
                $katalogService->uploadPhotos($produk->produk_id, $request->file('fotos'));
            }

            DB::commit();
            return redirect()->route('manage.katalog')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->withErrors(['msg' => 'Gagal update produk: ' . $e->getMessage()]);
        }
    }


    public function archive($id)
    {
        try {
            $katalog = ProdukKatalog::where('produk_id', $id)->firstOrFail();
            if ($katalog->status === 'Arsip') {
                return back()->with('error', 'Item sudah dalam status arsip.');
            }
            $katalog->update(['status' => 'Arsip']);

            return redirect()->route('manage.katalog')->with('success', 'Produk berhasil diarsipkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengarsipkan produk.');
        }
    }
    public function restore($id)
    {
        try {

            $katalog = ProdukKatalog::where('produk_id', $id)->firstOrFail();
            $katalog->update(['status' => 'Tersedia']);
            return back()->with('success', 'Produk dipulihkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulihkan produk.');
        }
    }


    public function destroy($id)
    {
        try {
            $katalog = ProdukKatalog::where('produk_id', $id)->firstOrFail();
            $produk = $katalog->produk;

            if ($katalog->status !== 'Arsip') {
                return back()->with('error', 'Hanya item terarsip yang boleh dihapus.');
            }
            foreach ($produk->fotos as $foto) {
                Storage::disk('public')->delete($foto->path);
            }

            $produk->delete();

            return redirect()->route('manage.katalog')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk.');
        }
    }

}
