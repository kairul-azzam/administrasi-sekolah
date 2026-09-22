<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends FormRequest
{
    /**
     * Tentukan apakah user berwenang membuat request ini (petugas).
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPetugas();
    }

    /**
     * Aturan validasi pembaruan data siswa.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $siswaId = $this->route('siswa') ? $this->route('siswa')->id : null;

        return [
            'kelas_id' => ['required', 'exists:kelas,id'],
            'nis' => [
                'required',
                'string',
                'max:20',
                Rule::unique('siswa', 'nis')->ignore($siswaId),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', 'in:laki,perempuan'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'alamat' => ['required', 'string', 'max:500'],
            'status' => ['required', 'in:aktif,nonaktif'],
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
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar untuk siswa lain.',
            'nis.max' => 'NIS tidak boleh lebih dari 20 karakter.',
            'nama.required' => 'Nama lengkap siswa wajib diisi.',
            'nama.max' => 'Nama lengkap tidak boleh lebih dari 150 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'alamat.required' => 'Alamat tempat tinggal wajib diisi.',
            'alamat.max' => 'Alamat tidak boleh lebih dari 500 karakter.',
            'status.required' => 'Status kesiswaan wajib dipilih.',
            'status.in' => 'Pilihan status tidak valid.',
        ];
    }
}
