@props(['variant' => 'primary', 'type' => 'submit'])

@php
$classes = match($variant) {
    'primary' => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 focus:ring-gray-400',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
    default => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
};
@endphp

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition $classes"]) }}>
    {{ $slot }}
</button>
