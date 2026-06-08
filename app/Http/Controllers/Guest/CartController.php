<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $rawItems = array_values($request->session()->get('cart', []));
        $items = array_map(function ($item) {
            $rawImage = $item['image'] ?? null;
            $isAbsolute = is_string($rawImage)
                && (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://'));

            $item['image_url'] = $isAbsolute
                ? $rawImage
                : ($rawImage ? asset('storage/' . ltrim($rawImage, '/')) : 'https://picsum.photos/id/1/600/800');

            return $item;
        }, $rawItems);

        $recommendations = ProdukKatalog::with(['produk', 'fotos'])
            ->where('stok', '>', 0)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $subtotal = collect($items)->sum(fn($i) => ((int) $i['price']) * ((int) $i['quantity']));

        return view('pages.guest.keranjang.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'notes' => (string) $request->session()->get('cart_notes', ''),
            'recommendations' => $recommendations,
        ]);
    }

    public function add(Request $request, int $katalog_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.min' => 'Kuantitas harus lebih dari 0',
        ]);

        $qty = (int) $request->input('quantity');
        
        $size = $request->input('size');
        $color = $request->input('color');
        $mode = $request->input('mode');
        $stok = $request->input('stok');

        $katalog = ProdukKatalog::with(['produk', 'fotos'])->findOrFail($katalog_id);

        // $katalog = ProdukKatalog::with(['produk', 'fotos'])->findOrFail($katalog_id);

        $name = $katalog->produk->nama_produk
            ?? $katalog->produk->nama
            ?? $katalog->produk->name
            ?? 'Produk';

        $image = optional($katalog->fotos->first())->foto
            ?? optional($katalog->fotos->first())->path
            ?? optional($katalog->fotos->first())->url
            ?? null;

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$katalog_id])) {
            $cart[$katalog_id]['quantity'] += $qty;
        } else {
            $cart[$katalog_id] = [
                'id' => $katalog_id,
                'katalog_id' => $katalog_id,
                'name' => $name,
                'price' => (int) $katalog->harga,
                'stok' => (int) $stok,
                'quantity' => $qty,
                'size' => is_string($size) && trim($size) !== '' ? trim($size) : null,
                'color' => is_string($color) && trim($color) !== '' ? trim($color) : null,
                'mode' => is_string($mode) && trim($mode) !== '' ? trim($mode) : null,
                'image' => $image,
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('keranjang')->with('cart_success', 'berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, int $katalog_id)
    {
        $cart = $request->session()->get('cart', []);
        if (!isset($cart[$katalog_id])) {
            return redirect()->route('keranjang');
        }

        $action = $request->input('action');

        if ($action === 'increment') {
            $qty = ((int) $cart[$katalog_id]['quantity']) + 1;
        } elseif ($action === 'decrement') {
            $qty = ((int) $cart[$katalog_id]['quantity']) - 1;
        } else {
            $qty = (int) $request->input('quantity', 1);
        }

        if ($qty <= 0) {
            unset($cart[$katalog_id]);
        } else {
            $cart[$katalog_id]['quantity'] = $qty;
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('keranjang');
    }

    public function remove(Request $request, int $katalog_id)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$katalog_id]);
        $request->session()->put('cart', $cart);

        return redirect()->route('keranjang');
    }

    public function updateNotes(Request $request)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $notes = trim((string) ($validated['notes'] ?? ''));

        if ($notes === '') {
            $request->session()->forget('cart_notes');
        } else {
            $request->session()->put('cart_notes', $notes);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('keranjang');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');
        $request->session()->forget('cart_notes');
        return redirect()->route('keranjang');
    }
}
