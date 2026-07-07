@extends('layouts.member')

@section('title', 'Detail Booking')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $booking->booking_code }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Dibuat {{ $booking->created_at->translatedFormat('d F Y, H:i') }}</p>
        </div>
        <a href="{{ route('member.bookings.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Status Badges --}}
    <div class="flex flex-wrap gap-2">
        <x-status-badge :status="$booking->status" />
        @if($booking->payment_status)
            <x-status-badge :status="$booking->payment_status" />
        @endif
    </div>

    {{-- Payment Action --}}
    @if($booking->status === \App\Enums\BookingStatus::PendingPayment && $booking->is_hold_active)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-yellow-800 font-medium">Menunggu pembayaran</p>
                    <p class="text-xs text-yellow-700 mt-1">Batas waktu: {{ $booking->payment_expires_at->format('d M Y, H:i') }} WITA</p>
                </div>
                <a href="{{ route('booking.pay', $booking->booking_code) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                    Bayar Sekarang
                </a>
            </div>
        </div>
    @endif

    {{-- Booking Details --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Kamar</p>
                <p class="font-semibold text-gray-800">{{ $booking->room_type_name_snapshot }}</p>
                <p class="text-sm text-gray-600">{{ $booking->room_name_snapshot }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jumlah Tamu</p>
                <p class="font-semibold text-gray-800">{{ $booking->guest_count }} orang</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Check-in</p>
                <p class="font-semibold text-gray-800">{{ $booking->check_in->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Check-out</p>
                <p class="font-semibold text-gray-800">{{ $booking->check_out->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Durasi</p>
                <p class="font-semibold text-gray-800">{{ $booking->nights }} malam</p>
            </div>
        </div>

        <hr class="border-gray-100">

        {{-- Pricing breakdown --}}
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Harga per malam</span>
                <span class="text-gray-800">Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">{{ $booking->nights }} malam</span>
                <span class="text-gray-800">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($booking->promotion_discount > 0)
            <div class="flex justify-between text-sm text-green-600">
                <span>Diskon promo ({{ $booking->promotion_code_snapshot }})</span>
                <span>-Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($booking->points_discount > 0)
            <div class="flex justify-between text-sm text-green-600">
                <span>Potongan poin ({{ number_format($booking->points_redeemed) }} poin)</span>
                <span>-Rp{{ number_format($booking->points_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-100">
                <span class="text-gray-900">Total</span>
                <span class="text-primary-600">{{ $booking->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- Invoice Download --}}
    @if($booking->status === \App\Enums\BookingStatus::Completed || $booking->payment_status === \App\Enums\PaymentStatus::Paid)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-800">Invoice</p>
                    <p class="text-xs text-gray-500">{{ $booking->invoice_number ?? 'Tersedia setelah pembayaran' }}</p>
                </div>
            </div>
            @if($booking->invoice_number)
                <a href="{{ route('booking.invoice', $booking->booking_code) }}"
                   class="text-sm text-primary-600 font-medium hover:text-primary-800">
                    Unduh PDF →
                </a>
            @endif
        </div>
    @endif

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Riwayat Status</h2>
        <div class="space-y-3">
            @foreach($booking->statusHistories as $history)
            <div class="flex items-start gap-3 text-sm">
                <span class="text-gray-400 flex-shrink-0 w-28">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-medium text-gray-800">{{ $history->to_status }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
