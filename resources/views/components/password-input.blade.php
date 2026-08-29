{{-- Password Input with Show/Hide Toggle --}}
{{-- Usage: <x-password-input name="password" label="Kata Sandi" /> --}}

@props([
    'name' => 'password',
    'label' => 'Kata Sandi',
    'id' => null,
    'placeholder' => '',
    'required' => false,
    'showHints' => false,
    'autocomplete' => 'current-password',
])

@php
$fieldId = $id ?? $name;
@endphp

<div x-data="{ show: false, value: '' }" x-modelable="value" {{ $attributes->whereStartsWith('x-model') }}>
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <div class="relative">
        <input :type="show ? 'text' : 'password'"
               name="{{ $name }}"
               id="{{ $fieldId }}"
               x-model="value"
               @if($placeholder) placeholder="{{ $placeholder }}" @endif
               @if($required) required @endif
               autocomplete="{{ $autocomplete }}"
               {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 pr-10']) }}>
        <button type="button"
                @click="show = !show"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
            {{-- Eye icon (show) --}}
            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{-- Eye-off icon (hide) --}}
            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
        </button>
    </div>

    @if($showHints)
        <div class="mt-2 space-y-1">
            <p class="text-xs text-gray-500 mb-1">Kata sandi harus memenuhi:</p>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" :class="value.length >= 8 ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs" :class="value.length >= 8 ? 'text-green-700' : 'text-gray-500'">Minimal 8 karakter</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" :class="/[A-Z]/.test(value) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs" :class="/[A-Z]/.test(value) ? 'text-green-700' : 'text-gray-500'">Memiliki huruf besar</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" :class="/[a-z]/.test(value) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs" :class="/[a-z]/.test(value) ? 'text-green-700' : 'text-gray-500'">Memiliki huruf kecil</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" :class="/[0-9]/.test(value) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs" :class="/[0-9]/.test(value) ? 'text-green-700' : 'text-gray-500'">Memiliki angka</span>
            </div>
        </div>
    @endif

    <x-form-error :field="$name" />
</div>
