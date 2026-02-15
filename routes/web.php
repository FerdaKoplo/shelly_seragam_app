<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use Illuminate\Support\Facades\Route;

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



// guest routes
Route::get('/', function () {
    return view('pages.guest.landing.landing');
})->name('home');

Route::get('/kustom', function () {
    return view('pages.guest.kustom.index');
})->name('kustom');

Route::get('/katalog', function () {
    return view('pages.guest.katalog.index');
})->name('katalog');
Route::get('/katalog/{slug}', function ($slug) {
    // a mock object to simulate a database record
    $product = (object) [
        'name' => 'Kemeja Kotak-Kotak Casual',
        'price' => 114000,
        'stock' => 100,
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'tags' => ['#Kemeja', '#Katun', '#Formal'],
        'slug' => $slug
    ];

    return view('pages.guest.katalog.detail', compact('product'));
})->name('product.show');


Route::get('/kerannjang', function () {
    return view('pages.guest.keranjang.index');
})->name('keranjang');


// user routes
Route::prefix('admin')->group(function () {
    // Pegawai
    Route::get('/manage-transaksi', function () {
        return view('pages.user.transaksi.index');
    })->name('manage.transaksi');


    Route::prefix('manage-katalog')->name('manage.katalog')->group(function () {
        Route::get('/', [KatalogProdukController::class, 'index']);
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
    });

    // Route::get('/manage-katalog', function () {
    //     return view('pages.user.katalog.index');
    // })->name('manage.katalog');

    Route::get('/manage-kustom', function () {
        return view('pages.user.kustom.index');
    })->name('manage.kustom');


    // Admin
    // Route::get('/statistik-transaksi', function () {
    //     return view('pages.user.admin.statistik-transaksi.index');
    // })->name('statistik.transaksi');

    Route::get('/traffic', function () {
        return view('pages.user.admin.statistik-transaksi.index');
    })->name('traffic');


    Route::prefix('manage-pegawai')->name('manage.pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store'])->name('.store');
        Route::put('/{user_id}', [PegawaiController::class, 'update'])->name('.update');
        Route::delete('/{user_id}', [PegawaiController::class, 'destroy'])->name('.destroy');
    });

});
