<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeputusanPengajuanRequest extends FormRequest
{
    /**
     * Tentukan apakah user berwenang memutuskan permohonan surat (hanya kepsek).
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isKepsek();
    }

    /**
     * Aturan validasi keputusan pengajuan oleh Kepala Sekolah.
     * Catatan WAJIB diisi saat aksi adalah 'tolak'.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'aksi' => ['required', 'in:setujui,tolak'],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:aksi,tolak',
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
            'aksi.required' => 'Keputusan akhir wajib ditentukan.',
            'aksi.in' => 'Keputusan harus Setujui atau Tolak.',
            'catatan.required_if' => 'Alasan penolakan permohonan wajib diisi dengan jelas.',
            'catatan.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
