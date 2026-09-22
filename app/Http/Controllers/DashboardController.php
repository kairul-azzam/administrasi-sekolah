<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard sesuai peran (role) pengguna saat ini.
     * Menggunakan eager loading untuk menghindari query N+1.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $role = $user->role;

        $data = [
            'role' => $role,
            'user' => $user,
        ];

        if ($role === 'staf') {
            // Dashboard Staf:
            // 1. Ringkasan jumlah pengajuan milik sendiri per status
            $counts = Pengajuan::where('user_id', $user->id)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $data['statusCounts'] = [
                'draft' => $counts['draft'] ?? 0,
                'diajukan' => $counts['diajukan'] ?? 0,
                'diperiksa' => $counts['diperiksa'] ?? 0,
                'dikembalikan' => $counts['dikembalikan'] ?? 0,
                'disetujui' => $counts['disetujui'] ?? 0,
                'ditolak' => $counts['ditolak'] ?? 0,
                'total' => array_sum($counts),
            ];

            // 2. Lima pengajuan terbaru milik sendiri (dengan eager loading)
            $data['pengajuanTerbaru'] = Pengajuan::where('user_id', $user->id)
                ->with(['jenisPengajuan', 'siswa.kelas'])
                ->latest()
                ->take(5)
                ->get();

        } elseif ($role === 'petugas') {
            // Dashboard Petugas:
            // 1. Total siswa aktif & total kelas
            $data['totalSiswaAktif'] = Siswa::where('status', 'aktif')->count();
            $data['totalSiswaNonaktif'] = Siswa::where('status', 'nonaktif')->count();
            $data['totalKelas'] = Kelas::count();

            // 2. Pengajuan yang menunggu pemeriksaan (status = diajukan)
            $data['menungguPemeriksaan'] = Pengajuan::where('status', 'diajukan')->count();
            $data['totalPengajuan'] = Pengajuan::count();

            // 3. Lima pengajuan antrean pemeriksaan terbaru
            $data['pengajuanPeriksa'] = Pengajuan::where('status', 'diajukan')
                ->with(['user', 'jenisPengajuan', 'siswa.kelas'])
                ->latest()
                ->take(5)
                ->get();

        } elseif ($role === 'kepsek') {
            // Dashboard Kepsek:
            // 1. Menunggu keputusan (status = diperiksa)
            $data['menungguKeputusan'] = Pengajuan::where('status', 'diperiksa')->count();

            // 2. Jumlah pengajuan yang telah disetujui & ditolak
            $data['totalDisetujui'] = Pengajuan::where('status', 'disetujui')->count();
            $data['totalDitolak'] = Pengajuan::where('status', 'ditolak')->count();
            $data['totalSiswaAktif'] = Siswa::where('status', 'aktif')->count();

            // 3. Lima pengajuan menunggu keputusan terbaru
            $data['antreanKeputusan'] = Pengajuan::where('status', 'diperiksa')
                ->with(['user', 'jenisPengajuan', 'siswa.kelas'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard.index', $data);
    }
}
