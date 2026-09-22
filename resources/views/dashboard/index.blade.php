<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <!-- Welcome Greeting Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                Halo, {{ $user->name }}
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Selamat datang di portal administrasi persuratan dan kesiswaan sekolah.
            </p>
        </div>

        @if($role === 'staf')
            <div>
                <a href="{{ route('pengajuan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150">
                    <x-icon.plus class="w-4 h-4" />
                    <span>Buat Pengajuan Surat</span>
                </a>
            </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- 1. DASHBOARD ROLE: STAF                                  --}}
    {{-- ======================================================== --}}
    @if($role === 'staf')
        <!-- Status Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-8">
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs">
                <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Draft</span>
                <span class="text-2xl font-semibold text-stone-800 tabular-nums mt-1 block">{{ $statusCounts['draft'] }}</span>
                <span class="text-[10px] text-stone-400 mt-0.5 block">Belum diajukan</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-amber-200/80 bg-amber-50/20 shadow-xs">
                <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Diajukan</span>
                <span class="text-2xl font-semibold text-amber-900 tabular-nums mt-1 block">{{ $statusCounts['diajukan'] }}</span>
                <span class="text-[10px] text-amber-600 mt-0.5 block">Menunggu verifikasi</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-blue-200/80 bg-blue-50/20 shadow-xs">
                <span class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider block">Diperiksa</span>
                <span class="text-2xl font-semibold text-blue-900 tabular-nums mt-1 block">{{ $statusCounts['diperiksa'] }}</span>
                <span class="text-[10px] text-blue-600 mt-0.5 block">Menunggu persetujuan</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-orange-200/80 bg-orange-50/20 shadow-xs">
                <span class="text-[11px] font-semibold text-orange-700 uppercase tracking-wider block">Dikembalikan</span>
                <span class="text-2xl font-semibold text-orange-900 tabular-nums mt-1 block">{{ $statusCounts['dikembalikan'] }}</span>
                <span class="text-[10px] text-orange-600 mt-0.5 block">Perlu revisi staf</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Disetujui</span>
                <span class="text-2xl font-semibold text-emerald-900 tabular-nums mt-1 block">{{ $statusCounts['disetujui'] }}</span>
                <span class="text-[10px] text-emerald-600 mt-0.5 block">Selesai & diterbitkan</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-rose-200/80 bg-rose-50/20 shadow-xs">
                <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Ditolak</span>
                <span class="text-2xl font-semibold text-rose-900 tabular-nums mt-1 block">{{ $statusCounts['ditolak'] }}</span>
                <span class="text-[10px] text-rose-600 mt-0.5 block">Tidak disetujui</span>
            </div>
        </div>

        <!-- 5 Pengajuan Terbaru Milik Sendiri -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">5 Pengajuan Terbaru Anda</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Daftar berkas terakhir yang Anda buat atau ajukan.</p>
                </div>
                <a href="{{ route('pengajuan.index') }}" class="text-xs font-medium text-teal-700 hover:text-teal-800 transition">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($pengajuanTerbaru->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-10 h-10 mx-auto rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                        <x-icon.document class="w-5 h-5" />
                    </div>
                    <p class="text-xs text-stone-500">Belum ada pengajuan surat yang dibuat.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 font-semibold">Jenis Surat</th>
                                <th class="px-5 py-3 font-semibold">Siswa</th>
                                <th class="px-5 py-3 font-semibold">Kelas</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach($pengajuanTerbaru as $p)
                                <tr class="hover:bg-stone-50/60 transition duration-150">
                                    <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                        {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 font-medium text-stone-900">
                                        {{ $p->jenisPengajuan->nama_jenis }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-700">
                                        {{ $p->siswa->nama }}
                                        <span class="block text-[11px] text-stone-400 tabular-nums">NIS: {{ $p->siswa->nis }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-600 whitespace-nowrap">
                                        {{ $p->siswa->kelas->kelas }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <x-badge-status :status="$p->status" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <a href="{{ route('pengajuan.show', $p) }}"
                                           class="inline-flex items-center text-xs font-semibold text-teal-700 hover:text-teal-900 transition">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    {{-- ======================================================== --}}
    {{-- 2. DASHBOARD ROLE: PETUGAS                               --}}
    {{-- ======================================================== --}}
    @elseif($role === 'petugas')
        <!-- Summary Cards Petugas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Siswa Aktif</span>
                    <span class="p-2 rounded-lg bg-teal-50 text-teal-700">
                        <x-icon.users class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-stone-900 tabular-nums">{{ $totalSiswaAktif }}</span>
                    <span class="text-xs text-stone-500">siswa</span>
                </div>
                <span class="text-[11px] text-stone-400 mt-1 block">Dari total {{ $totalKelas }} rombel kelas</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Perlu Diperiksa</span>
                    <span class="p-2 rounded-lg bg-amber-100 text-amber-800">
                        <x-icon.clock class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-amber-900 tabular-nums">{{ $menungguPemeriksaan }}</span>
                    <span class="text-xs text-amber-700">berkas</span>
                </div>
                <span class="text-[11px] text-amber-700/80 mt-1 block">Menunggu verifikasi Anda</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Total Rombel</span>
                    <span class="p-2 rounded-lg bg-stone-100 text-stone-700">
                        <x-icon.academic-cap class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-stone-900 tabular-nums">{{ $totalKelas }}</span>
                    <span class="text-xs text-stone-500">kelas</span>
                </div>
                <span class="text-[11px] text-stone-400 mt-1 block">Tahun ajaran aktif</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Total Pengajuan</span>
                    <span class="p-2 rounded-lg bg-stone-100 text-stone-700">
                        <x-icon.document class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-stone-900 tabular-nums">{{ $totalPengajuan }}</span>
                    <span class="text-xs text-stone-500">berkas</span>
                </div>
                <span class="text-[11px] text-stone-400 mt-1 block">Seluruh riwayat pengajuan</span>
            </div>
        </div>

        <!-- Antrean Pengajuan Menunggu Pemeriksaan Petugas -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Antrean Berkas Pengajuan Menunggu Pemeriksaan</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Berkas berstatus "Diajukan" yang siap ditinjau dan diteruskan atau dikembalikan.</p>
                </div>
                <a href="{{ route('pengajuan.index', ['status' => 'diajukan']) }}" class="text-xs font-medium text-teal-700 hover:text-teal-800 transition">
                    Lihat Semua Antrean &rarr;
                </a>
            </div>

            @if($pengajuanPeriksa->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                        <x-icon.check class="w-5 h-5" />
                    </div>
                    <p class="text-xs text-stone-600 font-medium">Semua berkas telah selesai diperiksa!</p>
                    <p class="text-[11px] text-stone-400 mt-0.5">Tidak ada pengajuan yang sedang menunggu pemeriksaan saat ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 font-semibold">Pemohon (Staf)</th>
                                <th class="px-5 py-3 font-semibold">Jenis Surat</th>
                                <th class="px-5 py-3 font-semibold">Siswa & Kelas</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach($pengajuanPeriksa as $p)
                                <tr class="hover:bg-stone-50/60 transition duration-150">
                                    <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                        {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-900 font-medium whitespace-nowrap">
                                        {{ $p->user->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-800">
                                        {{ $p->jenisPengajuan->nama_jenis }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-700">
                                        {{ $p->siswa->nama }}
                                        <span class="block text-[11px] text-stone-400">{{ $p->siswa->kelas->kelas }} (NIS: {{ $p->siswa->nis }})</span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <x-badge-status :status="$p->status" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <a href="{{ route('pengajuan.show', $p) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white font-medium rounded-lg text-xs transition duration-150">
                                            <span>Periksa</span>
                                            <x-icon.arrow-right class="w-3.5 h-3.5" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    {{-- ======================================================== --}}
    {{-- 3. DASHBOARD ROLE: KEPSEK                                --}}
    {{-- ======================================================== --}}
    @elseif($role === 'kepsek')
        <!-- Summary Cards Kepsek -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-blue-200 bg-blue-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Menunggu Keputusan</span>
                    <span class="p-2 rounded-lg bg-blue-100 text-blue-800">
                        <x-icon.clock class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-blue-900 tabular-nums">{{ $menungguKeputusan }}</span>
                    <span class="text-xs text-blue-700">berkas</span>
                </div>
                <span class="text-[11px] text-blue-700/80 mt-1 block">Telah diverifikasi oleh Petugas</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Disetujui</span>
                    <span class="p-2 rounded-lg bg-emerald-100 text-emerald-800">
                        <x-icon.check class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-emerald-900 tabular-nums">{{ $totalDisetujui }}</span>
                    <span class="text-xs text-emerald-700">berkas</span>
                </div>
                <span class="text-[11px] text-emerald-700/80 mt-1 block">Telah disetujui & disahkan</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-rose-200 bg-rose-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Ditolak</span>
                    <span class="p-2 rounded-lg bg-rose-100 text-rose-800">
                        <x-icon.x-mark class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-rose-900 tabular-nums">{{ $totalDitolak }}</span>
                    <span class="text-xs text-rose-700">berkas</span>
                </div>
                <span class="text-[11px] text-rose-700/80 mt-1 block">Permohonan tidak dikabulkan</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Total Siswa Aktif</span>
                    <span class="p-2 rounded-lg bg-teal-50 text-teal-700">
                        <x-icon.users class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-stone-900 tabular-nums">{{ $totalSiswaAktif }}</span>
                    <span class="text-xs text-stone-500">siswa</span>
                </div>
                <span class="text-[11px] text-stone-400 mt-1 block">Terdaftar resmi di sekolah</span>
            </div>
        </div>

        <!-- Antrean Pengajuan Menunggu Keputusan Kepsek -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Antrean Persetujuan Kepala Sekolah</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Berkas berstatus "Diperiksa" yang memerlukan tanda tangan / persetujuan pimpinan.</p>
                </div>
                <a href="{{ route('pengajuan.index', ['status' => 'diperiksa']) }}" class="text-xs font-medium text-teal-700 hover:text-teal-800 transition">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($antreanKeputusan->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-10 h-10 mx-auto rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                        <x-icon.check class="w-5 h-5" />
                    </div>
                    <p class="text-xs text-stone-600 font-medium">Tidak ada berkas yang menunggu keputusan.</p>
                    <p class="text-[11px] text-stone-400 mt-0.5">Seluruh berkas yang masuk telah diputuskan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 font-semibold">Pemohon</th>
                                <th class="px-5 py-3 font-semibold">Jenis Surat</th>
                                <th class="px-5 py-3 font-semibold">Siswa & Kelas</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach($antreanKeputusan as $p)
                                <tr class="hover:bg-stone-50/60 transition duration-150">
                                    <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                        {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-900 font-medium whitespace-nowrap">
                                        {{ $p->user->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-800">
                                        {{ $p->jenisPengajuan->nama_jenis }}
                                    </td>
                                    <td class="px-5 py-3.5 text-stone-700">
                                        {{ $p->siswa->nama }}
                                        <span class="block text-[11px] text-stone-400">{{ $p->siswa->kelas->kelas }} (NIS: {{ $p->siswa->nis }})</span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <x-badge-status :status="$p->status" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <a href="{{ route('pengajuan.show', $p) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white font-medium rounded-lg text-xs transition duration-150">
                                            <span>Beri Keputusan</span>
                                            <x-icon.arrow-right class="w-3.5 h-3.5" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
