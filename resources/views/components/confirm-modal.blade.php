{{-- Confirmation Dialog Component --}}
{{-- Usage: 
    <x-confirm-modal 
        id="delete-confirm"
        title="Hapus Data" 
        message="Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?"
        confirm-text="Ya, Hapus"
        cancel-text="Batal"
        variant="danger"
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
    'method' => 'POST',
])

@php
$btnClass = match($variant) {
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white',
    default => 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 text-white',
};
@endphp

<div x-data="{ open: false }"
     x-on:open-confirm.window="if ($event.detail.id === '{{ $id }}') open = true"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $id }}-title"
>
    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50" @click="open = false"></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6" @click.stop>
            
            <h3 id="{{ $id }}-title" class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
            <p class="text-sm text-gray-600 mb-6">{{ $message }}</p>

            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition">
                    {{ $cancelText }}
                </button>

                @if($formAction)
                    <form method="POST" action="{{ $formAction }}">
                        @csrf
                        @if(strtoupper($method) === 'DELETE')
                            @method('DELETE')
                        @elseif(strtoupper($method) === 'PATCH')
                            @method('PATCH')
                        @endif
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition {{ $btnClass }}">
                            {{ $confirmText }}
                        </button>
                    </form>
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
    </div>
</div>
