@extends('layouts.public')

@section('title', 'Kebijakan - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($policy)
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $policy->title }}</h1>
        <p class="text-sm text-gray-500 mb-6">Versi {{ $policy->version }}
            @if($policy->published_at)
                &middot; Diperbarui {{ $policy->published_at->format('d M Y') }}
            @endif
        </p>
        <div class="prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($policy->content)) !!}
        </div>
    @else
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Kebijakan Tamu</h1>
        <div class="text-center py-12">
            <p class="text-gray-500">Kebijakan belum tersedia.</p>
        </div>
    @endif
</div>
@endsection
