<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel non-standar yang digunakan model.
     */
    protected $table = 'pengajuan';

    /**
     * Kolom yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'user_id',
        'jenis_pengajuan_id',
        'siswa_id',
        'tanggal_pengajuan',
        'keterangan',
        'status',
    ];

    /**
     * Casting format atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    /**
     * Relasi ke user yang mengajukan permohonan (pemohon).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke master data jenis pengajuan surat.
     */
    public function jenisPengajuan(): BelongsTo
    {
        return $this->belongsTo(JenisPengajuan::class, 'jenis_pengajuan_id');
    }

    /**
     * Relasi ke data siswa yang diajukan.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke catatan log riwayat persetujuan berurutan dari yang terbaru.
     */
    public function persetujuan(): HasMany
    {
        return $this->hasMany(Persetujuan::class, 'pengajuan_id')->latest();
    }

    /**
     * Method inti untuk mengubah status pengajuan.
     * Wajib digunakan pada seluruh alur proses status.
     * Menjalankan update status pengajuan + insert riwayat persetujuan dalam satu DB::transaction.
     *
     * @param string $statusBaru ('draft', 'diajukan', 'diperiksa', 'dikembalikan', 'disetujui', 'ditolak')
     * @param string|null $catatan Catatan alasan / revisi
     * @param int|null $userId ID user pelaku perubahan status (default: user login)
     * @return self
     */
    public function ubahStatus(string $statusBaru, ?string $catatan = null, ?int $userId = null): self
    {
        return DB::transaction(function () use ($statusBaru, $catatan, $userId) {
            $statusLama = $this->status;

            // 1. Perbarui kolom status pengajuan
            $this->update([
                'status' => $statusBaru,
            ]);

            // 2. Catat riwayat log ke tabel persetujuan
            $this->persetujuan()->create([
                'user_id' => $userId ?? auth()->id(),
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
            ]);

            return $this;
        });
    }

    /**
     * Cek apakah pengajuan masih boleh diedit atau dibatalkan (dihapus) oleh staf pemohon.
     * Hanya saat status: 'draft', 'diajukan', atau 'dikembalikan'.
     */
    public function canBeModifiedByStaf(): bool
    {
        return in_array($this->status, ['draft', 'diajukan', 'dikembalikan'], true);
    }
}
