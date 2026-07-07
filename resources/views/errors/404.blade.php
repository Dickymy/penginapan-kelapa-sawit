@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Halaman Tidak Ditemukan</h1>
    <p class="text-gray-500 mb-8">Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('home') }}" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
            Kembali ke Beranda
        </a>
        <a href="{{ route('rooms.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
            Lihat Kamar
        </a>
    </div>
</div>
@endsection
