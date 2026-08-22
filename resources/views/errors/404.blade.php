@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan - ' . \App\Models\Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit'))

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20">
    <div class="max-w-xl mx-auto px-4 text-center">
        <div class="relative w-full max-w-sm mx-auto mb-8">
            <div class="absolute inset-0 bg-primary-100 rounded-full blur-3xl opacity-50"></div>
            <img src="{{ asset('images/hero.jpg') }}" alt="404" class="relative w-full h-64 object-cover rounded-2xl shadow-xl shadow-primary-900/10 grayscale border-4 border-white">
            <div class="absolute -bottom-6 -right-6 bg-white shadow-xl rounded-xl p-4 border border-gray-100">
                <span class="text-5xl font-extrabold text-primary-600 block">404</span>
            </div>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ups! Halaman Tidak Ditemukan</h1>
        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            Sepertinya Anda tersesat. Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau memang tidak pernah ada.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Kembali ke Beranda
            </a>
            <a href="{{ route('rooms.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                Lihat Kamar
            </a>
        </div>
    </div>
</section>
@endsection
