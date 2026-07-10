@extends('layouts.public')

@section('title', 'Hasil Pencarian Ketersediaan - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Hasil Pencarian</h1>
        <a href="{{ route('home') }}#cari-kamar"
           class="text-sm text-primary-600 hover:text-primary-800 font-medium mt-1 sm:mt-0">
            ← Ubah Pencarian
        </a>
    </div>

    {{-- Info Bar --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <div class="flex flex-wrap gap-4 text-sm text-blue-800">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Check-in: <strong>{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</strong>
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Check-out: <strong>{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</strong>
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <strong>{{ $nights }}</strong> malam
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <strong>{{ $guestCount }}</strong> tamu
            </span>
        </div>
    </div>

    {{-- Results --}}
    @if($results->isEmpty())
        <div class="text-center py-12 bg-white border border-gray-200 rounded-xl">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mt-4 text-gray-700 text-lg font-medium">Kamar tidak tersedia untuk tanggal tersebut</p>
            <p class="mt-1 text-gray-500 text-sm">
                {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }} – {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }} ({{ $nights }} malam, {{ $guestCount }} tamu)
            </p>
            <p class="mt-2 text-gray-500 text-sm">Coba pilih tanggal lain atau kurangi jumlah tamu.</p>
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}#cari-kamar" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                    Ubah Pencarian
                </a>
                @php $waNoResult = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo, saya ingin bertanya ketersediaan kamar untuk tanggal ' . \Carbon\Carbon::parse($checkIn)->format('d M Y') . ' sampai ' . \Carbon\Carbon::parse($checkOut)->format('d M Y') . '.'); @endphp
                @if($waNoResult)
                <a href="{{ $waNoResult }}" target="_blank" rel="noopener" class="inline-flex items-center px-5 py-2.5 border border-green-600 text-green-700 rounded-lg font-medium hover:bg-green-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    Hubungi Penginapan
                </a>
                @endif
            </div>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($results as $item)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row">
                    {{-- Room Image --}}
                    <div class="md:w-72 h-48 md:h-auto flex-shrink-0">
                        @if($item['room_type']->coverImage)
                            <img src="{{ Storage::disk('public')->url($item['room_type']->coverImage->path) }}"
                                 alt="{{ $item['room_type']->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Room Info --}}
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $item['room_type']->name }}</h2>
                            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Kapasitas: {{ $item['room_type']->capacity }} tamu
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    {{ $item['room_type']->bed_count }} tempat tidur {{ $item['room_type']->bed_type ?? '' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-green-700 font-medium">
                                {{ $item['available_count'] }} kamar tersedia
                            </p>
                        </div>

                        <div class="mt-4 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Harga per malam</p>
                                <p class="text-xl font-bold text-gray-900">
                                    Rp{{ number_format($item['quote']['price_per_night'], 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Total {{ $nights }} malam:
                                    <span class="font-semibold text-gray-900">Rp{{ number_format($item['quote']['total_amount'], 0, ',', '.') }}</span>
                                </p>
                            </div>
                            <a href="{{ route('booking.checkout', [
                                    'room_type_id' => $item['room_type']->id,
                                    'check_in' => $checkIn->format('Y-m-d'),
                                    'check_out' => $checkOut->format('Y-m-d'),
                                    'guest_count' => $guestCount,
                                ]) }}"
                               class="inline-flex items-center justify-center bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 transition">
                                Pilih Kamar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
