@extends('layouts.public')

@section('title', 'Terlalu Banyak Permintaan')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="mx-auto w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Terlalu Banyak Permintaan</h1>
    <p class="text-gray-500 mb-8">Anda telah mengirim terlalu banyak permintaan. Silakan tunggu sebentar lalu coba lagi.</p>
    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
        Kembali ke Beranda
    </a>
</div>
@endsection
