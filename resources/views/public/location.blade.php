@extends('layouts.public')

@section('title', 'Lokasi - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Lokasi</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Info --}}
        <div class="space-y-4">
            @if($address)
            <div>
                <h2 class="text-sm font-semibold text-gray-700 uppercase mb-1">Alamat</h2>
                <p class="text-gray-600">{{ $address }}</p>
            </div>
            @endif

            @if($whatsapp)
            <div>
                <h2 class="text-sm font-semibold text-gray-700 uppercase mb-1">WhatsApp</h2>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener" class="text-green-600 hover:text-green-800">
                    {{ $whatsapp }}
                </a>
            </div>
            @endif

            @if($email)
            <div>
                <h2 class="text-sm font-semibold text-gray-700 uppercase mb-1">Email</h2>
                <a href="mailto:{{ $email }}" class="text-primary-600 hover:text-primary-800">{{ $email }}</a>
            </div>
            @endif
        </div>

        {{-- Map --}}
        <div>
            @if($mapUrl)
                @if(str_contains($mapUrl, 'google.com/maps/embed'))
                    <iframe src="{{ $mapUrl }}" width="100%" height="300" class="rounded-lg border-0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @else
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Buka di Google Maps
                    </a>
                @endif
            @else
                <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400 text-sm">Peta belum tersedia</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
