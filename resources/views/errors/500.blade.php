@extends('layouts.public')

@section('title', 'Terjadi Kesalahan')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="mx-auto w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Terjadi Kesalahan</h1>
    <p class="text-gray-500 mb-8">Maaf, terjadi kesalahan pada server. Tim kami sudah diberitahu. Silakan coba lagi nanti.</p>
    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
        Kembali ke Beranda
    </a>
</div>
@endsection
