<x-app-layout>
    <x-slot name="header">
        Data Siswa
    </x-slot>

    <!-- Page Header & Action Button -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                Data Kesiswaan
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Daftar seluruh siswa aktif dan terdaftar di sekolah.
            </p>
        </div>

        @if(auth()->user()->isPetugas())
            <div>
                <a href="{{ route('siswa.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150">
                    <x-icon.plus class="w-4 h-4" />
                    <span>Tambah Siswa Baru</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword (Nama / NIS) -->
            <div class="lg:col-span-4">
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
                           placeholder="Cari Nama atau NIS..."
                           class="w-full h-10 pl-9 pr-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                </div>
            </div>

            <!-- Filter Kelas -->
            <div class="lg:col-span-3">
                <label for="kelas_id" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Kelas
                </label>
                <select id="kelas_id"
                        name="kelas_id"
                        class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->kelas }} ({{ $k->tahun_ajaran }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Kelamin -->
            <div class="lg:col-span-2">
                <label for="jenis_kelamin" class="block text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-1">
                    Jenis Kelamin
                </label>
                <select id="jenis_kelamin"
                        name="jenis_kelamin"
                        class="w-full h-10 px-3 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150">
                    <option value="">Semua</option>
                    <option value="laki" {{ request('jenis_kelamin') === 'laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
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
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="lg:col-span-1 flex gap-1.5">
                <button type="submit"
                        title="Terapkan Filter"
                        class="w-full h-10 flex items-center justify-center bg-stone-900 hover:bg-stone-800 text-white rounded-lg transition duration-150 cursor-pointer">
                    <x-icon.filter class="w-4 h-4" />
                </button>
                @if(request()->anyFilled(['q', 'kelas_id', 'jenis_kelamin', 'status']))
                    <a href="{{ route('siswa.index') }}"
                       title="Reset Filter"
                       class="h-10 px-2.5 flex items-center justify-center bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-lg transition duration-150">
                        <x-icon.arrow-path class="w-3.5 h-3.5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Siswa -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-xs overflow-hidden">
        @if($siswa->isEmpty())
            <div class="p-12 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                    <x-icon.users class="w-5 h-5" />
                </div>
                <p class="text-xs text-stone-600 font-medium">Tidak ada data siswa yang sesuai.</p>
                <p class="text-[11px] text-stone-400 mt-0.5">Coba ubah kata kunci pencarian atau sesuaikan filter Anda.</p>
                @if(request()->anyFilled(['q', 'kelas_id', 'jenis_kelamin', 'status']))
                    <a href="{{ route('siswa.index') }}" class="inline-block mt-3 text-xs font-medium text-teal-700 hover:underline">
                        Reset semua filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50/75 border-b border-stone-200 text-stone-500 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">NIS</th>
                            <th class="px-5 py-3 font-semibold">Nama Siswa</th>
                            <th class="px-5 py-3 font-semibold">Kelas</th>
                            <th class="px-5 py-3 font-semibold">L/P</th>
                            <th class="px-5 py-3 font-semibold">Tanggal Lahir</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach($siswa as $s)
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="px-5 py-3.5 font-mono text-stone-700 tabular-nums whitespace-nowrap">
                                    {{ $s->nis }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-stone-900 whitespace-nowrap">
                                    {{ $s->nama }}
                                </td>
                                <td class="px-5 py-3.5 text-stone-600 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-stone-100 text-stone-700 text-[11px] font-medium">
                                        {{ $s->kelas->kelas }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-stone-600 whitespace-nowrap">
                                    {{ $s->jenis_kelamin === 'laki' ? 'L' : 'P' }}
                                </td>
                                <td class="px-5 py-3.5 text-stone-600 tabular-nums whitespace-nowrap">
                                    {{ $s->tanggal_lahir->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($s->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                                            <span>Nonaktif</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('siswa.show', $s) }}"
                                       class="inline-flex items-center text-xs font-semibold text-stone-700 hover:text-stone-900 transition">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isPetugas())
                                        <span class="text-stone-300">|</span>
                                        <a href="{{ route('siswa.edit', $s) }}"
                                           class="inline-flex items-center text-xs font-semibold text-teal-700 hover:text-teal-900 transition">
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
                {{ $siswa->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
