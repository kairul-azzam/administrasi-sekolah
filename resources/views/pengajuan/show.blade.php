<x-app-layout>
    <x-slot name="header">
        Detail Pengajuan Surat
    </x-slot>

    <!-- Top Navigation Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('pengajuan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
            &larr; Kembali ke Daftar Pengajuan
        </a>

        <!-- Staf Action Buttons: Edit or Delete (Only for draft, diajukan, dikembalikan) -->
        @if(auth()->user()->isStaf() && $pengajuan->user_id === auth()->id() && $pengajuan->canBeModifiedByStaf())
            <div class="flex items-center gap-2">
                <a href="{{ route('pengajuan.edit', $pengajuan) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-stone-50 text-stone-700 text-xs font-semibold rounded-lg border border-stone-200 transition duration-150">
                    <x-icon.pencil class="w-3.5 h-3.5" />
                    <span>Ubah Pengajuan</span>
                </a>

                <button type="button"
                        onclick="openConfirmModal('confirm-modal', '{{ route('pengajuan.destroy', $pengajuan) }}', 'Batalkan & Hapus Pengajuan', 'Apakah Anda yakin ingin membatalkan pengajuan surat ini? Berkas dan riwayat persetujuannya akan dihapus secara permanen.')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg border border-rose-200 transition duration-150 cursor-pointer">
                    <x-icon.trash class="w-3.5 h-3.5" />
                    <span>Batalkan Pengajuan</span>
                </button>
            </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
            <div class="flex items-center gap-2 font-semibold">
                <x-icon.exclamation-circle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                <span>Terjadi kesalahan pada tindakan Anda:</span>
            </div>
            <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- ======================================================== -->
        <!-- KOLOM KIRI (2/3): INFORMASI BERKAS & PANEL AKSI ROLE      -->
        <!-- ======================================================== -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Pokok Pengajuan -->
            <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6">
                <!-- Header Kartu -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-stone-200 gap-3">
                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Jenis Surat</span>
                        <h1 class="text-xl font-semibold text-stone-900 mt-0.5">{{ $pengajuan->jenisPengajuan->nama_jenis }}</h1>
                    </div>
                    <div>
                        <x-badge-status :status="$pengajuan->status" class="text-xs px-3 py-1" />
                    </div>
                </div>

                <!-- Rincian Data Pengajuan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 py-5 border-b border-stone-200 text-xs">
                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Tanggal Pengajuan</span>
                        <span class="mt-1 block font-medium text-stone-900 tabular-nums">
                            {{ $pengajuan->tanggal_pengajuan->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Pemohon (Staf / Guru)</span>
                        <span class="mt-1 block font-medium text-stone-900">
                            {{ $pengajuan->user->name }}
                            <span class="text-stone-400 font-normal">({{ $pengajuan->user->email }})</span>
                        </span>
                    </div>

                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Nama Siswa Terkait</span>
                        <span class="mt-1 block font-medium text-stone-900">
                            <a href="{{ route('siswa.show', $pengajuan->siswa) }}" class="text-teal-700 hover:underline">
                                {{ $pengajuan->siswa->nama }}
                            </a>
                        </span>
                    </div>

                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">NIS & Kelas Siswa</span>
                        <span class="mt-1 block font-medium text-stone-900 tabular-nums">
                            NIS: {{ $pengajuan->siswa->nis }} &bull; {{ $pengajuan->siswa->kelas->kelas }}
                        </span>
                    </div>
                </div>

                <!-- Keterangan & Alasan Pengajuan -->
                <div class="pt-5">
                    <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block mb-2">
                        Keterangan & Alasan Permohonan
                    </span>
                    <div class="p-4 rounded-lg bg-stone-50 border border-stone-200 text-xs text-stone-800 leading-relaxed whitespace-pre-line">
                        {{ $pengajuan->keterangan }}
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PANEL AKSI: PETUGAS ADMINISTRASI (Saat status DIAJUKAN)   -->
            <!-- ======================================================== -->
            @if(auth()->user()->isPetugas() && $pengajuan->status === 'diajukan')
                <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-stone-200">
                        <span class="p-1.5 rounded-lg bg-teal-50 text-teal-700">
                            <x-icon.document-check class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-stone-900">Pemeriksaan Berkas Petugas</h2>
                            <p class="text-[11px] text-stone-500">Tentukan apakah berkas sudah valid atau perlu dikembalikan untuk direvisi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Opsi 1: Teruskan ke Kepala Sekolah -->
                        <div class="p-4 rounded-lg border border-teal-200 bg-teal-50/20 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                                    <x-icon.check-circle class="w-4 h-4 text-teal-700" />
                                    <span>Berkas Lengkap & Valid</span>
                                </div>
                                <p class="text-[11px] text-teal-700/90 mt-1 leading-relaxed">
                                    Meneruskan pengajuan ini ke Kepala Sekolah untuk mendapatkan persetujuan resmi.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('pengajuan.periksa', $pengajuan) }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="aksi" value="periksa">
                                <input type="hidden" name="catatan" value="Berkas telah diverifikasi lengkap dan valid oleh petugas administrasi. Diteruskan ke Kepala Sekolah.">
                                <button type="submit"
                                        class="w-full h-9 flex items-center justify-center gap-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium rounded-lg shadow-xs transition duration-150 cursor-pointer">
                                    <x-icon.check class="w-3.5 h-3.5" />
                                    <span>Teruskan ke Kepala Sekolah</span>
                                </button>
                            </form>
                        </div>

                        <!-- Opsi 2: Kembalikan untuk Revisi (Catatan Wajib) -->
                        <div class="p-4 rounded-lg border border-orange-200 bg-orange-50/20">
                            <div class="flex items-center gap-2 text-xs font-semibold text-orange-900 mb-1">
                                <x-icon.arrow-path class="w-4 h-4 text-orange-600" />
                                <span>Kembalikan untuk Revisi</span>
                            </div>
                            <p class="text-[11px] text-orange-700/90 mb-3 leading-relaxed">
                                Berikan catatan perbaikan yang wajib dipenuhi staf sebelum diajukan kembali.
                            </p>

                            <form method="POST" action="{{ route('pengajuan.periksa', $pengajuan) }}" class="space-y-2.5">
                                @csrf
                                <input type="hidden" name="aksi" value="kembalikan">
                                <div>
                                    <textarea name="catatan"
                                              rows="2"
                                              required
                                              placeholder="Tuliskan catatan revisi wajib di sini..."
                                              class="w-full p-2.5 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full h-9 flex items-center justify-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg shadow-xs transition duration-150 cursor-pointer">
                                    <x-icon.arrow-path class="w-3.5 h-3.5" />
                                    <span>Kembalikan ke Staf</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ======================================================== -->
            <!-- PANEL AKSI: KEPALA SEKOLAH (Saat status DIPERIKSA)       -->
            <!-- ======================================================== -->
            @if(auth()->user()->isKepsek() && $pengajuan->status === 'diperiksa')
                <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-stone-200">
                        <span class="p-1.5 rounded-lg bg-purple-50 text-purple-700">
                            <x-icon.academic-cap class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-stone-900">Keputusan Kepala Sekolah</h2>
                            <p class="text-[11px] text-stone-500">Tentukan persetujuan resmi atau penolakan permohonan surat ini.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Opsi 1: Setujui -->
                        <div class="p-4 rounded-lg border border-emerald-200 bg-emerald-50/20 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-900">
                                    <x-icon.check-circle class="w-4 h-4 text-emerald-700" />
                                    <span>Setujui Permohonan</span>
                                </div>
                                <p class="text-[11px] text-emerald-700/90 mt-1 leading-relaxed">
                                    Mengesahkan permohonan surat sehingga dokumen dapat dicetak dan diterbitkan.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('pengajuan.keputusan', $pengajuan) }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="aksi" value="setujui">
                                <input type="hidden" name="catatan" value="Permohonan telah disetujui secara resmi oleh Kepala Sekolah.">
                                <button type="submit"
                                        class="w-full h-9 flex items-center justify-center gap-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-medium rounded-lg shadow-xs transition duration-150 cursor-pointer">
                                    <x-icon.check class="w-3.5 h-3.5" />
                                    <span>Setujui & Sahkan Surat</span>
                                </button>
                            </form>
                        </div>

                        <!-- Opsi 2: Tolak (Catatan Wajib) -->
                        <div class="p-4 rounded-lg border border-rose-200 bg-rose-50/20">
                            <div class="flex items-center gap-2 text-xs font-semibold text-rose-900 mb-1">
                                <x-icon.x-circle class="w-4 h-4 text-rose-600" />
                                <span>Tolak Permohonan</span>
                            </div>
                            <p class="text-[11px] text-rose-700/90 mb-3 leading-relaxed">
                                Berikan alasan penolakan yang wajib diinformasikan kepada pemohon.
                            </p>

                            <form method="POST" action="{{ route('pengajuan.keputusan', $pengajuan) }}" class="space-y-2.5">
                                @csrf
                                <input type="hidden" name="aksi" value="tolak">
                                <div>
                                    <textarea name="catatan"
                                              rows="2"
                                              required
                                              placeholder="Tuliskan alasan penolakan wajib di sini..."
                                              class="w-full p-2.5 text-xs bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full h-9 flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium rounded-lg shadow-xs transition duration-150 cursor-pointer">
                                    <x-icon.x-mark class="w-3.5 h-3.5" />
                                    <span>Tolak Permohonan Ini</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- KOLOM KANAN (1/3): VERTICAL TIMELINE RIWAYAT STATUS       -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-xs p-6">
            <div class="flex items-center gap-2 pb-4 mb-4 border-b border-stone-200">
                <span class="p-1 rounded bg-stone-100 text-stone-600">
                    <x-icon.clock class="w-4 h-4" />
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Riwayat Status</h2>
                    <p class="text-[10px] text-stone-400">Jejak audit dan alur persetujuan berkas</p>
                </div>
            </div>

            <!-- Vertical Timeline Container -->
            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-stone-200">
                @forelse($pengajuan->persetujuan as $log)
                    @php
                        $dotColor = match($log->status_baru) {
                            'draft' => 'bg-stone-400 ring-stone-100',
                            'diajukan' => 'bg-amber-500 ring-amber-100',
                            'diperiksa' => 'bg-blue-500 ring-blue-100',
                            'dikembalikan' => 'bg-orange-500 ring-orange-100',
                            'disetujui' => 'bg-emerald-500 ring-emerald-100',
                            'ditolak' => 'bg-rose-500 ring-rose-100',
                            default => 'bg-stone-400 ring-stone-100',
                        };
                        $userRoleBadge = match($log->user->role) {
                            'staf' => 'bg-stone-100 text-stone-600',
                            'petugas' => 'bg-teal-50 text-teal-800',
                            'kepsek' => 'bg-purple-50 text-purple-800',
                            default => 'bg-stone-100 text-stone-600',
                        };
                    @endphp

                    <div class="relative group">
                        <!-- Bullet point dot on vertical line -->
                        <div class="absolute -left-[27px] top-1 w-2.5 h-2.5 rounded-full ring-4 {{ $dotColor }}"></div>

                        <!-- Content of step -->
                        <div>
                            <div class="flex items-baseline justify-between gap-1 flex-wrap">
                                <span class="text-xs font-semibold text-stone-900">
                                    {{ $log->user->name }}
                                </span>
                                <span class="text-[10px] text-stone-400 tabular-nums">
                                    {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>

                            <div class="mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-medium px-1.5 py-0.2 rounded {{ $userRoleBadge }}">
                                    {{ ucfirst($log->user->role) }}
                                </span>

                                <span class="text-[11px] text-stone-500">
                                    @if($log->status_lama)
                                        <span class="line-through text-stone-400">{{ ucfirst($log->status_lama) }}</span>
                                        &rarr;
                                    @endif
                                    <strong class="text-stone-800">{{ ucfirst($log->status_baru) }}</strong>
                                </span>
                            </div>

                            @if($log->catatan)
                                <div class="mt-2 p-2.5 rounded-lg bg-stone-50/80 border border-stone-200/80 text-[11px] text-stone-700 italic leading-relaxed">
                                    "{{ $log->catatan }}"
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-400">Belum ada riwayat persetujuan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
