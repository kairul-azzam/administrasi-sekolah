<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting atribut model.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data pengajuan yang diajukan oleh user ini (pemohon).
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'user_id');
    }

    /**
     * Relasi ke catatan riwayat persetujuan/aksi yang dilakukan oleh user ini.
     */
    public function persetujuan(): HasMany
    {
        return $this->hasMany(Persetujuan::class, 'user_id');
    }

    /**
     * Helper untuk mengecek apakah user berperan sebagai Staf / Guru.
     */
    public function isStaf(): bool
    {
        return $this->role === 'staf';
    }

    /**
     * Helper untuk mengecek apakah user berperan sebagai Petugas Administrasi / TU.
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Helper untuk mengecek apakah user berperan sebagai Kepala Sekolah.
     */
    public function isKepsek(): bool
    {
        return $this->role === 'kepsek';
    }
}
