@props(['color' => null, 'type' => null])

@php
// Support both 'color' and 'type' props for backward compatibility
$variant = $color ?? $type ?? 'gray';

$classes = match($variant) {
    'green', 'success' => 'bg-green-100 text-green-800',
    'red', 'danger' => 'bg-red-100 text-red-800',
    'yellow', 'warning' => 'bg-yellow-100 text-yellow-800',
    'blue', 'info' => 'bg-blue-100 text-blue-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'gray', 'secondary' => 'bg-gray-100 text-gray-800',
    default => 'bg-gray-100 text-gray-800',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
