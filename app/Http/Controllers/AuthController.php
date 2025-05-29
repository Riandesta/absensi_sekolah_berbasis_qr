<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


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
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            // Jika username tidak ditemukan
            return back()->withErrors([
                'username' => 'Username tidak ditemukan.',
            ])->onlyInput('username');
        }

        // Cek apakah password benar
        if (!Hash::check($credentials['password'], $user->password)) {
            // Jika password salah
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->onlyInput('username');
        }

        // Jika kredensial valid, login pengguna
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect berdasarkan role dengan path yang benar
        return match ($user->role) {
            'admin' => redirect()->intended('/admin/dashboard'),
            'siswa' => redirect()->intended('/siswa/dashboard'),
            'karyawan' => redirect()->intended('/karyawan/dashboard'),
            'kurikulum' => redirect()->intended('/kurikulum/dashboard'),
            'walikelas' => redirect()->intended('/walikelas/dashboard'),
            'kelas' => redirect()->intended('/kelas/dashboard'),
            default => $this->logoutWithError()
        };
    }

    protected function logoutWithError()
    {
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak dikenali.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
