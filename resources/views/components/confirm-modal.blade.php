{{-- Confirmation Dialog Component --}}
{{-- Usage: 
    <x-confirm-modal 
        id="delete-confirm"
        title="Hapus Data" 
        message="Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?"
        confirm-text="Ya, Hapus"
        cancel-text="Batal"
        variant="danger"
        form-action="/delete/1"
        method="DELETE"
    />
    Trigger with: $dispatch('open-confirm', { id: 'delete-confirm' })
--}}

@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin ingin melanjutkan?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'variant' => 'danger',
    'formAction' => '',
    'formId' => '',
    'method' => 'POST',
])

@php
$btnClass = match($variant) {
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white',
    default => 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 text-white',
};

$loadingText = match($variant) {
    'danger' => str_contains(strtolower($confirmText), 'hapus') ? 'Menghapus...' : (str_contains(strtolower($confirmText), 'keluar') ? 'Keluar...' : 'Memproses...'),
    default => 'Memproses...',
};
@endphp

<div x-data="{ open: false, submitting: false }"
     x-on:open-confirm.window="if ($event.detail.id === '{{ $id }}') { open = true; submitting = false; }"
     x-on:keydown.escape.window="if (open) open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $id }}-title"
>
    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50"
         @click="if (!submitting) open = false"></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             x-trap.noscroll="open"
             class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6"
             @click.stop>
            
            <h3 id="{{ $id }}-title" class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
            <p class="text-sm text-gray-600 mb-6">{{ $message }}</p>

            <div class="flex justify-end gap-3">
                <button type="button"
                        @click="open = false"
                        :disabled="submitting"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition disabled:opacity-50">
                    {{ $cancelText }}
                </button>

                @if($formAction)
                    <form method="POST" action="{{ $formAction }}" @submit="submitting = true">
                        @csrf
                        @if(strtoupper($method) === 'DELETE')
                            @method('DELETE')
                        @elseif(strtoupper($method) === 'PATCH')
                            @method('PATCH')
                        @endif
                        <button type="submit"
                                :disabled="submitting"
                                class="px-4 py-2 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition disabled:opacity-60 inline-flex items-center {{ $btnClass }}">
                            <svg x-show="submitting" x-cloak class="animate-spin -ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!submitting">{{ $confirmText }}</span>
                            <span x-show="submitting" x-cloak>{{ $loadingText }}</span>
                        </button>
                    </form>
                @elseif($formId)
                    <button type="button"
                            @click="submitting = true; document.getElementById('{{ $formId }}').requestSubmit ? document.getElementById('{{ $formId }}').requestSubmit() : document.getElementById('{{ $formId }}').submit()"
                            :disabled="submitting"
                            class="px-4 py-2 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition disabled:opacity-60 inline-flex items-center {{ $btnClass }}">
                        <svg x-show="submitting" x-cloak class="animate-spin -ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!submitting">{{ $confirmText }}</span>
                        <span x-show="submitting" x-cloak>{{ $loadingText }}</span>
                    </button>
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
    </div>
</div>
