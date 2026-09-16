@props([
    'id' => 'confirm-modal-' . rand(1000, 9999),
    'title' => 'Konfirmasi Aksi',
    'message' => 'Apakah Anda yakin ingin melanjutkan? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'bg-red-600 hover:bg-red-700 text-white',
    'trigger' => null,
])

<div
    x-data="{ open: false }"
    x-on:open-confirm-modal.window="if($event.detail.id === '{{ $id }}') open = true"
    x-trap.noscroll="open"
    @keydown.escape.window="open = false"
>
    {{-- Trigger Slot (optional) --}}
    @if($trigger)
        <div @click="open = true">{{ $trigger }}</div>
    @endif

    {{-- Modal Overlay --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
        @click.self="open = false"
        x-cloak
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="w-full max-w-sm rounded-xl bg-white shadow-xl p-6"
        >
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
                    {{ $slot }}
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button
                    type="button"
                    @click="open = false"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50"
                >
                    {{ $cancelText }}
                </button>
                <button
                    type="button"
                    @click="open = false; $dispatch('confirm-modal-confirmed', { id: '{{ $id }}' })"
                    class="px-4 py-2 text-sm font-medium rounded-lg {{ $confirmClass }}"
                >
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>
