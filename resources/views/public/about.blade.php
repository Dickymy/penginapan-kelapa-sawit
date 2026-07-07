@extends('layouts.public')

@section('title', 'Tentang - ' . $propertyName)

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold">Tentang {{ $propertyName }}</h1>
        <p class="mt-2 text-primary-100">Kota Bangun, Kalimantan Timur</p>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($content)
        <div class="prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($content)) !!}
        </div>
    @else
        {{-- Professional fallback with known facts --}}
        <div class="space-y-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">{{ $propertyName }}</h2>
                <p class="text-gray-600 leading-relaxed">
                    {{ $propertyName }} berlokasi di Kota Bangun, Kalimantan Timur. Kami menyediakan akomodasi yang nyaman untuk tamu yang berkunjung ke wilayah ini.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Lokasi</h3>
                    <p class="text-gray-600 text-sm">Kota Bangun, Kalimantan Timur, Indonesia</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Pemesanan</h3>
                    <p class="text-gray-600 text-sm">Pesan langsung melalui website kami atau hubungi via WhatsApp.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- CTA Section --}}
    <div class="mt-12 flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
            Lihat Kamar
        </a>
        <a href="{{ route('location') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
            Hubungi Kami
        </a>
    </div>
</div>
@endsection
