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

class KatalogProdukController extends Controller
{
    public function index()
    {
        $query = ProdukKatalog::with('produk', 'fotos');

        $katalog = $query->paginate(20);

        return view('pages.user.katalog.index', compact('katalog'));
    }

    public function create()
    {
        return view('pages.user.katalog.create');
    }

    public function store(Request $request)
    {
        // 1. Basic Validation
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
                'jenis_produk' => 'katalog', // Enum value
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
                            'nama_detail' => ucfirst($type), // "Ukuran" or "Warna"
                            'deskripsi_detail' => 'Variasi ' . ucfirst($type),
                        ]);

                        foreach ($items as $item) {
                            $data = json_decode($item['data'], true);


                            $opsiValue = json_encode($data);

                            PilihanDetailProduk::create([
                                'detail_produk_id' => $detail->detail_produk_id,
                                'opsi' => $opsiValue, // Storing as JSON string
                                'pengaruh_harga' => 0, // Default 0 for now
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
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function arsip()
    {

    }
}
