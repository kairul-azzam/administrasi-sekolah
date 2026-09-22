<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Administrasi Sekolah
|--------------------------------------------------------------------------
|
| Rute web aplikasi menggunakan MVC standar, middleware auth bawaan,
| dan custom middleware 'role' (CheckRole) untuk otorisasi berjenjang.
|
*/

// Halaman utama: redirect otomatis berdasarkan status login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ==========================================
// RUTE GUEST (AUTENTIKASI MANUAL)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ==========================================
// RUTE TERAUTENTIKASI (SEMUA ROLE)
// ==========================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Multi-Role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // MODUL DATA SISWA
    // ==========================================
    // Semua role dapat melihat daftar dan detail siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    
    // Hanya Petugas yang dapat menambah dan mengubah data siswa (role:petugas)
    Route::middleware('role:petugas')->group(function () {
        Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
        Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    });

    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');

    // ==========================================
    // MODUL PENGAJUAN SURAT
    // ==========================================
    // Daftar pengajuan (disaring otomatis di controller sesuai role)
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');

    // Khusus Staf: membuat, mengubah, dan membatalkan pengajuan (role:staf)
    Route::middleware('role:staf')->group(function () {
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{pengajuan}', [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::delete('/pengajuan/{pengajuan}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
    });

    // Detail pengajuan (dengan validasi kepemilikan bagi staf)
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');

    // Khusus Petugas: memeriksa pengajuan (teruskan / kembalikan revisi)
    Route::post('/pengajuan/{pengajuan}/periksa', [PengajuanController::class, 'periksa'])
        ->middleware('role:petugas')
        ->name('pengajuan.periksa');

    // Khusus Kepsek: persetujuan pengajuan (setujui / tolak)
    Route::post('/pengajuan/{pengajuan}/keputusan', [PengajuanController::class, 'keputusan'])
        ->middleware('role:kepsek')
        ->name('pengajuan.keputusan');
});
