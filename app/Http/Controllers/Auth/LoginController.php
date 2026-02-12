<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validate = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8|max:255'
        ], [
            'required' => 'Lengkapi Semua Data',
        ]);

        $credentials = ['username' => $request->username, 'password' => $request->password];



        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role == 'Admin') {
                return redirect()->route('manage.transaksi');
            } else {
                return redirect()->route('statistik.transaksi');
            }
        }

        return back()->withErrors([
            'username' => 'Akun tidak ditemukan atau password salah',
        ])->onlyInput('username');
    }
}
