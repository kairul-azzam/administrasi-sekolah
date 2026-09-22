<x-app-layout>
    <x-slot name="header">
        Detail Siswa
    </x-slot>

    <!-- Back Navigation & Actions -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('siswa.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
            &larr; Kembali ke Daftar Siswa
        </a>

        @if(auth()->user()->isPetugas())
            <a href="{{ route('siswa.edit', $siswa) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150">
                <x-icon.pencil class="w-3.5 h-3.5" />
                <span>Ubah Data Siswa</span>
            </a>
        @endif
    </div>

    <!-- Student Detail Card -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-stone-200 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-stone-100 text-stone-700 flex items-center justify-center text-lg font-semibold border border-stone-200">
                    {{ substr($siswa->nama, 0, 2) }}
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-stone-900">{{ $siswa->nama }}</h1>
                    <div class="flex items-center gap-2 mt-1 text-xs text-stone-500">
                        <span class="font-mono tabular-nums font-medium text-stone-700">NIS: {{ $siswa->nis }}</span>
                        <span>&bull;</span>
                        <span class="font-medium text-stone-700">{{ $siswa->kelas->kelas }} (TA {{ $siswa->kelas->tahun_ajaran }})</span>
                    </div>
                </div>
            </div>

            <div>
                @if($siswa->status === 'aktif')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Siswa Aktif</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-stone-100 text-stone-600 border border-stone-200">
                        <span class="w-2 h-2 rounded-full bg-stone-400"></span>
                        <span>Siswa Nonaktif</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-6">
            <div>
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider">Jenis Kelamin</span>
                <span class="mt-1 block text-sm font-medium text-stone-900">
                    {{ $siswa->jenis_kelamin === 'laki' ? 'Laki-laki' : 'Perempuan' }}
                </span>
            </div>

            <div>
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider">Tanggal Lahir</span>
                <span class="mt-1 block text-sm font-medium text-stone-900 tabular-nums">
                    {{ $siswa->tanggal_lahir->translatedFormat('d F Y') }}
                    <span class="text-xs text-stone-500 font-normal">({{ $siswa->tanggal_lahir->age }} tahun)</span>
                </span>
            </div>

            <div>
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider">Rombel Kelas</span>
                <span class="mt-1 block text-sm font-medium text-stone-900">
                    {{ $siswa->kelas->kelas }}
                </span>
            </div>

            <div>
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider">Tahun Ajaran</span>
                <span class="mt-1 block text-sm font-medium text-stone-900">
                    {{ $siswa->kelas->tahun_ajaran }}
                </span>
            </div>

            <div class="sm:col-span-2 lg:col-span-4">
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider">Alamat Lengkap</span>
                <p class="mt-1 text-sm text-stone-800 leading-relaxed">
                    {{ $siswa->alamat }}
                </p>
            </div>
        </div>
    </div>

    <!-- Student Administration Requests History -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-200">
            <h2 class="text-sm font-semibold text-stone-900">Riwayat Pengajuan Surat Administrasi Siswa</h2>
            <p class="text-xs text-stone-500 mt-0.5">Daftar semua permohonan surat yang diajukan untuk siswa ini.</p>
        </div>

        @if($siswa->pengajuan->isEmpty())
            <div class="p-10 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                    <x-icon.document class="w-5 h-5" />
                </div>
                <p class="text-xs text-stone-600 font-medium">Belum ada pengajuan surat untuk siswa ini.</p>
                <p class="text-[11px] text-stone-400 mt-0.5">Seluruh permohonan surat baru akan tercatat di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal Pengajuan</th>
                            <th class="px-5 py-3 font-semibold">Jenis Surat</th>
                            <th class="px-5 py-3 font-semibold">Pemohon</th>
                            <th class="px-5 py-3 font-semibold">Status Terakhir</th>
                            <th class="px-5 py-3 font-semibold text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach($siswa->pengajuan as $p)
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                    {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-stone-900">
                                    {{ $p->jenisPengajuan->nama_jenis }}
                                </td>
                                <td class="px-5 py-3.5 text-stone-700 whitespace-nowrap">
                                    {{ $p->user->name }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-badge-status :status="$p->status" />
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('pengajuan.show', $p) }}"
                                       class="text-xs font-semibold text-teal-700 hover:text-teal-900 transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
