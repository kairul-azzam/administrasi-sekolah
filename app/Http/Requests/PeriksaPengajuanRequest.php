<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriksaPengajuanRequest extends FormRequest
{
    /**
     * Tentukan apakah user berwenang memeriksa pengajuan (hanya petugas).
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPetugas();
    }

    /**
     * Aturan validasi pemeriksaan pengajuan oleh petugas administrasi.
     * Catatan WAJIB diisi saat aksi adalah 'kembalikan'.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'aksi' => ['required', 'in:periksa,kembalikan'],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:aksi,kembalikan',
            ],
        ];
    }

    /**
     * Pesan kustom validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aksi.required' => 'Tindakan pemeriksaan wajib dipilih.',
            'aksi.in' => 'Tindakan harus Berkas Valid (Teruskan) atau Kembalikan untuk Revisi.',
            'catatan.required_if' => 'Catatan alasan pengembalian wajib diisi agar staf dapat mengetahui perbaikan yang diperlukan.',
            'catatan.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
