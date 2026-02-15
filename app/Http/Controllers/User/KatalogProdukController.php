<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\FotoProdukKatalog;
use App\Models\PilihanDetailProduk;
use App\Models\Produk;
use App\Models\ProdukKatalog;
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

            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($subQ) use ($search) {
                    $subQ->where('nama_produk', 'like', '%' . $search . '%');
                })
                    ->orWhere('kategori', 'like', '%' . $search . '%');
            });
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
                    $query->where('stok', '<', 0);
                    break;
                default: 
                    $query->where('stok', '>=', 0);
                    break;
            }
        } else {
            $query->where('stok', '>=', 0);
        }

        $categories = ProdukKatalog::select('kategori')->distinct()->pluck('kategori');
        $katalog = $query->paginate(18)->appends(request()->except('page'));;

        return view('pages.user.katalog.index', compact('katalog', 'categories'));
    }

    public function create()
    {
        return view('pages.user.katalog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'variations' => 'nullable|array'
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
                $variations = collect($request->variations);

                $types = ['ukuran', 'warna'];

                foreach ($types as $type) {
                    $items = $variations->where('type', $type);

                    if ($items->isNotEmpty()) {
                        $detail = DetailProduk::create([
                            'produk_id' => $produk->produk_id,
                            'nama_detail' => ucfirst($type),
                            'deskripsi_detail' => 'Variasi ' . ucfirst($type),
                        ]);

                        foreach ($items as $item) {
                            $data = json_decode($item['data'], true);


                            $opsiValue = json_encode($data);

                            PilihanDetailProduk::create([
                                'detail_produk_id' => $detail->detail_produk_id,
                                'opsi' => $opsiValue,
                                'pengaruh_harga' => 0,
                            ]);
                        }
                    }
                }
            }

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $photo) {
                    $path = $photo->store('uploads/produk', 'public');

                    FotoProdukKatalog::create([
                        'produk_id' => $produk->produk_id,
                        'path' => $path,
                    ]);
                }
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


                $data['type'] = $type;
                $existingVariations[] = $data;
            }
        }

        return view('pages.user.katalog.edit', compact('katalog', 'existingVariations'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'deleted_photos' => 'nullable|array',
            'variations' => 'nullable|array'
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

            DetailProduk::where('produk_id', $produk->produk_id)->delete();

            if ($request->has('variations')) {
                $variations = collect($request->variations);
                $types = ['ukuran', 'warna'];

                foreach ($types as $type) {
                    $items = $variations->where('type', $type);
                    if ($items->isNotEmpty()) {
                        $detail = DetailProduk::create([
                            'produk_id' => $produk->produk_id,
                            'nama_detail' => ucfirst($type),
                            'deskripsi_detail' => 'Variasi ' . ucfirst($type),
                        ]);

                        foreach ($items as $item) {
                            $data = json_decode($item['data'], true);
                            unset($data['type']);

                            PilihanDetailProduk::create([
                                'detail_produk_id' => $detail->detail_produk_id,
                                'opsi' => $data,
                                'pengaruh_harga' => 0,
                            ]);
                        }
                    }
                }
            }

            if ($request->has('deleted_photos')) {
                $photosToDelete = FotoProdukKatalog::whereIn('id', $request->deleted_photos)->get();
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $photo) {
                    $path = $photo->store('uploads/produk', 'public');
                    FotoProdukKatalog::create([
                        'produk_id' => $produk->produk_id,
                        'path' => $path,
                    ]);
                }
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
            $katalog->update(['stok' => -1]);

            return redirect()->route('manage.katalog')->with('success', 'Produk berhasil diarsipkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengarsipkan produk.');
        }
    }
    public function restore($id)
    {
        try {

            $katalog = ProdukKatalog::where('produk_id', $id)->firstOrFail();
            $katalog->update(['stok' => 0]);
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
