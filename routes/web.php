<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
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
    return view('pages.guest.landing');
})->name('kustom');

Route::get('/katalog', function () {
    return view('pages.guest.landing');
})->name('katalog');

Route::get('/cart', function () {
    return view('pages.guest.landing');
})->name('cart');
