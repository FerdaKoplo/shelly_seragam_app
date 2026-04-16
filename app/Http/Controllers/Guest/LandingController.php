<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;

class LandingController extends Controller
{
    public function index()
    {
        $katalogTerbaru = ProdukKatalog::query()
            ->with(['produk', 'fotos'])
            ->orderByDesc('katalog_id')
            ->take(9)
            ->get();

        return view('pages.guest.landing.landing', compact('katalogTerbaru'));
    }
}