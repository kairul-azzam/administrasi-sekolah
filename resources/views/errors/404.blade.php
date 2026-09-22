<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-50 text-stone-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | Sistem Administrasi Sekolah</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center items-center px-4 py-12 bg-stone-50 font-sans">
    <div class="w-full max-w-md text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-stone-100 text-stone-600 mb-6 border border-stone-200 shadow-xs">
            <x-icon.information-circle class="w-8 h-8" />
        </div>

        <span class="text-xs font-semibold text-stone-400 uppercase tracking-widest block mb-1">
            Error 404 &bull; Not Found
        </span>

        <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
            Halaman Tidak Ditemukan
        </h1>

        <p class="text-xs text-stone-600 mt-2.5 leading-relaxed">
            Halaman atau berkas yang Anda tuju tidak ditemukan atau telah dipindahkan.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ url()->previous() ?: route('dashboard') }}"
               class="px-4 py-2 text-xs font-medium text-stone-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-100 transition duration-150">
                &larr; Kembali Sebelumnya
            </a>

            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 text-xs font-medium text-white bg-stone-900 hover:bg-stone-800 rounded-lg shadow-xs transition duration-150">
                Ke Dashboard Utama
            </a>
        </div>
    </div>
</body>
</html>
