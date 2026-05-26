<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\CheckoutController;
use App\Http\Controllers\Guest\KatalogController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\Guest\ShippingController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\KelolaTransaksiController;
use App\Http\Controllers\User\ManageKustomisasiController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use App\Http\Controllers\User\VoucherController;
use App\Http\Controllers\Webhooks\XenditWebhookController;
use App\Models\CheckoutOrder;
use App\Models\ProdukKatalog;
use App\Models\PaymentInvoice;
use App\Services\CheckoutTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

/**
 * Keranjang
 */
Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');

Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::post('/add/{katalog_id}', [CartController::class, 'add'])
        ->whereNumber('katalog_id')
        ->name('add');

    Route::patch('/update/{katalog_id}', [CartController::class, 'update'])
        ->whereNumber('katalog_id')
        ->name('update');

    Route::patch('/notes', [CartController::class, 'updateNotes'])
        ->name('notes.update');

    Route::delete('/remove/{katalog_id}', [CartController::class, 'remove'])
        ->whereNumber('katalog_id')
        ->name('remove');

    Route::delete('/clear', [CartController::class, 'clear'])
        ->name('clear');
});

/**
 * RajaOngkir (Customer checkout helper)
 */
Route::prefix('shipping')->name('shipping.')->group(function () {
    Route::get('/destinations', [ShippingController::class, 'destinations'])->name('destinations');
    Route::post('/cost', [ShippingController::class, 'cost'])->name('cost');
});

/**
 * Webhooks
 */
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/xendit/invoice', [XenditWebhookController::class, 'invoice'])->name('xendit.invoice');
});

/**
 * Checkout
 */
Route::match(['GET', 'POST'], '/checkout', CheckoutController::class)->name('checkout');

// user routes
Route::prefix('admin')->middleware('auth')->group(function () {
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
        Route::get('/search-destination', [KelolaTransaksiController::class, 'searchDestination'])->name('.search-destination');
        Route::post('/', [KelolaTransaksiController::class, 'store'])->name('.store');
        Route::post('/upload-payment', [KelolaTransaksiController::class, 'uploadTransaksiKustomPayment'])->name('.upload-payment');
        Route::put('/{id}', [KelolaTransaksiController::class, 'update'])->name('.update');
    });


    Route::prefix('manage-voucher')->name('manage.voucher')->group(function () {
        Route::get('/', [VoucherController::class, 'index']);
        Route::get('/create', [VoucherController::class, 'create'])->name('.create');
        Route::get('/{id}/edit', [VoucherController::class, 'edit'])->name('.edit');

        Route::post('/', [VoucherController::class, 'store'])->name('.store');

        Route::put('/{id}', [VoucherController::class, 'update'])->name('.update');

        Route::patch('/{id}/deactivate', [VoucherController::class, 'deactiveVoucher'])->name('.deactivate');

        Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('.destroy');
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
