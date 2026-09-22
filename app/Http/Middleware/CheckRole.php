<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Menangani pemeriksaan role pengguna sebelum mengakses rute tertentu.
     *
     * Middleware ini dapat menerima satu atau beberapa role sekaligus:
     * Contoh: role:petugas atau role:staf,kepsek
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan user telah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Verifikasi apakah role user saat ini cocok dengan salah satu role yang diizinkan
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
