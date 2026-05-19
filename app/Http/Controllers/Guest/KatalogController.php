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

        // 2. Harga
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // 3. Kategori & Status
        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'ready') {
                $query->where('status', 'Tersedia');
            } elseif ($request->filter_status === 'pre-order') {
                $query->where('status', 'Pre-Order');
            }
        }

        if ($request->filled('filter_gender')) {
            $query->whereHas('produk.detailProduks', function ($q) use ($request) {
                // Asumsi nama atribut di DB adalah 'Pakaian Untuk' atau 'Gender'
                $q->whereIn('nama_detail', ['Pakaian Untuk', 'Gender'])
                    ->whereHas('pilihanDetails', function ($q2) use ($request) {
                        $q2->where('opsi', $request->filter_gender);
                    });
            });
        }

        if ($request->filled('filter_ukuran')) {
            $query->whereHas('produk.detailProduks', function ($q) use ($request) {
                $q->where('nama_detail', 'Ukuran')
                    ->whereHas('pilihanDetails', function ($q2) use ($request) {
                        $q2->where('opsi', $request->filter_ukuran);
                    });
            });
        }

        if ($request->filled('filter_material')) {
            $query->whereHas('produk.detailProduks', function ($q) use ($request) {
                $q->whereIn('nama_detail', ['Material Kain', 'Bahan', 'Jenis Kain'])
                    ->whereHas('pilihanDetails', function ($q2) use ($request) {
                        $q2->where('opsi', 'like', '%' . $request->filter_material . '%');
                    });
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_high') {
            $query->orderByDesc('harga');
        } elseif ($sort === 'price_low') {
            $query->orderBy('harga');
        } elseif ($sort === 'best_selling') {
            $query->addSelect([
                'terjual' => \App\Models\ProdukTransaksi::selectRaw('SUM(quantity)')
                    ->whereColumn('produk_id', 'produk_katalog.produk_id')
            ])->orderByDesc('terjual');
        } else {
            $query->orderByDesc('katalog_id');
        }

        $katalog = $query->paginate(35)->appends($request->except('page'));

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
