<x-app-layout>
    <x-slot name="header">
        Ubah Pengajuan Surat
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-stone-900 tracking-tight">Ubah Berkas Pengajuan</h1>
                <p class="text-xs text-stone-500 mt-0.5">Perbarui rincian permohonan atau lengkapi catatan perbaikan.</p>
            </div>
            <a href="{{ route('pengajuan.show', $pengajuan) }}"
               class="text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                &larr; Batal & Kembali
            </a>
        </div>

        <!-- Banner Catatan Revisi jika status Dikembalikan -->
        @if($pengajuan->status === 'dikembalikan')
            @php
                $catatanTerakhir = $pengajuan->persetujuan()->where('status_baru', 'dikembalikan')->first();
            @endphp
            <div class="mb-6 p-4 rounded-xl bg-orange-50 border border-orange-200 text-xs text-orange-900">
                <div class="flex items-center gap-2 font-semibold text-orange-800">
                    <x-icon.exclamation-circle class="w-4 h-4 text-orange-600 flex-shrink-0" />
                    <span>Catatan Revisi dari Petugas Administrasi:</span>
                </div>
                <p class="mt-2 text-orange-950 italic pl-6 leading-relaxed">
                    "{{ $catatanTerakhir?->catatan ?? 'Harap tinjau kembali data dan keterangan pengajuan Anda.' }}"
                </p>
                <p class="mt-2 text-[11px] text-orange-700 pl-6">
                    Setelah memperbaiki isian di bawah, klik tombol <strong>"Ajukan Ulang Permohonan"</strong> agar berkas dapat diverifikasi kembali.
                </p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                <div class="flex items-center gap-2 font-semibold">
                    <x-icon.exclamation-circle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                    <span>Periksa kesalahan input berikut:</span>
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('pengajuan.update', $pengajuan) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Pilihan Siswa -->
                <div>
                    <label for="siswa_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Pilih Siswa <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id"
                            name="siswa_id"
                            required
                            class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                        @foreach($siswaList as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $pengajuan->siswa_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }} &bull; NIS: {{ $s->nis }} ({{ $s->kelas->kelas }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenis Pengajuan & Tanggal (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis_pengajuan_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Jenis Surat Administrasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="jenis_pengajuan_id"
                                name="jenis_pengajuan_id"
                                required
                                class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_pengajuan_id', $pengajuan->jenis_pengajuan_id) == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tanggal_pengajuan" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Tanggal Pengajuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date"
                               id="tanggal_pengajuan"
                               name="tanggal_pengajuan"
                               value="{{ old('tanggal_pengajuan', $pengajuan->tanggal_pengajuan->format('Y-m-d')) }}"
                               required
                               class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    </div>
                </div>

                <!-- Keterangan / Keperluan -->
                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Keterangan & Alasan Permohonan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="keterangan"
                              name="keterangan"
                              rows="4"
                              required
                              placeholder="Jelaskan tujuan permohonan surat secara rinci..."
                              class="w-full p-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">{{ old('keterangan', $pengajuan->keterangan) }}</textarea>
                </div>

                <!-- Dua Tombol Aksi -->
                <div class="pt-5 border-t border-stone-200 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                    <button type="submit"
                            name="aksi"
                            value="draft"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-medium text-stone-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 transition duration-150 cursor-pointer">
                        Simpan sebagai Draf
                    </button>

                    <button type="submit"
                            name="aksi"
                            value="diajukan"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150 cursor-pointer">
                        <x-icon.check class="w-4 h-4" />
                        <span>{{ $pengajuan->status === 'dikembalikan' ? 'Ajukan Ulang Permohonan' : 'Kirim & Ajukan Permohonan' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
