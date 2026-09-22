<x-app-layout>
    <x-slot name="header">
        Tambah Siswa
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-stone-900 tracking-tight">Tambah Data Siswa Baru</h1>
                <p class="text-xs text-stone-500 mt-0.5">Masukkan data pokok peserta didik dengan lengkap dan benar.</p>
            </div>
            <a href="{{ route('siswa.index') }}"
               class="text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                &larr; Batal & Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                <div class="flex items-center gap-2 font-semibold">
                    <x-icon.exclamation-circle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('siswa.store') }}" class="space-y-5">
                @csrf

                <!-- NIS -->
                <div>
                    <label for="nis" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="nis"
                           name="nis"
                           value="{{ old('nis') }}"
                           required
                           placeholder="Contoh: 2025001"
                           class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    <p class="text-[11px] text-stone-400 mt-1">NIS harus unik dan belum pernah didaftarkan.</p>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Siswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama') }}"
                           required
                           placeholder="Nama sesuai akta kelahiran..."
                           class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                </div>

                <!-- Kelas & Jenis Kelamin (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kelas_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Rombel Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select id="kelas_id"
                                name="kelas_id"
                                required
                                class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->kelas }} ({{ $k->tahun_ajaran }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="jenis_kelamin"
                                name="jenis_kelamin"
                                required
                                class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="laki" {{ old('jenis_kelamin') === 'laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Tanggal Lahir & Status (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_lahir" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="date"
                               id="tanggal_lahir"
                               name="tanggal_lahir"
                               value="{{ old('tanggal_lahir') }}"
                               required
                               class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                            Status Kesiswaan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                required
                                class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                        Alamat Tempat Tinggal <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="alamat"
                              name="alamat"
                              rows="3"
                              required
                              placeholder="Alamat jalan, RT/RW, kelurahan, kecamatan..."
                              class="w-full p-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">{{ old('alamat') }}</textarea>
                </div>

                <!-- Form Buttons -->
                <div class="pt-4 border-t border-stone-200 flex items-center justify-end gap-3">
                    <a href="{{ route('siswa.index') }}"
                       class="px-4 py-2 text-xs font-medium text-stone-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 transition duration-150">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150 cursor-pointer">
                        <x-icon.check class="w-4 h-4" />
                        <span>Simpan Data Siswa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
