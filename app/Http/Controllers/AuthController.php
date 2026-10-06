<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. HALAMAN LOGIN: Membuka layar pengisian Email & Password (bila ternyata sudah login sebelumnya, otomatis dijegal dan didorong maju ke Dashboard)
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // 2. PROSES COCOK DATA: Mesin pencocok! Memeriksa apakah password & email yg diketik cocok dengan data di database. Kalau gagal otomatis di-kick out dan disuruh mengulang!
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // 3. KELUAR SISTEM (LOGOUT): Menghancurkan tiket izin masuk saat tombol Logout ditekan, lalu melempar pengguna kembali ke layar Login awal.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
