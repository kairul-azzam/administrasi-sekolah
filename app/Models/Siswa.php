<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel non-standar yang digunakan model.
     */
    protected $table = 'siswa';

    /**
     * Kolom yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'kelas_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'status',
    ];

    /**
     * Casting format atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Relasi siswa dimiliki oleh satu kelas (belongsTo).
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi satu siswa dapat memiliki banyak riwayat pengajuan surat.
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'siswa_id');
    }
}
