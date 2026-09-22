@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-stone-900/40 backdrop-blur-xs transition-opacity duration-150 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white rounded-xl border border-stone-200 shadow-xl p-6 transition-all transform duration-150">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                <x-icon.exclamation-circle class="w-5 h-5" />
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-stone-900" id="{{ $id }}-title">{{ $title }}</h3>
                <p class="mt-1.5 text-xs text-stone-600 leading-relaxed" id="{{ $id }}-message">{{ $message }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2.5">
            <button type="button"
                    onclick="closeConfirmModal('{{ $id }}')"
                    class="px-3.5 py-2 text-xs font-medium text-stone-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 hover:text-stone-900 transition duration-150">
                {{ $cancelText }}
            </button>
            <form id="{{ $id }}-form" method="POST" action="">
                @csrf
                <div id="{{ $id }}-method"></div>
                <button type="submit"
                        class="px-3.5 py-2 text-xs font-medium text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition duration-150">
                    {{ $confirmText }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openConfirmModal(modalId, actionUrl, title = null, message = null, method = 'DELETE') {
        const modal = document.getElementById(modalId);
        const form = document.getElementById(modalId + '-form');
        const methodDiv = document.getElementById(modalId + '-method');
        if (title) document.getElementById(modalId + '-title').innerText = title;
        if (message) document.getElementById(modalId + '-message').innerText = message;
        form.action = actionUrl;
        methodDiv.innerHTML = `<input type="hidden" name="_method" value="${method}">`;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeConfirmModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
