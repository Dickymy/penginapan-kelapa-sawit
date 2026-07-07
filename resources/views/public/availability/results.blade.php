@extends('layouts.public')

@section('title', 'Hasil Pencarian Ketersediaan - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Hasil Pencarian Ketersediaan</h1>

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
            <p class="mt-4 text-gray-700 text-lg font-medium">Kamar tidak tersedia untuk tanggal tersebut.</p>
            <p class="mt-1 text-gray-500 text-sm">Coba pilih tanggal lain atau kurangi jumlah tamu.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                Ubah Tanggal
            </a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($results as $item)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row">
                    {{-- Room Image --}}
                    <div class="md:w-72 h-48 md:h-auto flex-shrink-0">
                        @if($item['room_type']->coverImage)
                            <img src="{{ asset('storage/' . $item['room_type']->coverImage->path) }}"
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
