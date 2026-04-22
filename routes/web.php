<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\KelolaTransaksiController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\ManageKustomisasiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\Guest\KatalogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// auth
Route::get('/login', function () {
    return view('pages.auth.login');
});
Route::post('/login', LoginController::class)->name('login');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// guest routes

// Home (landing) -> DB
Route::get('/', [LandingController::class, 'index'])->name('home');

// Kustom (masih statis view)
Route::get('/kustom', function () {
    return view('pages.guest.kustom.index');
})->name('kustom');

// Katalog list -> DB
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

// Katalog detail -> DB (ganti dari {slug} mock jadi {id})
Route::get('/katalog/{id}', [KatalogController::class, 'show'])
    ->whereNumber('id')
    ->name('product.show');

// Keranjang (masih statis view)
Route::get('/keranjang', function () {
    return view('pages.guest.keranjang.index');
})->name('keranjang');

// Checkout (masih mock, nanti bisa integrasi bertahap)
Route::get('/checkout', function (Request $request) {

    $type = $request->query('type', 'katalog');

    $mockKatalogItems = [
        ['id' => 1, 'name' => 'Kemeja Kotak', 'price' => 114000, 'quantity' => 2, 'size' => 'S', 'image' => 'product-1.png'],
        ['id' => 2, 'name' => 'Kemeja Kotak Blue', 'price' => 114000, 'quantity' => 1, 'size' => 'M', 'image' => 'product-1.png'],
    ];

    $mockCustomData = [
        'title' => 'Kain: Oxford Navy',
        'qty' => '12 pcs',
        'type' => 'Bundle (Atasan + Bawahan)',
        'price' => 1750000,
        'file' => 'desain_logo.png'
    ];

    $shippingOptions = [
        ['id' => 'reg', 'label' => 'Regular', 'price' => 15000],
        ['id' => 'exp', 'label' => 'Express', 'price' => 35000],
    ];

    return view('pages.guest.checkout.checkout', [
        'type' => $type,
        'items' => $mockKatalogItems,
        'customData' => $mockCustomData,
        'shippingOptions' => $shippingOptions
    ]);
})->name('checkout');

// user routes
Route::prefix('admin')->group(function () {
    // Pegawai
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
