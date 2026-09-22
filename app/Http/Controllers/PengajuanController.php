<?php

namespace App\Http\Controllers;

use App\Http\Requests\KeputusanPengajuanRequest;
use App\Http\Requests\PeriksaPengajuanRequest;
use App\Http\Requests\StorePengajuanRequest;
use App\Http\Requests\UpdatePengajuanRequest;
use App\Models\JenisPengajuan;
use App\Models\Pengajuan;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar pengajuan surat sesuai wewenang role yang sedang login.
     * Menerapkan eager loading with(['user', 'jenisPengajuan', 'siswa.kelas']) untuk mencegah N+1 query.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Pengajuan::with(['user', 'jenisPengajuan', 'siswa.kelas']);

        // 1. Otorisasi Data Berdasarkan Role
        if ($user->isStaf()) {
            // Staf hanya dapat melihat pengajuan miliknya sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->isKepsek()) {
            // Kepala Sekolah hanya melihat pengajuan yang sudah diperiksa atau sudah diputuskan
            $query->whereIn('status', ['diperiksa', 'disetujui', 'ditolak']);
        }
        // Petugas TU dapat melihat semua pengajuan tanpa batasan kepemilikan

        // 2. Filter Pencarian Teks (Nama Siswa, NIS, atau Keterangan)
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('keterangan', 'like', "%{$keyword}%")
                    ->orWhereHas('siswa', function ($sq) use ($keyword) {
                        $sq->where('nama', 'like', "%{$keyword}%")
                           ->orWhere('nis', 'like', "%{$keyword}%");
                    });
            });
        }

        // 3. Filter Status Pengajuan
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 4. Filter Jenis Pengajuan Surat
        if ($request->filled('jenis_pengajuan_id')) {
            $query->where('jenis_pengajuan_id', $request->input('jenis_pengajuan_id'));
        }

        // 5. Filter Rentang Tanggal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->input('tanggal_awal'));
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->input('tanggal_akhir'));
        }

        // 6. Pagination 10 data per halaman dengan mempertahankan query string filter
        $pengajuan = $query->latest('id')->paginate(10)->withQueryString();

        // Data pendukung untuk dropdown filter
        $jenisList = JenisPengajuan::orderBy('nama_jenis')->get();

        return view('pengajuan.index', compact('pengajuan', 'jenisList'));
    }

    /**
     * Menampilkan formulir pembuatan pengajuan surat baru (khusus role: staf).
     */
    public function create(): View
    {
        $jenisList = JenisPengajuan::orderBy('nama_jenis')->get();
        $siswaList = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('pengajuan.create', compact('jenisList', 'siswaList'));
    }

    /**
     * Menyimpan pengajuan surat baru dengan status 'draft' atau 'diajukan'.
     * Menjalankan pencatatan log awal ke tabel persetujuan dalam transaksi database.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $statusBaru = $request->input('aksi') === 'draft' ? 'draft' : 'diajukan';

        $pengajuan = DB::transaction(function () use ($request, $statusBaru) {
            // 1. Buat record pengajuan
            $pengajuan = Pengajuan::create([
                'user_id' => Auth::id(),
                'jenis_pengajuan_id' => $request->input('jenis_pengajuan_id'),
                'siswa_id' => $request->input('siswa_id'),
                'tanggal_pengajuan' => $request->input('tanggal_pengajuan'),
                'keterangan' => $request->input('keterangan'),
                'status' => $statusBaru,
            ]);

            // 2. Tulis baris pertama riwayat status di tabel persetujuan
            $catatanAwal = $statusBaru === 'draft'
                ? 'Draf permohonan dibuat dan disimpan oleh staf.'
                : 'Permohonan diajukan oleh staf untuk diperiksa petugas administrasi.';

            $pengajuan->persetujuan()->create([
                'user_id' => Auth::id(),
                'status_lama' => null,
                'status_baru' => $statusBaru,
                'catatan' => $catatanAwal,
            ]);

            return $pengajuan;
        });

        $pesan = $statusBaru === 'draft'
            ? 'Draf pengajuan surat berhasil disimpan.'
            : 'Pengajuan surat berhasil dikirim dan menunggu pemeriksaan petugas.';

        return redirect()->route('pengajuan.show', $pengajuan)->with('success', $pesan);
    }

    /**
     * Menampilkan detail berkas pengajuan lengkap beserta alur timeline riwayat statusnya.
     */
    public function show(Pengajuan $pengajuan): View
    {
        $user = Auth::user();

        // 1. Pengecekan Kepemilikan: Staf dilarang membuka pengajuan milik staf lain
        if ($user->isStaf() && $pengajuan->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat pengajuan milik staf lain.');
        }

        // 2. Pengecekan Akses Kepsek: hanya yang berstatus diperiksa, disetujui, atau ditolak
        if ($user->isKepsek() && !in_array($pengajuan->status, ['diperiksa', 'disetujui', 'ditolak'], true)) {
            abort(403, 'Akses Ditolak: Pengajuan ini belum berada pada tahap persetujuan Kepala Sekolah.');
        }

        // 3. Eager Loading relasi pemohon, jenis surat, siswa, kelas, dan riwayat persetujuan beserta user pelakunya
        $pengajuan->load([
            'user',
            'jenisPengajuan',
            'siswa.kelas',
            'persetujuan' => function ($query) {
                $query->with('user')->oldest();
            },
        ]);

        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Menampilkan form edit pengajuan (hanya untuk staf pemilik dan status draft/diajukan/dikembalikan).
     */
    public function edit(Pengajuan $pengajuan): View
    {
        $user = Auth::user();

        // Verifikasi kepemilikan
        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengubah pengajuan staf lain.');
        }

        // Verifikasi status: hanya draft, diajukan, atau dikembalikan
        if (!$pengajuan->canBeModifiedByStaf()) {
            abort(403, 'Akses Ditolak: Pengajuan ini telah masuk tahap pemeriksaan dan tidak dapat diubah lagi.');
        }

        $jenisList = JenisPengajuan::orderBy('nama_jenis')->get();
        $siswaList = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orWhere('id', $pengajuan->siswa_id)
            ->orderBy('nama')
            ->get();

        return view('pengajuan.edit', compact('pengajuan', 'jenisList', 'siswaList'));
    }

    /**
     * Memperbarui isi pengajuan dan statusnya (draft -> diajukan, dikembalikan -> diajukan ulang, dsb).
     */
    public function update(UpdatePengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        $targetStatus = $request->input('aksi') === 'draft' ? 'draft' : 'diajukan';
        $statusLama = $pengajuan->status;

        DB::transaction(function () use ($request, $pengajuan, $targetStatus, $statusLama) {
            // Update data pengajuan
            $pengajuan->update([
                'jenis_pengajuan_id' => $request->input('jenis_pengajuan_id'),
                'siswa_id' => $request->input('siswa_id'),
                'tanggal_pengajuan' => $request->input('tanggal_pengajuan'),
                'keterangan' => $request->input('keterangan'),
            ]);

            // Jika ada perubahan status atau pengajuan ulang setelah revisi
            if ($statusLama !== $targetStatus || $statusLama === 'dikembalikan') {
                $catatan = match(true) {
                    $statusLama === 'dikembalikan' && $targetStatus === 'diajukan' => 'Pengajuan telah diperbaiki dan diajukan ulang oleh pemohon.',
                    $statusLama === 'draft' && $targetStatus === 'diajukan' => 'Draf pengajuan resmi diajukan untuk pemeriksaan.',
                    default => 'Informasi pengajuan diperbarui oleh pemohon.',
                };

                // Pakai method ubahStatus() untuk update status & insert log riwayat dalam transaksi
                $pengajuan->ubahStatus($targetStatus, $catatan, Auth::id());
            }
        });

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Perubahan berkas pengajuan berhasil disimpan.');
    }

    /**
     * Membatalkan (menghapus) pengajuan milik staf (hanya draft, diajukan, atau dikembalikan).
     */
    public function destroy(Pengajuan $pengajuan): RedirectResponse
    {
        $user = Auth::user();

        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak berhak menghapus pengajuan milik staf lain.');
        }

        if (!$pengajuan->canBeModifiedByStaf()) {
            abort(403, 'Akses Ditolak: Pengajuan yang telah diperiksa atau diputuskan tidak dapat dibatalkan.');
        }

        $pengajuan->delete();

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan surat berhasil dibatalkan dan dihapus.');
    }

    /**
     * Memproses pemeriksaan oleh petugas administrasi (teruskan ke kepsek atau kembalikan untuk revisi).
     */
    public function periksa(PeriksaPengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Pastikan status pengajuan saat ini adalah 'diajukan'
        if ($pengajuan->status !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan dengan status "Diajukan" yang dapat diperiksa.');
        }

        $aksi = $request->input('aksi');
        $catatan = $request->input('catatan');

        if ($aksi === 'periksa') {
            // Ubah status ke 'diperiksa' (diteruskan ke Kepala Sekolah)
            $catatanAkhir = $catatan ?: 'Berkas lengkap dan telah diverifikasi valid oleh petugas administrasi. Diteruskan ke Kepala Sekolah.';
            $pengajuan->ubahStatus('diperiksa', $catatanAkhir, Auth::id());

            return redirect()->route('pengajuan.show', $pengajuan)
                ->with('success', 'Pengajuan berhasil diverifikasi dan diteruskan ke Kepala Sekolah.');
        }

        if ($aksi === 'kembalikan') {
            // Ubah status ke 'dikembalikan' dengan catatan wajib
            $pengajuan->ubahStatus('dikembalikan', $catatan, Auth::id());

            return redirect()->route('pengajuan.show', $pengajuan)
                ->with('info', 'Pengajuan berhasil dikembalikan ke staf pemohon untuk diperbaiki.');
        }

        return back();
    }

    /**
     * Memproses keputusan akhir oleh Kepala Sekolah (setujui atau tolak).
     */
    public function keputusan(KeputusanPengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Pastikan status pengajuan saat ini adalah 'diperiksa'
        if ($pengajuan->status !== 'diperiksa') {
            return back()->with('error', 'Hanya pengajuan dengan status "Diperiksa" yang dapat diputuskan oleh Kepala Sekolah.');
        }

        $aksi = $request->input('aksi');
        $catatan = $request->input('catatan');

        if ($aksi === 'setujui') {
            // Ubah status ke 'disetujui'
            $catatanAkhir = $catatan ?: 'Permohonan surat resmi disetujui oleh Kepala Sekolah.';
            $pengajuan->ubahStatus('disetujui', $catatanAkhir, Auth::id());

            return redirect()->route('pengajuan.show', $pengajuan)
                ->with('success', 'Permohonan surat telah resmi disetujui.');
        }

        if ($aksi === 'tolak') {
            // Ubah status ke 'ditolak' dengan catatan wajib
            $pengajuan->ubahStatus('ditolak', $catatan, Auth::id());

            return redirect()->route('pengajuan.show', $pengajuan)
                ->with('error', 'Permohonan surat telah ditolak.');
        }

        return back();
    }
}
