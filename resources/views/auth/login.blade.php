<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-stone-900 tracking-tight">Masuk ke Akun</h2>
        <p class="text-xs text-stone-500 mt-1">Gunakan kredensial akun Anda untuk mengakses sistem administrasi.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700">
            <div class="flex items-center gap-2 font-medium">
                <x-icon.exclamation-circle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                <span>Terjadi kesalahan pada input:</span>
            </div>
            <ul class="mt-1.5 list-disc list-inside space-y-0.5 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
                Alamat Email
            </label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="nama@sekolah.test"
                   class="w-full h-10 px-3 text-sm bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150" />
        </div>

        <!-- Password Field -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                    Kata Sandi
                </label>
            </div>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   placeholder="••••••••"
                   class="w-full h-10 px-3 text-sm bg-white border border-stone-200 rounded-lg text-stone-900 placeholder:text-stone-400 focus:outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10 transition duration-150" />
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center">
            <input id="remember"
                   type="checkbox"
                   name="remember"
                   class="w-4 h-4 rounded border-stone-300 text-teal-700 focus:ring-teal-700/20 cursor-pointer">
            <label for="remember" class="ml-2 text-xs text-stone-600 cursor-pointer select-none">
                Ingat sesi saya pada perangkat ini
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full h-10 flex items-center justify-center gap-2 bg-stone-900 hover:bg-stone-800 text-white text-sm font-medium rounded-lg shadow-xs transition duration-150 cursor-pointer">
                <span>Masuk Sekarang</span>
                <x-icon.arrow-right class="w-4 h-4" />
            </button>
        </div>
    </form>

    <!-- Akun Demo Seeder Box -->
    <div class="mt-6 pt-5 border-t border-stone-200">
        <p class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider mb-2 text-center">
            Pilihan Akun Demo (Klik untuk Isi Cepat)
        </p>
        <div class="grid grid-cols-3 gap-2">
            <button type="button"
                    onclick="fillDemo('staf@sekolah.test', 'password')"
                    class="p-2 text-left bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-lg transition">
                <span class="block text-[11px] font-semibold text-stone-900">Staf</span>
                <span class="block text-[10px] text-stone-500 truncate">Guru / TU</span>
            </button>

            <button type="button"
                    onclick="fillDemo('petugas@sekolah.test', 'password')"
                    class="p-2 text-left bg-teal-50/50 hover:bg-teal-50 border border-teal-200/60 rounded-lg transition">
                <span class="block text-[11px] font-semibold text-teal-900">Petugas</span>
                <span class="block text-[10px] text-teal-700 truncate">Admin TU</span>
            </button>

            <button type="button"
                    onclick="fillDemo('kepsek@sekolah.test', 'password')"
                    class="p-2 text-left bg-purple-50/50 hover:bg-purple-50 border border-purple-200/60 rounded-lg transition">
                <span class="block text-[11px] font-semibold text-purple-900">Kepsek</span>
                <span class="block text-[10px] text-purple-700 truncate">Pimpinan</span>
            </button>
        </div>
        <p class="text-[10px] text-stone-400 text-center mt-2">Password default: <code class="font-mono text-stone-600 bg-stone-100 px-1 py-0.5 rounded">password</code></p>
    </div>

    <script>
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
