<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;
use Illuminate\Http\Request;

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
        //
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
