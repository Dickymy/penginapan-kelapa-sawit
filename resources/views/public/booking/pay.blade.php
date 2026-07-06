@extends('layouts.public')

@section('title', 'Pembayaran - ' . $booking->booking_code)

@section('meta')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Pembayaran</h1>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Kode Booking</span>
                <p class="font-bold text-gray-900">{{ $booking->booking_code }}</p>
            </div>
            <div>
                <span class="text-gray-500">Total</span>
                <p class="font-bold text-primary-600 text-lg">{{ $booking->formatted_total }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tipe Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_type_name_snapshot }}</p>
            </div>
            <div>
                <span class="text-gray-500">Batas Pembayaran</span>
                <p class="font-medium text-gray-900">{{ $booking->payment_expires_at->format('d M Y H:i') }} WITA</p>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button id="pay-button"
                class="bg-primary-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
            Bayar Sekarang
        </button>
        <p class="mt-3 text-sm text-gray-500">Anda akan diarahkan ke halaman pembayaran Midtrans.</p>
    </div>
</div>

@php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function() {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
            },
            onPending: function(result) {
                window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
            },
            onError: function(result) {
                window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
            },
            onClose: function() {
                // User closed popup without completing payment
            }
        });
    });
</script>
@endsection
