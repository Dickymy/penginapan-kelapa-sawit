@extends('layouts.public')

@section('title', 'Booking Berhasil - Penginapan Kelapa Sawit')

@section('meta')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
@php
    use App\Enums\BookingStatus;
    $accessUrl = $rawToken
        ? route('booking.guest.detail', ['bookingCode' => $booking->booking_code, 'access' => $rawToken])
        : route('booking.guest.detail', $booking->booking_code);
@endphp

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
    {{-- Success Header --}}
    <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Booking Berhasil!</h1>
        <p class="text-gray-600 mt-2">Booking Anda telah berhasil dibuat. Silakan lakukan pembayaran sebelum batas waktu.</p>
    </div>

    {{-- Booking Summary Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Kode Booking</p>
                <p class="text-lg font-bold text-gray-900 font-mono">{{ $booking->booking_code }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                Menunggu Pembayaran
            </span>
        </div>

        <div class="px-5 sm:px-6 py-5">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500">Tipe Kamar</p>
                    <p class="font-semibold text-gray-900">{{ $booking->room_type_name_snapshot }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tamu</p>
                    <p class="font-semibold text-gray-900">{{ $booking->guest_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Check-in</p>
                    <p class="font-semibold text-gray-900">{{ $booking->check_in->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Check-out</p>
                    <p class="font-semibold text-gray-900">{{ $booking->check_out->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Durasi</p>
                    <p class="font-semibold text-gray-900">{{ $booking->nights }} malam</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Jumlah Tamu</p>
                    <p class="font-semibold text-gray-900">{{ $booking->guest_count }} orang</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-600">Total Pembayaran</span>
                <span class="text-xl font-bold text-primary-600">{{ $booking->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Deadline Warning --}}
    @if($booking->payment_expires_at)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-amber-800">Batas waktu pembayaran</p>
            <p class="text-sm text-amber-700">{{ $booking->payment_expires_at->translatedFormat('d F Y, H:i') }} WITA</p>
            <p class="text-xs text-amber-600 mt-1">Booking akan otomatis dibatalkan jika pembayaran tidak diselesaikan.</p>
        </div>
    </div>
    @endif

    {{-- CTA Buttons --}}
    <div class="space-y-3 mb-8">
        <a href="{{ route('booking.pay', $booking->booking_code) }}"
           class="w-full flex items-center justify-center px-6 py-3.5 bg-primary-600 text-white rounded-xl font-semibold text-base hover:bg-primary-700 transition shadow-sm">
            Bayar Sekarang
        </a>
        <a href="{{ route('booking.guest.detail', $booking->booking_code) }}"
           class="w-full flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition">
            Lihat Detail Booking
        </a>
    </div>

    {{-- Save Access Link --}}
    @if($rawToken)
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5" x-data="{ copied: false, showToken: false }">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Simpan Link Akses Booking</h3>
        <p class="text-xs text-gray-600 mb-4">Gunakan link di bawah untuk mengakses booking ini kapan saja tanpa login.</p>

        <div class="flex gap-2">
            <button type="button"
                    @click="
                        navigator.clipboard.writeText('{{ $accessUrl }}');
                        copied = true;
                        setTimeout(() => copied = false, 2500);
                        $dispatch('toast', { type: 'success', message: 'Link booking berhasil disalin' });
                    "
                    class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition"
                    :class="copied ? 'bg-green-100 text-green-700' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
                <svg x-show="!copied" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                <svg x-show="copied" x-cloak class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="copied ? 'Link Tersalin!' : 'Salin Link Booking'"></span>
            </button>

            @php
                $waShareUrl = \App\Support\WhatsApp::shareUrl('Booking saya di Penginapan Kelapa Sawit' . "\n" . 'Kode: ' . $booking->booking_code . "\n" . 'Link: ' . $accessUrl);
            @endphp
            @if($waShareUrl)
            <a href="{{ $waShareUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition"
               aria-label="Bagikan ke WhatsApp">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            </a>
            @endif
        </div>

        <p class="text-xs text-gray-500 mt-3">
            💡 Tip: Anda juga dapat mengecek booking melalui menu <strong>Booking Saya</strong> dengan kode booking dan nomor WhatsApp.
        </p>
    </div>
    @endif
</div>
@endsection
