<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login manual.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Jika pengguna sudah login, arahkan langsung ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses autentikasi pengguna secara manual tanpa paket pihak ketiga.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Melakukan percobaan login via Laravel Auth bawaan
        if (Auth::attempt($credentials, $remember)) {
            // Mencegah Session Fixation Attack
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        // Jika kredensial salah, kembalikan ke form login dengan pesan kesalahan
        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi yang dimasukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Mengeluarkan pengguna dari sistem (logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate session dan regenerate CSRF token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari aplikasi.');
    }
}
