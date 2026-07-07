@extends('layouts.public')

@section('title', 'Lokasi - Penginapan Kelapa Sawit')

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold">Lokasi</h1>
        <p class="mt-2 text-primary-100">Temukan kami di Kota Bangun, Kalimantan Timur</p>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Info (Kiri) --}}
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontak</h2>
                <div class="space-y-4">
                    @if($address)
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Alamat</p>
                            <p class="text-sm text-gray-600">{{ $address }}</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Lokasi</p>
                            <p class="text-sm text-gray-600">Kota Bangun, Kalimantan Timur</p>
                        </div>
                    </div>
                    @endif

                    @if($whatsapp)
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">WhatsApp</p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener" class="text-sm text-green-600 hover:text-green-800">
                                {{ $whatsapp }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($email)
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Email</p>
                            <a href="mailto:{{ $email }}" class="text-sm text-primary-600 hover:text-primary-800">{{ $email }}</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Buka di Google Maps --}}
            @php $googleLink = $mapLink ?: $mapUrl; @endphp
            @if($googleLink)
            <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="inline-flex items-center px-5 py-3 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Buka di Google Maps
            </a>
            @endif
        </div>

        {{-- Map (Kanan) --}}
        <div>
            @if($mapEmbedUrl)
                <iframe src="{{ $mapEmbedUrl }}" width="100%" height="100%" style="min-height: 350px;" class="rounded-xl border-0 shadow-sm w-full h-full" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @elseif($mapUrl && str_contains($mapUrl, 'google.com/maps/embed'))
                <iframe src="{{ $mapUrl }}" width="100%" height="100%" style="min-height: 350px;" class="rounded-xl border-0 shadow-sm w-full h-full" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @else
                <div class="w-full h-full min-h-[350px] bg-gray-50 border border-gray-200 rounded-xl flex flex-col items-center justify-center p-6 text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm font-medium text-gray-600">Penginapan Kelapa Sawit</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $address ?: 'Kota Bangun, Kalimantan Timur' }}</p>
                    @if($googleLink)
                    <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-medium hover:bg-primary-700 transition">
                        Lihat di Google Maps
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
