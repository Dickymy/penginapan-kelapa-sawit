{{-- Loading Button Component --}}
{{-- Usage: <x-loading-button text="Simpan" loading-text="Menyimpan..." /> --}}

@props([
    'variant' => 'primary',
    'type' => 'submit',
    'text' => 'Simpan',
    'loadingText' => 'Memproses...',
])

@php
$classes = match($variant) {
    'primary' => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 focus:ring-gray-400',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
    'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
    'outline' => 'bg-white border border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    default => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
};
@endphp

<button type="{{ $type }}"
    x-data="{ loading: false }"
    x-on:click="if ($el.form && $el.form.checkValidity()) { loading = true; $el.form.submit(); }"
    x-bind:disabled="loading"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-offset-2 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none $classes"]) }}>
    
    {{-- Spinner --}}
    <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    <span x-show="!loading">{{ $text }}</span>
    <span x-show="loading" x-cloak>{{ $loadingText }}</span>
</button>
