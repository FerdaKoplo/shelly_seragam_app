<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;
use App\Services\Guest\KatalogFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KatalogController extends Controller
{
    public function index(Request $request, KatalogFilterService $filterService)
    {
        $queryString = json_encode($request->all());
        $cacheKey = 'katalog_index_' . md5($queryString);

        $katalog = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($request, $filterService) {
            return $filterService->getFilteredKatalog($request);
        });

        $categories = Cache::remember('katalog_categories_list', now()->addHours(24), function () {
            return ProdukKatalog::query()
                ->where('stok', '>=', 0)
                ->select('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori');
        });

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
