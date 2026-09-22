<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persetujuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel non-standar yang digunakan model.
     */
    protected $table = 'persetujuan';

    /**
     * Kolom yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'status_lama',
        'status_baru',
        'catatan',
    ];

    /**
     * Relasi ke pengajuan yang bersangkutan.
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    /**
     * Relasi ke user pelaku yang melakukan tindakan / perubahan status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
