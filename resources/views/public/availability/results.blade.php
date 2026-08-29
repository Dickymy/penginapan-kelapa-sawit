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

    {{-- Search Summary --}}
    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-primary-900">
        <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="font-semibold">Check-in:</span> {{ Carbon\Carbon::parse(request('check_in'))->format('d M Y') }}
        </div>
        <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="font-semibold">Check-out:</span> {{ Carbon\Carbon::parse(request('check_out'))->format('d M Y') }}
        </div>
        <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <span class="font-semibold">{{ Carbon\Carbon::parse(request('check_in'))->diffInDays(Carbon\Carbon::parse(request('check_out'))) }}</span> malam
        </div>
        <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="font-semibold">{{ request('guest_count', 1) }}</span> tamu
        </div>
    </div>

    @php
        $requestedRoomSlug = request('room_type');
        $requestedRoom = null;
        $isRequestedAvailable = false;
        
        if ($requestedRoomSlug) {
            $requestedRoom = collect($results)->first(function ($item) use ($requestedRoomSlug) {
                return isset($item['room_type']) && $item['room_type']->slug === $requestedRoomSlug;
            });
            if ($requestedRoom) {
                $isRequestedAvailable = $requestedRoom['available_count'] > 0;
            }
        }
    @endphp

    @if($requestedRoom && !$isRequestedAvailable)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-5 mb-8 rounded-r-xl shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-amber-800">Kamar Pilihan Anda Tidak Tersedia</h3>
                    <div class="mt-1 text-amber-700 text-sm">
                        <p>Mohon maaf, tipe kamar <strong class="text-amber-900">{{ $requestedRoom['room_type']->name }}</strong> saat ini sedang tidak tersedia untuk tanggal yang Anda pilih.</p>
                        @if(collect($results)->where('available_count', '>', 0)->count() > 0)
                            <p class="mt-2 font-medium">Sebagai gantinya, kami merekomendasikan tipe kamar lain yang masih tersedia di bawah ini 👇</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

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
            @php $hasShownUnavailableDivider = false; @endphp
            @foreach($results as $item)
                @php
                    $isFullyBlocked = $item['available_count'] == 0 && isset($item['blocked_rooms']) && $item['blocked_rooms']->isNotEmpty();
                    $isFullyBooked = $item['available_count'] == 0 && !$isFullyBlocked;
                    $isPartiallyBlocked = $item['available_count'] > 0 && isset($item['blocked_rooms']) && $item['blocked_rooms']->isNotEmpty();
                @endphp

                @if($item['available_count'] == 0 && !$hasShownUnavailableDivider && $results->first()['available_count'] > 0)
                    <div class="flex items-center gap-4 py-4 mt-2">
                        <div class="h-px bg-gray-300 flex-1"></div>
                        <span class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Tidak Tersedia</span>
                        <div class="h-px bg-gray-300 flex-1"></div>
                    </div>
                    @php $hasShownUnavailableDivider = true; @endphp
                @endif

                <div class="relative bg-white border {{ $isFullyBlocked ? 'border-red-400 ring-1 ring-red-200 shadow-red-100' : 'border-gray-200 group hover:shadow-lg hover:border-primary-200 transition-all duration-300' }} rounded-xl shadow-sm overflow-hidden flex flex-col {{ $isFullyBooked ? 'opacity-80' : '' }}">
                    <div class="flex flex-col md:flex-row {{ $isFullyBlocked ? 'bg-red-50/30' : '' }}">
                    {{-- Room Image --}}
                    <div class="md:w-72 h-48 md:h-auto flex-shrink-0 relative overflow-hidden">
                        @if($item['room_type']->coverImage)
                            <img src="{{ $item['room_type']->coverImage->medium_url }}"
                                 srcset="{{ $item['room_type']->coverImage->thumb_url }} 480w, {{ $item['room_type']->coverImage->medium_url }} 960w"
                                 sizes="(max-width: 768px) 100vw, 288px"
                                 alt="{{ $item['room_type']->name }}"
                                 loading="lazy"
                                 decoding="async"
                                 width="480" height="360"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        @if($isFullyBlocked)
                            <div class="absolute inset-0 bg-red-900/30 flex items-center justify-center backdrop-blur-[2px] p-4">
                                <span class="bg-red-600 text-white px-5 py-2 rounded-lg font-bold uppercase tracking-widest text-sm shadow-lg border border-red-500/50 text-center max-w-full truncate">
                                    {{ $item['blocked_rooms']->first()['reason'] ?? 'TIDAK TERSEDIA' }}
                                </span>
                            </div>
                        @elseif($isFullyBooked)
                            <div class="absolute inset-0 bg-gray-900/30 flex items-center justify-center backdrop-blur-[2px]">
                                <span class="bg-gray-800 text-white px-5 py-2 rounded-lg font-bold uppercase tracking-widest text-sm shadow-lg border border-gray-700/50">Penuh</span>
                            </div>
                        @endif
                    </div>

                    {{-- Room Info --}}
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">
                                <a href="{{ route('rooms.show', [
                                    'slug' => $item['room_type']->slug,
                                    'check_in' => $checkIn->format('Y-m-d'),
                                    'check_out' => $checkOut->format('Y-m-d'),
                                    'guest_count' => $guestCount
                                ]) }}" class="focus:outline-none before:absolute before:inset-0 before:z-10">
                                    {{ $item['room_type']->name }}
                                </a>
                            </h2>
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
                            @if($item['available_count'] > 0)
                                <p class="mt-2 text-sm text-green-700 font-medium">
                                    {{ $item['available_count'] }} kamar tersedia
                                </p>
                            @else
                                <p class="mt-2 text-sm text-red-600 font-medium">
                                    Kamar tidak tersedia
                                </p>
                            @endif
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
                            <div class="flex items-center gap-2">
                                <a href="{{ route('rooms.show', [
                                        'slug' => $item['room_type']->slug,
                                        'check_in' => $checkIn->format('Y-m-d'),
                                        'check_out' => $checkOut->format('Y-m-d'),
                                        'guest_count' => $guestCount
                                    ]) }}"
                                   class="relative z-20 inline-flex items-center justify-center bg-white text-gray-700 border border-gray-300 px-4 py-3 rounded-lg font-medium hover:bg-gray-50 hover:text-primary-700 transition-all duration-300">
                                    Lihat Detail
                                </a>
                                @if($item['available_count'] > 0)
                                    <a href="{{ route('booking.checkout', [
                                            'room_type_id' => $item['room_type']->id,
                                            'check_in' => $checkIn->format('Y-m-d'),
                                            'check_out' => $checkOut->format('Y-m-d'),
                                            'guest_count' => $guestCount,
                                        ]) }}"
                                       class="relative z-20 inline-flex items-center justify-center bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                                        Pilih Kamar
                                    </a>
                                @else
                                    <button type="button" disabled
                                            class="relative z-20 inline-flex items-center justify-center bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-medium cursor-not-allowed">
                                        Tidak Tersedia
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    </div>

                    {{-- Blocked Rooms Info (At the bottom, separated by border) --}}
                    @if($isFullyBlocked)
                        <div class="border-t border-red-200 bg-red-50 p-4 px-6">
                            <div class="flex items-start gap-3 text-sm text-red-800">
                                <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <span class="font-bold text-base text-red-900">Kamar Tidak Dapat Dipesan</span>
                                    <ul class="mt-1 space-y-1 text-red-700">
                                        @foreach($item['blocked_rooms'] as $blocked)
                                            <li>&bull; {{ $blocked['reason'] ?? 'Tanpa keterangan' }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif($isPartiallyBlocked)
                        <div class="border-t border-orange-100 bg-orange-50/50 p-3 px-6">
                            <div class="flex items-center gap-2 text-sm text-orange-800">
                                <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>
                                    <strong>Info:</strong> {{ $item['blocked_rooms']->count() }} unit lain pada tipe kamar ini sedang tidak tersedia ({{ $item['blocked_rooms']->pluck('reason')->filter()->join(', ') ?: 'Diblokir' }}).
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
