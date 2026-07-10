@props([
    'phone' => null,
    'message' => null,
])
@php
    $url = \App\Support\WhatsApp::url($phone, $message);
@endphp
@if($url)
<a href="{{ $url }}" target="_blank" rel="noopener" {{ $attributes }}>{{ $slot }}</a>
@endif
