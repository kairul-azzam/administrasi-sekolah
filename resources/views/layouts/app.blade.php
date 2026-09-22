<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50 text-stone-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Sistem Administrasi Sekolah') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex bg-stone-50 font-sans selection:bg-teal-100 selection:text-teal-900">
    <x-toast />
    <x-modal-confirm />

    <!-- Mobile Drawer Backdrop -->
    <div id="sidebar-backdrop"
         onclick="toggleMobileSidebar()"
         class="fixed inset-0 z-40 bg-stone-900/40 backdrop-blur-xs hidden md:hidden transition-opacity duration-200">
    </div>

    <!-- Sidebar (240px) -->
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 w-60 bg-white border-r border-stone-200 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <!-- Logo / App Name -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-stone-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-stone-900 text-white flex items-center justify-center flex-shrink-0">
                    <x-icon.academic-cap class="w-4 h-4 text-teal-400" />
                </div>
                <div class="leading-tight">
                    <span class="block text-sm font-semibold tracking-tight text-stone-900">ambtrasi</span>
                    <span class="block text-[10px] text-stone-400 tracking-wider font-medium uppercase">Admin Sekolah</span>
                </div>
            </a>
            <!-- Mobile Close Button -->
            <button type="button"
                    onclick="toggleMobileSidebar()"
                    class="md:hidden p-1.5 rounded-lg text-stone-500 hover:bg-stone-100 transition">
                <x-icon.x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <!-- Menu Group Header -->
            <div class="px-3 pt-2 pb-1 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">
                Navigasi Utama
            </div>

            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                <x-icon.home class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-teal-700' : 'text-stone-400' }}" />
                <span>Dashboard</span>
            </a>

            <!-- Data Siswa (All roles can access) -->
            <a href="{{ route('siswa.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('siswa.*') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                <x-icon.users class="w-4 h-4 {{ request()->routeIs('siswa.*') ? 'text-teal-700' : 'text-stone-400' }}" />
                <span>Data Siswa</span>
                @if(auth()->user()->isPetugas())
                    <span class="ml-auto text-[10px] bg-stone-100 text-stone-600 font-semibold px-1.5 py-0.5 rounded">Kelola</span>
                @endif
            </a>

            <!-- Menu Group Header -->
            <div class="px-3 pt-5 pb-1 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">
                Administrasi
            </div>

            <!-- Pengajuan Links based on role -->
            @if(auth()->user()->isStaf())
                <a href="{{ route('pengajuan.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                    <x-icon.document class="w-4 h-4 {{ request()->routeIs('pengajuan.index') ? 'text-teal-700' : 'text-stone-400' }}" />
                    <span>Pengajuan Saya</span>
                </a>
                <a href="{{ route('pengajuan.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('pengajuan.create') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                    <x-icon.plus class="w-4 h-4 {{ request()->routeIs('pengajuan.create') ? 'text-teal-700' : 'text-stone-400' }}" />
                    <span>Buat Pengajuan</span>
                </a>
            @elseif(auth()->user()->isPetugas())
                <a href="{{ route('pengajuan.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('pengajuan.*') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                    <x-icon.document-check class="w-4 h-4 {{ request()->routeIs('pengajuan.*') ? 'text-teal-700' : 'text-stone-400' }}" />
                    <span>Pemeriksaan Surat</span>
                </a>
            @elseif(auth()->user()->isKepsek())
                <a href="{{ route('pengajuan.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('pengajuan.*') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100/70' }}">
                    <x-icon.check-circle class="w-4 h-4 {{ request()->routeIs('pengajuan.*') ? 'text-teal-700' : 'text-stone-400' }}" />
                    <span>Persetujuan Surat</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar User Footer -->
        <div class="p-3 border-t border-stone-200 bg-stone-50/50">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-8 h-8 rounded-full bg-stone-200 text-stone-700 flex items-center justify-center text-xs font-semibold uppercase flex-shrink-0">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-stone-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-stone-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 md:pl-60 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="h-16 sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-stone-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="toggleMobileSidebar()"
                        class="md:hidden p-2 rounded-lg text-stone-600 hover:bg-stone-100 transition">
                    <x-icon.bars-3 class="w-5 h-5" />
                </button>
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-stone-900">
                        {{ $header ?? 'Dashboard' }}
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Role Chip -->
                @php
                    $roleLabel = match(auth()->user()->role) {
                        'staf' => 'Staf / Guru',
                        'petugas' => 'Petugas TU',
                        'kepsek' => 'Kepala Sekolah',
                        default => auth()->user()->role,
                    };
                    $roleColor = match(auth()->user()->role) {
                        'staf' => 'bg-stone-100 text-stone-700 border-stone-200',
                        'petugas' => 'bg-teal-50 text-teal-800 border-teal-200',
                        'kepsek' => 'bg-purple-50 text-purple-800 border-purple-200',
                        default => 'bg-stone-100 text-stone-700',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleColor }}">
                    {{ $roleLabel }}
                </span>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            title="Keluar dari sistem"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-stone-600 hover:text-stone-900 bg-white hover:bg-stone-100 border border-stone-200 rounded-lg transition duration-150">
                        <x-icon.arrow-right-on-rectangle class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 max-w-7xl w-full mx-auto">
            {{ $slot }}
        </main>

        <!-- Minimalist Footer -->
        <footer class="border-t border-stone-200 bg-white py-4 px-4 sm:px-6 lg:px-8 text-center text-xs text-stone-400">
            Sistem Administrasi Sekolah &copy; {{ date('Y') }}. Dibangun dengan arsitektur MVC Laravel yang rapi dan elegan.
        </footer>
    </div>

    <!-- Mobile Drawer JS -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }
    </script>
</body>
</html>
