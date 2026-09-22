<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan filter, pencarian, dan pagination 10 item per halaman.
     * Menggunakan eager loading with('kelas') untuk mencegah N+1 query.
     */
    public function index(Request $request): View
    {
        // 1. Inisialisasi query dengan eager loading relasi kelas
        $query = Siswa::with('kelas');

        // 2. Filter Pencarian: Nama atau NIS
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%");
            });
        }

        // 3. Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->input('kelas_id'));
        }

        // 4. Filter Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        // 5. Filter Status (aktif / nonaktif)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 6. Pagination 10 data per halaman dengan mempertahankan parameter URL query string
        $siswa = $query->latest('id')->paginate(10)->withQueryString();

        // Ambil daftar kelas untuk dropdown filter
        $kelasList = Kelas::orderBy('kelas')->get();

        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    /**
     * Menampilkan form penambahan siswa baru (hanya untuk role: petugas).
     */
    public function create(): View
    {
        $kelasList = Kelas::orderBy('kelas')->get();
        return view('siswa.create', compact('kelasList'));
    }

    /**
     * Menyimpan data siswa baru ke database (hanya untuk role: petugas).
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        $siswa = Siswa::create($request->validated());

        return redirect()->route('siswa.index')
            ->with('success', "Data siswa {$siswa->nama} (NIS: {$siswa->nis}) berhasil ditambahkan.");
    }

    /**
     * Menampilkan profil detail siswa beserta riwayat pengajuan suratnya.
     */
    public function show(Siswa $siswa): View
    {
        // Eager load relasi kelas dan riwayat pengajuan surat siswa
        $siswa->load([
            'kelas',
            'pengajuan' => function ($query) {
                $query->with(['jenisPengajuan', 'user'])->latest();
            },
        ]);

        return view('siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan form edit data siswa (hanya untuk role: petugas).
     */
    public function edit(Siswa $siswa): View
    {
        $kelasList = Kelas::orderBy('kelas')->get();
        return view('siswa.edit', compact('siswa', 'kelasList'));
    }

    /**
     * Memperbarui data siswa di database (hanya untuk role: petugas).
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->route('siswa.show', $siswa)
            ->with('success', "Data siswa {$siswa->nama} berhasil diperbarui.");
    }
}
