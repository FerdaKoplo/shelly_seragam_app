<?php

namespace App\Services\Guest;

use App\Models\ProdukKatalog;
use App\Models\ProdukTransaksi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
class KatalogFilterService
{
    public function getFilteredKatalog(Request $request): LengthAwarePaginator
    {
        $query = ProdukKatalog::query()->with(['produk', 'fotos']);

        $query->where('stok', '>=', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('produk', function ($subQ) use ($search) {
                $subQ->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

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
                'terjual' => ProdukTransaksi::selectRaw('SUM(quantity)')
                    ->whereColumn('produk_id', 'produk_katalog.produk_id')
            ])->orderByDesc('terjual');
        } else {
            $query->orderByDesc('katalog_id');
        }

        return $query->paginate(35)->appends($request->except('page'));
    }
}