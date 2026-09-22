<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    /**
     * Tentukan apakah user berwenang membuat permohonan pengajuan (hanya staf).
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStaf();
    }

    /**
     * Aturan validasi pembuatan pengajuan surat baru.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis_pengajuan_id' => ['required', 'exists:jenis_pengajuan,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tanggal_pengajuan' => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'aksi' => ['required', 'in:draft,diajukan'],
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
            'jenis_pengajuan_id.required' => 'Jenis pengajuan surat wajib dipilih.',
            'jenis_pengajuan_id.exists' => 'Jenis pengajuan surat tidak valid.',
            'siswa_id.required' => 'Siswa yang bersangkutan wajib dipilih.',
            'siswa_id.exists' => 'Data siswa yang dipilih tidak ditemukan.',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi.',
            'tanggal_pengajuan.date' => 'Format tanggal pengajuan tidak valid.',
            'keterangan.required' => 'Keterangan atau alasan permohonan wajib diisi.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 2000 karakter.',
            'aksi.required' => 'Tindakan pengajuan tidak valid.',
            'aksi.in' => 'Pilihan tindakan harus Simpan Draft atau Ajukan.',
        ];
    }
}
