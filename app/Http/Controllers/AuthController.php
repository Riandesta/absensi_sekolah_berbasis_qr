<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah username ada di database
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if (!$user) {
            // Jika username tidak ditemukan
            return back()->withErrors([
                'username' => 'Username tidak ditemukan.',
            ])->onlyInput('username');
        }

        // Cek apakah password benar
        if (!\Hash::check($credentials['password'], $user->password)) {
            // Jika password salah
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->onlyInput('username');
        }

        // Jika kredensial valid, login pengguna
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect berdasarkan role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'karyawan' => redirect()->route('karyawan.dashboard'),
            'kurikulum' => redirect()->route('kurikulum.dashboard'),
            'walikelas' => redirect()->route('walikelas.dashboard'),
            default => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
