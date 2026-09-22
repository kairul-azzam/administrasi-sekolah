@if (session('success') || session('error') || session('info') || session('status'))
    @php
        $type = session('error') ? 'error' : (session('info') ? 'info' : 'success');
        $message = session('success') ?? session('error') ?? session('info') ?? session('status');
    @endphp

    <div id="toast-notification"
         class="fixed top-5 right-5 z-50 flex items-start gap-3 p-4 bg-white border border-stone-200 rounded-xl shadow-lg max-w-sm transition-all duration-300 ease-out transform translate-y-0 opacity-100"
         role="alert">
        @if ($type === 'success')
            <div class="p-1 rounded-lg bg-emerald-50 text-emerald-600">
                <x-icon.check class="w-4 h-4" />
            </div>
        @elseif ($type === 'error')
            <div class="p-1 rounded-lg bg-rose-50 text-rose-600">
                <x-icon.x-mark class="w-4 h-4" />
            </div>
        @else
            <div class="p-1 rounded-lg bg-teal-50 text-teal-700">
                <x-icon.information-circle class="w-4 h-4" />
            </div>
        @endif

        <div class="flex-1 pt-0.5">
            <p class="text-sm font-medium text-stone-900 leading-tight">
                {{ $type === 'success' ? 'Berhasil' : ($type === 'error' ? 'Perhatian' : 'Informasi') }}
            </p>
            <p class="text-xs text-stone-600 mt-0.5 leading-relaxed">
                {{ $message }}
            </p>
        </div>

        <button type="button"
                onclick="dismissToast()"
                class="text-stone-400 hover:text-stone-600 transition p-0.5 rounded-md hover:bg-stone-100">
            <x-icon.x-mark class="w-3.5 h-3.5" />
        </button>
    </div>

    <script>
        function dismissToast() {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }
        setTimeout(dismissToast, 4000);
    </script>
@endif
