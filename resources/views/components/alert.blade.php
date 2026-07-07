@props(['type' => 'info', 'message' => '', 'dismissible' => true])

@php
$config = match($type) {
    'success' => [
        'bg' => 'bg-green-50 border-green-200',
        'text' => 'text-green-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'iconColor' => 'text-green-500',
    ],
    'error' => [
        'bg' => 'bg-red-50 border-red-200',
        'text' => 'text-red-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'iconColor' => 'text-red-500',
    ],
    'warning' => [
        'bg' => 'bg-yellow-50 border-yellow-200',
        'text' => 'text-yellow-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        'iconColor' => 'text-yellow-500',
    ],
    default => [
        'bg' => 'bg-blue-50 border-blue-200',
        'text' => 'text-blue-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'iconColor' => 'text-blue-500',
    ],
};
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="rounded-lg border p-4 mb-4 {{ $config['bg'] }}" role="alert">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 {{ $config['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $config['icon'] !!}
        </svg>
        <p class="flex-1 text-sm {{ $config['text'] }}">{{ $message }}</p>
        @if($dismissible)
            <button @click="show = false" type="button" class="flex-shrink-0 {{ $config['text'] }} opacity-60 hover:opacity-100 transition" aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>
</div>
