<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Guest\KatalogController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\KelolaTransaksiController;
use App\Http\Controllers\User\ManageKustomisasiController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// auth
Route::get('/login', function () {
    return view('pages.auth.login');
});
Route::post('/login', LoginController::class)->name('login');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// guest routes
Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/kustom', function () {
    return view('pages.guest.kustom.index');
})->name('kustom');

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

Route::get('/katalog/{id}', [KatalogController::class, 'show'])
    ->whereNumber('id')
    ->name('product.show');

Route::get('/keranjang', function () {
    return view('pages.guest.keranjang.index');
})->name('keranjang');

/**
 * Checkout
 * - GET: untuk katalog / akses langsung
 * - POST: untuk kustom (termasuk upload file)
 */
Route::match(['GET', 'POST'], '/checkout', function (Request $request) {
    // kalau POST dari halaman kustom, input('type') ada dan bernilai "kustom"
    // kalau GET dari katalog, biasanya pakai query string ?type=katalog
    $type = $request->input('type', $request->query('type', 'katalog'));

    // mock data katalog (sementara)
    $mockKatalogItems = [
        ['id' => 1, 'name' => 'Kemeja Kotak', 'price' => 114000, 'quantity' => 2, 'size' => 'S', 'image' => 'product-1.png'],
        ['id' => 2, 'name' => 'Kemeja Kotak Blue', 'price' => 114000, 'quantity' => 1, 'size' => 'M', 'image' => 'product-1.png'],
    ];

    // default shipping
    $shippingOptions = [
        ['id' => 'reg', 'label' => 'Regular', 'price' => 15000],
        ['id' => 'exp', 'label' => 'Express', 'price' => 35000],
    ];

    // handle upload hanya untuk POST kustom
    $uploadedUrl = null;
    $uploadedName = null;

    if ($request->isMethod('post') && $request->hasFile('design_file')) {
        // validasi basic (optional tapi recommended)
        $request->validate([
            'design_file' => ['file', 'max:10240', 'mimes:png,jpg,jpeg,svg,pdf'],
        ]);

        $path = $request->file('design_file')->store('uploads/kustom', 'public');
        $uploadedUrl = Storage::disk('public')->url($path);
        $uploadedName = $request->file('design_file')->getClientOriginalName();
    }

    // data custom buat ringkasan checkout
    $mockCustomData = [
        'title' => 'Kustom',
        'qty' => ($request->input('total_quantity', 1)) . ' pcs',
        'type' => $request->input('category', 'bundle'),
        'price' => 1750000,

        // supaya bisa ditampilkan di checkout
        'file_name' => $uploadedName,
        'file_url' => $uploadedUrl,

        // tambahan optional kalau kamu mau tampilkan juga:
        'notes' => $request->input('notes'),
        'size' => $request->input('size'),
    ];

    return view('pages.guest.checkout.checkout', [
        'type' => $type,
        'items' => $mockKatalogItems,
        'customData' => $mockCustomData,
        'shippingOptions' => $shippingOptions,
    ]);
})->name('checkout');

// user routes
Route::prefix('admin')->group(function () {
    Route::get('/manage-transaksi', function () {
        return view('pages.user.transaksi.index');
    })->name('manage.transaksi');

    Route::prefix('manage-katalog')->name('manage.katalog')->group(function () {
        Route::get('/', [KatalogProdukController::class, 'index'])->name('');
        Route::get('/create', [KatalogProdukController::class, 'create'])->name('.create');
        Route::post('/', [KatalogProdukController::class, 'store'])->name('.store');
        Route::get('/{id}/edit', [KatalogProdukController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [KatalogProdukController::class, 'update'])->name('.update');

        Route::put('/{id}/archive', [KatalogProdukController::class, 'archive'])->name('.archive');
        Route::put('/{id}/restore', [KatalogProdukController::class, 'restore'])->name('.restore');

        Route::delete('/{id}', [KatalogProdukController::class, 'destroy'])->name('.destroy');
    });

    Route::prefix('statistik-transaksi')->name('statistik.transaksi')->group(function () {
        Route::get('/', [StatistikPenjualanController::class, 'index']);
        Route::get('/export', [StatistikPenjualanController::class, 'export'])->name('.export');
    });

    Route::prefix('manage-kustom')->name('manage.kustom')->group(function () {
        Route::get('/', [ManageKustomisasiController::class, 'index'])->name('');
        Route::get('/create', [ManageKustomisasiController::class, 'create'])->name('.create');
        Route::post('/', [ManageKustomisasiController::class, 'store'])->name('.store');
        Route::get('/{id}/edit', [ManageKustomisasiController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [ManageKustomisasiController::class, 'update'])->name('.update');
        Route::delete('/{id}', [ManageKustomisasiController::class, 'destroy'])->name('.destroy');
    });

    Route::prefix('manage-transaksi')->name('manage.transaksi')->group(function () {
        Route::get('/', [KelolaTransaksiController::class, 'index']);
        Route::get('/check-resi', [KelolaTransaksiController::class, 'checkResi'])->name('.check-resi');
        Route::get('/get-ongkir', [KelolaTransaksiController::class, 'getOngkir'])->name('.get-ongkir');
        Route::post('/', [KelolaTransaksiController::class, 'store'])->name('.store');
        Route::put('/{id}', [KelolaTransaksiController::class, 'update'])->name('.update');
    });

    Route::get('/traffic', function () {
        return view('pages.user.admin.traffic.index');
    })->name('traffic');

    Route::prefix('manage-pegawai')->name('manage.pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store'])->name('.store');
        Route::put('/{user_id}', [PegawaiController::class, 'update'])->name('.update');
        Route::delete('/{user_id}', [PegawaiController::class, 'destroy'])->name('.destroy');
    });
});