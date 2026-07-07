@extends('layouts.public')

@section('title', 'Akses Ditolak')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="mx-auto w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Akses Ditolak</h1>
    <p class="text-gray-500 mb-8">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
        Kembali ke Beranda
    </a>
</div>
@endsection
