<?php

namespace Database\Seeders;

use App\Models\JenisPengajuan;
use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\Persetujuan;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data awal aplikasi.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // ==========================================
        // 1. SEED 3 USER UTAMA SESUAI SPESIFIKASI
        // ==========================================
        $staf = User::create([
            'name' => 'Budi Prasetyo, S.Pd.',
            'email' => 'staf@sekolah.test',
            'password' => Hash::make('password'),
            'role' => 'staf',
        ]);

        $petugas = User::create([
            'name' => 'Siti Rahmawati, A.Md.',
            'email' => 'petugas@sekolah.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        $kepsek = User::create([
            'name' => 'Drs. H. Ahmad Dahlan, M.Pd.',
            'email' => 'kepsek@sekolah.test',
            'password' => Hash::make('password'),
            'role' => 'kepsek',
        ]);

        // ==========================================
        // 2. SEED 6 KELAS
        // ==========================================
        $daftarKelas = [
            ['kelas' => 'X RPL', 'tahun_ajaran' => '2025/2026'],
            ['kelas' => 'X TKJ', 'tahun_ajaran' => '2025/2026'],
            ['kelas' => 'XI RPL', 'tahun_ajaran' => '2025/2026'],
            ['kelas' => 'XI TKJ', 'tahun_ajaran' => '2025/2026'],
            ['kelas' => 'XII RPL', 'tahun_ajaran' => '2025/2026'],
            ['kelas' => 'XII TKJ', 'tahun_ajaran' => '2025/2026'],
        ];

        $kelasList = [];
        foreach ($daftarKelas as $k) {
            $kelasList[] = Kelas::create($k);
        }

        // ==========================================
        // 3. SEED 60 SISWA (FAKER LOCALE id_ID)
        // ==========================================
        $siswaList = [];
        $nisCounter = 2025001;

        for ($i = 0; $i < 60; $i++) {
            $jk = $faker->randomElement(['laki', 'perempuan']);
            $nama = $jk === 'laki'
                ? $faker->firstNameMale() . ' ' . $faker->lastName()
                : $faker->firstNameFemale() . ' ' . $faker->lastName();

            $kelas = $kelasList[$i % count($kelasList)];
            $status = ($i % 10 === 0) ? 'nonaktif' : 'aktif';

            $siswaList[] = Siswa::create([
                'kelas_id' => $kelas->id,
                'nis' => (string)$nisCounter++,
                'nama' => $nama,
                'jenis_kelamin' => $jk,
                'tanggal_lahir' => $faker->dateTimeBetween('2007-01-01', '2009-12-31')->format('Y-m-d'),
                'alamat' => $faker->address(),
                'status' => $status,
            ]);
        }

        // ==========================================
        // 4. SEED 6 JENIS PENGAJUAN
        // ==========================================
        $daftarJenis = [
            [
                'nama_jenis' => 'Surat Keterangan Siswa Aktif',
                'deskripsi' => 'Surat resmi untuk keperluan administrasi dinas, tunjangan orang tua, atau perbankan yang menerangkan bahwa siswa terdaftar aktif.',
            ],
            [
                'nama_jenis' => 'Surat Keterangan Berkelakuan Baik',
                'deskripsi' => 'Surat yang menerangkan bahwa siswa memiliki catatan budi pekerti baik dan tidak pernah melanggar peraturan sekolah.',
            ],
            [
                'nama_jenis' => 'Permohonan Dispensasi Kegiatan',
                'deskripsi' => 'Permohonan izin atau dispensasi kehadiran belajar untuk mengikuti perlombaan, pelatihan, atau kegiatan perwakilan sekolah.',
            ],
            [
                'nama_jenis' => 'Permohonan Mutasi Masuk',
                'deskripsi' => 'Pengajuan penerimaan peserta didik pindahan dari sekolah setingkat lain.',
            ],
            [
                'nama_jenis' => 'Permohonan Mutasi Keluar',
                'deskripsi' => 'Surat permohonan pindah sekolah ke instansi pendidikan lain beserta kelengkapan berkas kepindahan.',
            ],
            [
                'nama_jenis' => 'Rekomendasi Beasiswa',
                'deskripsi' => 'Surat pengantar dan rekomendasi sekolah untuk permohonan beasiswa prestasi maupun Program Indonesia Pintar (PIP).',
            ],
        ];

        $jenisList = [];
        foreach ($daftarJenis as $j) {
            $jenisList[] = JenisPengajuan::create($j);
        }

        // ==========================================
        // 5. SEED 25 PENGAJUAN DENGAN LOG PERSETUJUAN
        // ==========================================
        // Skenario pengajuan:
        // - 4 Draft
        // - 5 Diajukan
        // - 4 Diperiksa (sudah diverifikasi petugas, menunggu kepsek)
        // - 4 Dikembalikan (petugas mengembalikan ke staf dengan catatan revisi)
        // - 5 Disetujui (kepsek menyetujui)
        // - 3 Ditolak (kepsek menolak dengan catatan wajib)
        // Total = 25

        $skenario = [
            // Status Draft (4)
            ['status' => 'draft', 'keterangan' => 'Permohonan surat keterangan aktif untuk klaim tunjangan keluarga ayah.'],
            ['status' => 'draft', 'keterangan' => 'Draf permohonan dispensasi perlombaan LKS tingkat kota.'],
            ['status' => 'draft', 'keterangan' => 'Pengajuan surat kelakuan baik untuk syarat magang industri.'],
            ['status' => 'draft', 'keterangan' => 'Draf pengantar pendaftaran program beasiswa prestasi.'],

            // Status Diajukan (5)
            ['status' => 'diajukan', 'keterangan' => 'Mengajukan surat aktif sekolah untuk persyaratan pembukaan rekening beasiswa.'],
            ['status' => 'diajukan', 'keterangan' => 'Permohonan surat keterangan kelakuan baik untuk melengkapi berkas beasiswa luar daerah.'],
            ['status' => 'diajukan', 'keterangan' => 'Dispensasi mengikuti training camp olimpiade sains selama 3 hari kerja.'],
            ['status' => 'diajukan', 'keterangan' => 'Pengajuan mutasi keluar siswa pindah domisili mengikuti penugasan orang tua.'],
            ['status' => 'diajukan', 'keterangan' => 'Surat rekomendasi beasiswa prestasi cabang olahraga pencak silat.'],

            // Status Diperiksa (4) - Diteruskan oleh Petugas
            ['status' => 'diperiksa', 'keterangan' => 'Surat aktif belajar untuk perpanjangan tunjangan BPJS Kesehatan keluarga.'],
            ['status' => 'diperiksa', 'keterangan' => 'Permohonan mutasi masuk dari SMK Negeri 2 Cimahi bidang keahlian yang sama.'],
            ['status' => 'diperiksa', 'keterangan' => 'Surat keterangan kelakuan baik untuk pendaftaran seleksi calon anggota paskibra.'],
            ['status' => 'diperiksa', 'keterangan' => 'Rekomendasi beasiswa Yayasan Bakti Pendidikan tahun 2026.'],

            // Status Dikembalikan (4) - Dikembalikan oleh Petugas untuk Revisi
            ['status' => 'dikembalikan', 'keterangan' => 'Permohonan mutasi keluar ke SMK di Surabaya.'],
            ['status' => 'dikembalikan', 'keterangan' => 'Surat pengantar rekomendasi beasiswa kurang mampu.'],
            ['status' => 'dikembalikan', 'keterangan' => 'Permohonan surat aktif untuk pembuatan paspor siswa.'],
            ['status' => 'dikembalikan', 'keterangan' => 'Dispensasi izin kegiatan kepemudaan daerah.'],

            // Status Disetujui (5) - Disetujui oleh Kepsek
            ['status' => 'disetujui', 'keterangan' => 'Permohonan surat keterangan aktif untuk pencairan dana PIP tahap 1.'],
            ['status' => 'disetujui', 'keterangan' => 'Surat kelakuan baik untuk kelengkapan administrasi beasiswa Pemprov.'],
            ['status' => 'disetujui', 'keterangan' => 'Dispensasi lomba debat bahasa Inggris nasional perwakilan sekolah.'],
            ['status' => 'disetujui', 'keterangan' => 'Rekomendasi beasiswa berprestasi jalur akademik tingkat provinsi.'],
            ['status' => 'disetujui', 'keterangan' => 'Surat keterangan siswa aktif keperluan pembuatan visa student exchange.'],

            // Status Ditolak (3) - Ditolak oleh Kepsek
            ['status' => 'ditolak', 'keterangan' => 'Permohonan mutasi masuk pada semester genap kelas XII.'],
            ['status' => 'ditolak', 'keterangan' => 'Dispensasi kegiatan di luar agenda akademik selama 2 minggu.'],
            ['status' => 'ditolak', 'keterangan' => 'Permohonan surat keterangan kelakuan baik saat siswa masih masa pembinaan.'],
        ];

        foreach ($skenario as $idx => $item) {
            $siswa = $siswaList[$idx % count($siswaList)];
            $jenis = $jenisList[$idx % count($jenisList)];
            $tanggal = Carbon::now()->subDays(25 - $idx)->format('Y-m-d');

            $pengajuan = Pengajuan::create([
                'user_id' => $staf->id,
                'jenis_pengajuan_id' => $jenis->id,
                'siswa_id' => $siswa->id,
                'tanggal_pengajuan' => $tanggal,
                'keterangan' => $item['keterangan'],
                'status' => $item['status'],
                'created_at' => Carbon::parse($tanggal)->addHours(8),
                'updated_at' => Carbon::parse($tanggal)->addHours(8),
            ]);

            // Buat baris log persetujuan yang logis & konsisten
            if ($item['status'] === 'draft') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'draft',
                    'catatan' => 'Draf permohonan dibuat oleh staf.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
            } elseif ($item['status'] === 'diajukan') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'diajukan',
                    'catatan' => 'Pengajuan surat diajukan untuk ditinjau oleh staf Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
            } elseif ($item['status'] === 'diperiksa') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'diajukan',
                    'catatan' => 'Pengajuan surat diajukan untuk ditinjau oleh staf Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $petugas->id,
                    'status_lama' => 'diajukan',
                    'status_baru' => 'diperiksa',
                    'catatan' => 'Berkas administrasi siswa telah diverifikasi lengkap dan valid. Diteruskan ke Kepala Sekolah.',
                    'created_at' => Carbon::parse($tanggal)->addHours(11),
                ]);
            } elseif ($item['status'] === 'dikembalikan') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'diajukan',
                    'catatan' => 'Pengajuan surat diajukan untuk ditinjau oleh staf Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $petugas->id,
                    'status_lama' => 'diajukan',
                    'status_baru' => 'dikembalikan',
                    'catatan' => 'Mohon lengkapi fotokopi Kartu Keluarga dan surat pernyataan izin tertulis dari orang tua sebelum dilanjutkan.',
                    'created_at' => Carbon::parse($tanggal)->addHours(10),
                ]);
            } elseif ($item['status'] === 'disetujui') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'diajukan',
                    'catatan' => 'Pengajuan surat diajukan untuk ditinjau oleh staf Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $petugas->id,
                    'status_lama' => 'diajukan',
                    'status_baru' => 'diperiksa',
                    'catatan' => 'Pemeriksaan berkas siswa selesai, data valid.',
                    'created_at' => Carbon::parse($tanggal)->addHours(11),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $kepsek->id,
                    'status_lama' => 'diperiksa',
                    'status_baru' => 'disetujui',
                    'catatan' => 'Permohonan disetujui. Surat resmi dapat dicetak dan ditandatangani di loket Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(14),
                ]);
            } elseif ($item['status'] === 'ditolak') {
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $staf->id,
                    'status_lama' => null,
                    'status_baru' => 'diajukan',
                    'catatan' => 'Pengajuan surat diajukan untuk ditinjau oleh staf Tata Usaha.',
                    'created_at' => Carbon::parse($tanggal)->addHours(8),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $petugas->id,
                    'status_lama' => 'diajukan',
                    'status_baru' => 'diperiksa',
                    'catatan' => 'Pemeriksaan awal selesai, berkas dilanjutkan ke pimpinan.',
                    'created_at' => Carbon::parse($tanggal)->addHours(11),
                ]);
                Persetujuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $kepsek->id,
                    'status_lama' => 'diperiksa',
                    'status_baru' => 'ditolak',
                    'catatan' => 'Permohonan tidak dapat disetujui sesuai regulasi kurikulum dan peraturan tata tertib sekolah yang berlaku.',
                    'created_at' => Carbon::parse($tanggal)->addHours(15),
                ]);
            }
        }
    }
}
