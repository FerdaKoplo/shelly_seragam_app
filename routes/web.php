<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\PegawaiController;
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
    return view('pages.guest.landing');
})->name('home');

Route::get('/kustom', function () {
    return view('pages.guest.kustom.index');
})->name('kustom');

Route::get('/katalog', function () {
    return view('pages.guest.katalog.index');
})->name('katalog');

Route::get('/kerannjang', function () {
    return view('pages.guest.keranjang.index');
})->name('keranjang');


// user routes
Route::prefix('admin')->group(function () {
    // Pegawai
    Route::get('/manage-transaksi', function () {
        return view('pages.user.transaksi.index');
    })->name('manage.transaksi');

    Route::get('/manage-katalog', function () {
        return view('pages.user.katalog.index');
    })->name('manage.katalog');

    Route::get('/manage-kustom', function () {
        return view('pages.user.kustom.index');
    })->name('manage.kustom');


    // Admin
    Route::get('/statistik-transaksi', function () {
        return view('pages.user.admin.statistik-transaksi.index');
    })->name('statistik.transaksi');

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
