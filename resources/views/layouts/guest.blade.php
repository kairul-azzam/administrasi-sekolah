<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50 text-stone-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - {{ config('app.name', 'Sistem Administrasi Sekolah') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center items-center px-4 py-12 bg-stone-50 font-sans selection:bg-teal-100 selection:text-teal-900">
    <x-toast />

    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-stone-900 text-white mb-4 shadow-sm">
                <x-icon.academic-cap class="w-6 h-6 text-teal-400" />
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                Sistem Administrasi Sekolah
            </h1>
            <p class="text-xs text-stone-500 mt-1.5 uppercase tracking-widest font-medium">
                Layanan Terpadu & Administrasi Siswa
            </p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl border border-stone-200 p-8 shadow-xs">
            {{ $slot }}
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center text-xs text-stone-400">
            &copy; {{ date('Y') }} Sistem Administrasi Sekolah. Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>
