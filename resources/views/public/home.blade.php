@extends('layouts.public')

@section('title', 'Penginapan Kelapa Sawit - Kota Bangun, Kalimantan Timur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Hero --}}
    <section class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Penginapan Kelapa Sawit</h1>
        <p class="text-lg text-gray-600 mb-2">Kota Bangun, Kalimantan Timur</p>
        <p class="text-gray-500">Penginapan nyaman untuk perjalanan bisnis dan wisata Anda.</p>
    </section>

    {{-- CTA --}}
    <section class="text-center mb-16">
        <a href="#" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
            Pesan Sekarang
        </a>
    </section>

    {{-- Placeholder sections --}}
    <section class="grid md:grid-cols-2 gap-8 mb-16">
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Kamar Tersedia</h2>
            <p class="text-sm text-gray-500">Informasi kamar akan ditampilkan setelah data dikelola admin.</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Cek Ketersediaan</h2>
            <p class="text-sm text-gray-500">Form pencarian ketersediaan akan tersedia setelah fitur aktif.</p>
        </div>
    </section>
</div>
@endsection
