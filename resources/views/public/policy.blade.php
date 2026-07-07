@extends('layouts.public')

@section('title', 'Kebijakan - Penginapan Kelapa Sawit')

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold">Kebijakan Tamu</h1>
        <p class="mt-2 text-primary-100">Informasi penting untuk tamu penginapan</p>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($policy)
        <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800">{{ $policy->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Versi {{ $policy->version }}
                    @if($policy->published_at)
                        &middot; Diperbarui {{ $policy->published_at->translatedFormat('d F Y') }}
                    @endif
                </p>
            </div>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($policy->content)) !!}
            </div>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-600 font-medium">Kebijakan penginapan sedang diperbarui.</p>
            <p class="text-sm text-gray-500 mt-2">Silakan hubungi penginapan untuk informasi sebelum melakukan pemesanan.</p>
            <a href="{{ route('location') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                Hubungi Kami
            </a>
        </div>
    @endif
</div>
@endsection
