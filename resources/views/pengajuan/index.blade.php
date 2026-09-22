<x-app-layout>
    <x-slot name="header">
        Pengajuan Surat
    </x-slot>

    <!-- Page Header & Action Button -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                @if(auth()->user()->isStaf())
                    Pengajuan Surat Saya
                @elseif(auth()->user()->isPetugas())
                    Pemeriksaan & Verifikasi Pengajuan Surat
                @elseif(auth()->user()->isKepsek())
                    Persetujuan Pengajuan Surat
                @endif
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                @if(auth()->user()->isStaf())
                    Kelola dan pantau seluruh status surat administrasi yang Anda ajukan.
                @elseif(auth()->user()->isPetugas())
                    Periksa kelengkapan berkas yang masuk dari staf sebelum diteruskan ke pimpinan.
                @elseif(auth()->user()->isKepsek())
                    Tinjau berkas yang telah diverifikasi petugas untuk disetujui atau ditolak.
                @endif
            </p>
        </div>

        @if(auth()->user()->isStaf())
            <div>
                <a href="{{ route('pengajuan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150">
                    <x-icon.plus class="w-4 h-4" />
                    <span>Buat Pengajuan Baru</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('pengajuan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="lg:col-span-3">
                <label for="q" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Pencarian
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <x-icon.magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text"
                           id="q"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari Siswa, NIS, atau Keterangan..."
                           class="w-full h-10 pl-9 pr-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                </div>
            </div>

            <!-- Filter Status -->
            <div class="lg:col-span-2">
                <label for="status" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Status
                </label>
                <select id="status"
                        name="status"
                        class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    <option value="">Semua Status</option>
                    @if(auth()->user()->isKepsek())
                        <option value="diperiksa" {{ request('status') === 'diperiksa' ? 'selected' : '' }}>Diperiksa (Menunggu)</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    @else
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="diperiksa" {{ request('status') === 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                        <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    @endif
                </select>
            </div>

            <!-- Filter Jenis Surat -->
            <div class="lg:col-span-3">
                <label for="jenis_pengajuan_id" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Jenis Surat
                </label>
                <select id="jenis_pengajuan_id"
                        name="jenis_pengajuan_id"
                        class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    <option value="">Semua Jenis Surat</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j->id }}" {{ request('jenis_pengajuan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Rentang Tanggal: Awal -->
            <div class="lg:col-span-1 sm:col-span-1">
                <label for="tanggal_awal" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Tgl Awal
                </label>
                <input type="date"
                       id="tanggal_awal"
                       name="tanggal_awal"
                       value="{{ request('tanggal_awal') }}"
                       class="w-full h-10 px-2 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
            </div>

            <!-- Filter Rentang Tanggal: Akhir -->
            <div class="lg:col-span-1 sm:col-span-1">
                <label for="tanggal_akhir" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Tgl Akhir
                </label>
                <input type="date"
                       id="tanggal_akhir"
                       name="tanggal_akhir"
                       value="{{ request('tanggal_akhir') }}"
                       class="w-full h-10 px-2 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="lg:col-span-2 flex gap-1.5">
                <button type="submit"
                        title="Terapkan Filter"
                        class="flex-1 h-10 flex items-center justify-center gap-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium rounded-lg transition duration-150 cursor-pointer">
                    <x-icon.filter class="w-4 h-4" />
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['q', 'status', 'jenis_pengajuan_id', 'tanggal_awal', 'tanggal_akhir']))
                    <a href="{{ route('pengajuan.index') }}"
                       title="Reset Filter"
                       class="h-10 px-2.5 flex items-center justify-center bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-lg transition duration-150">
                        <x-icon.arrow-path class="w-3.5 h-3.5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pengajuan -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
        @if($pengajuan->isEmpty())
            <div class="p-12 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                    <x-icon.document class="w-5 h-5" />
                </div>
                <p class="text-xs text-stone-600 font-medium">Tidak ada berkas pengajuan yang ditemukan.</p>
                <p class="text-[11px] text-stone-400 mt-0.5">Silakan sesuaikan kriteria filter pencarian atau buat pengajuan baru.</p>
                @if(request()->anyFilled(['q', 'status', 'jenis_pengajuan_id', 'tanggal_awal', 'tanggal_akhir']))
                    <a href="{{ route('pengajuan.index') }}" class="inline-block mt-3 text-xs font-medium text-teal-700 hover:underline">
                        Reset semua filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tgl Pengajuan</th>
                            <th class="px-5 py-3 font-semibold">Jenis Surat</th>
                            <th class="px-5 py-3 font-semibold">Siswa & Rombel</th>
                            @if(!auth()->user()->isStaf())
                                <th class="px-5 py-3 font-semibold">Pemohon</th>
                            @endif
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach($pengajuan as $p)
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                    {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-stone-900">
                                    {{ $p->jenisPengajuan->nama_jenis }}
                                </td>
                                <td class="px-5 py-3.5 text-stone-700">
                                    <div class="font-medium text-stone-900">{{ $p->siswa->nama }}</div>
                                    <div class="text-[11px] text-stone-400 tabular-nums">
                                        {{ $p->siswa->kelas->kelas }} &bull; NIS: {{ $p->siswa->nis }}
                                    </div>
                                </td>
                                @if(!auth()->user()->isStaf())
                                    <td class="px-5 py-3.5 text-stone-700 whitespace-nowrap">
                                        <div class="font-medium text-stone-900">{{ $p->user->name }}</div>
                                        <div class="text-[10px] text-stone-400">{{ $p->user->email }}</div>
                                    </td>
                                @endif
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-badge-status :status="$p->status" />
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('pengajuan.show', $p) }}"
                                       class="inline-flex items-center text-xs font-semibold text-teal-700 hover:text-teal-900 transition">
                                        @if(auth()->user()->isPetugas() && $p->status === 'diajukan')
                                            Periksa &rarr;
                                        @elseif(auth()->user()->isKepsek() && $p->status === 'diperiksa')
                                            Putuskan &rarr;
                                        @else
                                            Detail &rarr;
                                        @endif
                                    </a>

                                    @if(auth()->user()->isStaf() && $p->canBeModifiedByStaf())
                                        <span class="text-stone-300">|</span>
                                        <a href="{{ route('pengajuan.edit', $p) }}"
                                           class="inline-flex items-center text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                                            Ubah
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="px-5 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $pengajuan->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
