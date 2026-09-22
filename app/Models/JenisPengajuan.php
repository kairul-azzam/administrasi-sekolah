<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPengajuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel non-standar yang digunakan model.
     */
    protected $table = 'jenis_pengajuan';

    /**
     * Kolom yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'nama_jenis',
        'deskripsi',
    ];

    /**
     * Relasi satu jenis pengajuan memiliki banyak pengajuan.
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'jenis_pengajuan_id');
    }
}
