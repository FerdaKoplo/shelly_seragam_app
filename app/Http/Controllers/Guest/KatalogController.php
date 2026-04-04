<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;

class KatalogController extends Controller
{
    public function index()
    {
        $katalog = ProdukKatalog::query()
            ->with(['produk', 'fotos'])
            ->orderByDesc('katalog_id')
            ->paginate(12);

        return view('pages.guest.katalog.index', compact('katalog'));
    }

    public function show($id)
    {
        $item = ProdukKatalog::query()
            ->with(['produk', 'fotos'])
            ->where('katalog_id', $id)
            ->firstOrFail();

        return view('pages.guest.katalog.detail', compact('item'));
    }
}