<?php

namespace Tests\Feature;

use App\Models\JenisPengajuan;
use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\Persetujuan;
use App\Models\Siswa;
use App\Models\User;
use Tests\TestCase;

class AdministrasiSekolahTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->seed();
    }

    /**
     * Uji alur lengkap:
     * 1. Staf login & buat pengajuan (simpan draf -> ajukan)
     * 2. Petugas login & kembalikan pengajuan dengan catatan (uji required_if catatan)
     * 3. Staf ubah & ajukan ulang
     * 4. Petugas periksa & teruskan ke kepsek
     * 5. Kepsek putuskan & setujui
     * 6. Seluruh riwayat tercatat di tabel persetujuan
     */
    public function test_alur_lengkap_siklus_pengajuan(): void
    {
        $staf = User::where('email', 'staf@sekolah.test')->first();
        $petugas = User::where('email', 'petugas@sekolah.test')->first();
        $kepsek = User::where('email', 'kepsek@sekolah.test')->first();
        $siswa = Siswa::first();
        $jenis = JenisPengajuan::first();

        // 1. Staf membuat pengajuan status draft
        $response = $this->actingAs($staf)->post(route('pengajuan.store'), [
            'siswa_id' => $siswa->id,
            'jenis_pengajuan_id' => $jenis->id,
            'tanggal_pengajuan' => now()->toDateString(),
            'keterangan' => 'Pengajuan surat uji siklus lengkap.',
            'aksi' => 'draft',
        ]);
        $response->assertRedirect();

        $pengajuan = Pengajuan::where('keterangan', 'Pengajuan surat uji siklus lengkap.')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals('draft', $pengajuan->status);

        // Staf mengubah draft menjadi diajukan
        $this->actingAs($staf)->put(route('pengajuan.update', $pengajuan), [
            'siswa_id' => $siswa->id,
            'jenis_pengajuan_id' => $jenis->id,
            'tanggal_pengajuan' => now()->toDateString(),
            'keterangan' => 'Pengajuan surat uji siklus lengkap - diajukan.',
            'aksi' => 'diajukan',
        ])->assertRedirect();

        $pengajuan->refresh();
        $this->assertEquals('diajukan', $pengajuan->status);

        // 2. Petugas mencoba kembalikan tanpa catatan (harus gagal validasi required_if)
        $this->actingAs($petugas)->post(route('pengajuan.periksa', $pengajuan), [
            'aksi' => 'kembalikan',
            'catatan' => '',
        ])->assertSessionHasErrors(['catatan']);

        // Petugas mengembalikan pengajuan dengan catatan yang sah
        $this->actingAs($petugas)->post(route('pengajuan.periksa', $pengajuan), [
            'aksi' => 'kembalikan',
            'catatan' => 'Harap lengkapi nomor kontak orang tua.',
        ])->assertRedirect();

        $pengajuan->refresh();
        $this->assertEquals('dikembalikan', $pengajuan->status);

        // 3. Staf memperbaiki keterangan dan mengajukan ulang
        $this->actingAs($staf)->put(route('pengajuan.update', $pengajuan), [
            'siswa_id' => $siswa->id,
            'jenis_pengajuan_id' => $jenis->id,
            'tanggal_pengajuan' => now()->toDateString(),
            'keterangan' => 'Pengajuan surat uji siklus lengkap - revisi nomor kontak: 08123456789.',
            'aksi' => 'diajukan',
        ])->assertRedirect();

        $pengajuan->refresh();
        $this->assertEquals('diajukan', $pengajuan->status);

        // 4. Petugas memverifikasi dan meneruskan ke kepala sekolah
        $this->actingAs($petugas)->post(route('pengajuan.periksa', $pengajuan), [
            'aksi' => 'periksa',
            'catatan' => 'Berkas valid dan lengkap.',
        ])->assertRedirect();

        $pengajuan->refresh();
        $this->assertEquals('diperiksa', $pengajuan->status);

        // 5. Kepsek mencoba menolak tanpa catatan (harus gagal validasi required_if)
        $this->actingAs($kepsek)->post(route('pengajuan.keputusan', $pengajuan), [
            'aksi' => 'tolak',
            'catatan' => '',
        ])->assertSessionHasErrors(['catatan']);

        // Kepsek menyetujui pengajuan
        $this->actingAs($kepsek)->post(route('pengajuan.keputusan', $pengajuan), [
            'aksi' => 'setujui',
            'catatan' => 'Disetujui untuk diterbitkan.',
        ])->assertRedirect();

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);

        // 6. Verifikasi seluruh log riwayat di tabel persetujuan
        $riwayat = Persetujuan::where('pengajuan_id', $pengajuan->id)->get();
        $this->assertGreaterThanOrEqual(4, $riwayat->count());

        // Pastikan transisi status terakhir adalah disetujui oleh kepsek
        $logTerakhir = $riwayat->last();
        $this->assertEquals('disetujui', $logTerakhir->status_baru);
        $this->assertEquals($kepsek->id, $logTerakhir->user_id);
    }

    /**
     * Uji pembatasan hak akses dan otorisasi role (CheckRole Middleware & 403 Forbidden).
     */
    public function test_pembatasan_hak_akses_dan_middleware_check_role(): void
    {
        $staf = User::where('email', 'staf@sekolah.test')->first();
        $petugas = User::where('email', 'petugas@sekolah.test')->first();
        $kepsek = User::where('email', 'kepsek@sekolah.test')->first();

        // 1. Staf dilarang membuka form tambah siswa (role:petugas)
        $this->actingAs($staf)->get(route('siswa.create'))->assertStatus(403);

        // 2. Kepsek dilarang membuka form tambah siswa (role:petugas)
        $this->actingAs($kepsek)->get(route('siswa.create'))->assertStatus(403);

        // 3. Petugas boleh membuka form tambah siswa
        $this->actingAs($petugas)->get(route('siswa.create'))->assertStatus(200);

        // 4. Petugas dilarang membuat pengajuan surat (role:staf)
        $this->actingAs($petugas)->get(route('pengajuan.create'))->assertStatus(403);

        // 5. Kepsek dilarang membuat pengajuan surat (role:staf)
        $this->actingAs($kepsek)->get(route('pengajuan.create'))->assertStatus(403);

        // 6. Staf boleh membuka form buat pengajuan
        $this->actingAs($staf)->get(route('pengajuan.create'))->assertStatus(200);
    }

    /**
     * Uji perlindungan kepemilikan berkas (staf dilarang membuka/mengubah milik staf lain).
     */
    public function test_proteksi_kepemilikan_pengajuan_staf(): void
    {
        $staf1 = User::where('email', 'staf@sekolah.test')->first();

        // Buat staf kedua
        $staf2 = User::create([
            'name' => 'Guru Lain, S.Pd.',
            'email' => 'guru.lain@sekolah.test',
            'password' => bcrypt('password'),
            'role' => 'staf',
        ]);

        $siswa = Siswa::first();
        $jenis = JenisPengajuan::first();

        // Staf 2 membuat pengajuan miliknya
        $pengajuanStaf2 = Pengajuan::create([
            'user_id' => $staf2->id,
            'siswa_id' => $siswa->id,
            'jenis_pengajuan_id' => $jenis->id,
            'tanggal_pengajuan' => now()->toDateString(),
            'keterangan' => 'Pengajuan rahasia staf 2.',
            'status' => 'diajukan',
        ]);

        // Staf 1 mencoba membuka detail pengajuan staf 2 -> harus 403 Forbidden
        $this->actingAs($staf1)->get(route('pengajuan.show', $pengajuanStaf2))->assertStatus(403);

        // Staf 1 mencoba mengedit pengajuan staf 2 -> harus 403 Forbidden
        $this->actingAs($staf1)->get(route('pengajuan.edit', $pengajuanStaf2))->assertStatus(403);
    }

    /**
     * Uji modul siswa: pencarian, filter, dan pagination bersamaan withQueryString.
     */
    public function test_modul_siswa_search_filter_pagination(): void
    {
        $staf = User::where('email', 'staf@sekolah.test')->first();
        $kelas = Kelas::first();

        $response = $this->actingAs($staf)->get(route('siswa.index', [
            'kelas_id' => $kelas->id,
            'jenis_kelamin' => 'laki',
            'status' => 'aktif',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Data Kesiswaan');
    }
}
