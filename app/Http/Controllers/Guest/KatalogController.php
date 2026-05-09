<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukKatalog::query()->with(['produk', 'fotos']);

        // default: jangan tampilkan yang diarsipkan (stok < 0)
        $query->where('stok', '>=', 0);

        // search
        if ($request->filled('search')) {
            $search = $request->search;

            // search (nama produk saja)
            if ($request->filled('search')) {
                $search = $request->search;

                $query->whereHas('produk', function ($subQ) use ($search) {
                    $subQ->where('nama_produk', 'like', '%' . $search . '%');
                });
            }
        }

        // harga
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }


        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // kategori (exact match dari DB)
        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }

        // status stok
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'ready') {
                $query->where('status', 'Tersedia');
            } elseif ($request->filter_status === 'pre-order') {
                $query->where('status', 'Pre-Order');
            }
        }

        // sort
        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_high') {
            $query->orderByDesc('harga');
        } elseif ($sort === 'price_low') {
            $query->orderBy('harga');
        } else {
            $query->orderByDesc('katalog_id'); // newest
        }

        $katalog = $query->paginate(35)->appends($request->except('page'));

        // categories untuk dropdown modal (sesuai DB)
        $categories = ProdukKatalog::query()
            ->where('stok', '>=', 0)
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('pages.guest.katalog.index', compact('katalog', 'categories'));
    }

    public function show($id)
    {
        $item = ProdukKatalog::query()
            ->with([
                'produk.detailProduks.pilihanDetails',
                'produk.produkTransaksis',
                'fotos',
            ])
            ->where('katalog_id', $id)
            ->firstOrFail();

        $sold = (int) $item->produk?->produkTransaksis?->sum('quantity');

        return view('pages.guest.katalog.detail', compact('item', 'sold'));
    }
}
