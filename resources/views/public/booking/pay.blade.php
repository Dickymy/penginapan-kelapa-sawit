@extends('layouts.public')

@section('title', 'Pembayaran - ' . $booking->booking_code)

@section('meta')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="paymentPage()">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Pembayaran</h1>

    {{-- Sandbox Indicator --}}
    @if(!config('midtrans.is_production'))
    <div class="bg-orange-50 border border-orange-300 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <p class="text-sm font-medium text-orange-800">
                Mode Uji Coba — Jangan gunakan uang asli.
            </p>
        </div>
    </div>
    @endif

    {{-- Countdown --}}
    @if($booking->payment_expires_at && $booking->payment_expires_at->isFuture())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-yellow-800">
                Selesaikan pembayaran dalam <span class="font-bold" x-text="countdown"></span>
            </p>
        </div>
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Kode Booking</span>
                <p class="font-bold text-gray-900">{{ $booking->booking_code }}</p>
            </div>
            <div>
                <span class="text-gray-500">Total Pembayaran</span>
                <p class="font-bold text-primary-600 text-xl">{{ $booking->formatted_total }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tipe Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_type_name_snapshot }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tanggal Menginap</span>
                <p class="font-medium text-gray-900">{{ $booking->check_in->format('d M Y') }} — {{ $booking->check_out->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button @click="pay()" :disabled="loading"
                class="inline-flex items-center bg-primary-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-show="!loading">Bayar Sekarang</span>
            <span x-show="loading" x-cloak>Memproses pembayaran...</span>
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
    function paymentPage() {
        return {
            loading: false,
            countdown: '',
            expiresAt: new Date('{{ $booking->payment_expires_at->toIso8601String() }}'),
            
            init() {
                this.updateCountdown();
                setInterval(() => this.updateCountdown(), 1000);
            },

            updateCountdown() {
                const now = new Date();
                const diff = this.expiresAt - now;
                
                if (diff <= 0) {
                    this.countdown = '00:00';
                    return;
                }
                
                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                this.countdown = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            },

            pay() {
                this.loading = true;
                snap.pay('{{ $snapToken }}', {
                    onSuccess: (result) => {
                        window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
                    },
                    onPending: (result) => {
                        window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
                    },
                    onError: (result) => {
                        this.loading = false;
                        window.location.href = '{{ route("booking.finish", $booking->booking_code) }}';
                    },
                    onClose: () => {
                        this.loading = false;
                    }
                });
            }
        }
    }
</script>
@endsection
