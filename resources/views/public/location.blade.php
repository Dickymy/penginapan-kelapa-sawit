@extends('layouts.public')

@section('title', 'Lokasi - Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Lokasi Penginapan Kelapa Sawit di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur. Lihat di Google Maps.">
@endsection

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold">Lokasi Penginapan</h1>
        <p class="mt-2 text-primary-100 text-sm">Temukan kami di Kota Bangun II, Kutai Kartanegara</p>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Address Card --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Alamat</h2>
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div>
                <p class="font-medium text-gray-800">Penginapan Kelapa Sawit</p>
                @if($address)
                    <p class="text-sm text-gray-600 mt-1">{{ $address }}</p>
                @endif
                <p class="text-sm text-gray-500 mt-1">
                    Kota Bangun II, Kecamatan Kota Bangun<br>
                    Kabupaten Kutai Kartanegara<br>
                    Kalimantan Timur
                </p>
            </div>
        </div>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 mt-5">
            @php $googleLink = $mapLink ?: $mapUrl; @endphp
            @if($googleLink)
            <a href="{{ $googleLink }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Buka di Google Maps
            </a>
            @endif
            @php $waDirectionsUrl = \App\Support\WhatsApp::url($whatsapp, 'Halo, saya ingin meminta petunjuk menuju Penginapan Kelapa Sawit.'); @endphp
            @if($waDirectionsUrl)
            <a href="{{ $waDirectionsUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center px-5 py-2.5 border border-green-600 text-green-700 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                Chat untuk Petunjuk Lokasi
            </a>
            @endif
        </div>
    </div>

    {{-- Map --}}
    <div class="rounded-xl overflow-hidden border border-gray-200">
        @if($mapEmbedUrl)
            <iframe src="{{ $mapEmbedUrl }}" width="100%" height="350" class="w-full border-0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Penginapan Kelapa Sawit"></iframe>
        @elseif($mapUrl && str_contains($mapUrl, 'google.com/maps/embed'))
            <iframe src="{{ $mapUrl }}" width="100%" height="350" class="w-full border-0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Penginapan Kelapa Sawit"></iframe>
        @else
            <div class="w-full bg-gray-50 flex flex-col items-center justify-center p-8 text-center" style="min-height: 250px;">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-sm font-medium text-gray-600">Penginapan Kelapa Sawit</p>
                <p class="text-sm text-gray-500 mt-1">Kota Bangun II, Kutai Kartanegara, Kalimantan Timur</p>
                @if($googleLink)
                <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Buka lokasi melalui Google Maps
                </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Contact Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        @if($whatsapp)
        @php $waUrlLoc = \App\Support\WhatsApp::url($whatsapp); @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            <div>
                <p class="text-sm font-medium text-gray-700">WhatsApp</p>
                @if($waUrlLoc)
                <a href="{{ $waUrlLoc }}" target="_blank" rel="noopener" class="text-sm text-green-600 hover:text-green-800">{{ $whatsapp }}</a>
                @else
                <p class="text-sm text-gray-600">{{ $whatsapp }}</p>
                @endif
            </div>
        </div>
        @endif

        @if($email)
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <div>
                <p class="text-sm font-medium text-gray-700">Email</p>
                <a href="mailto:{{ $email }}" class="text-sm text-primary-600 hover:text-primary-800">{{ $email }}</a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
