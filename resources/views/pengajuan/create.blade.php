<x-app-layout>
    <x-slot name="header">
        Buat Pengajuan Surat
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-stone-900 tracking-tight">Formulir Pengajuan Surat Administrasi</h1>
                <p class="text-xs text-stone-500 mt-0.5">Lengkapi data di bawah ini untuk mengajukan permohonan surat siswa.</p>
            </div>
            <a href="{{ route('pengajuan.index') }}"
               class="text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                &larr; Batal & Kembali
            </a>
        </div>

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
            <form method="POST" action="{{ route('pengajuan.store') }}" class="space-y-5">
                @csrf

                <!-- Pemohon (Otomatis dari Auth) -->
                <div>
                    <label class="block text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">
                        Pemohon (Staf / Guru)
                    </label>
                    <div class="w-full h-10 px-3 flex items-center text-xs bg-stone-50 border border-stone-200 rounded-lg text-stone-600">
                        <span class="font-medium text-stone-900">{{ auth()->user()->name }}</span>
                        <span class="ml-2 text-stone-400">({{ auth()->user()->email }})</span>
                    </div>
                </div>

                <!-- Pilihan Siswa -->
                <div>
                    <label for="siswa_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Pilih Siswa <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id"
                            name="siswa_id"
                            required
                            class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                        <option value="">-- Cari dan Pilih Siswa --</option>
                        @foreach($siswaList as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
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
                            <option value="">-- Pilih Jenis Surat --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_pengajuan_id') == $j->id ? 'selected' : '' }}>
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
                               value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}"
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
                              placeholder="Jelaskan tujuan permohonan surat, keperluan dinas, instansi yang dituju, atau alasan lainnya secara rinci..."
                              class="w-full p-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">{{ old('keterangan') }}</textarea>
                </div>

                <!-- Dua Tombol Aksi: Simpan Draft atau Ajukan -->
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
                        <span>Kirim & Ajukan Permohonan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
