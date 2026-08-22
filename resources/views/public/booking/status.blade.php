@extends('layouts.public')

@section('title', 'Status Booking - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Status Booking</h1>

    {{-- Booking Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 mb-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Kode Booking</p>
                <p class="text-xl font-bold text-gray-900">{{ $booking->booking_code }}</p>
            </div>
            <div class="mt-2 sm:mt-0">
                <x-status-badge :status="$booking->status" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Tipe Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_type_name_snapshot }}</p>
            </div>
            <div>
                <span class="text-gray-500">Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_name_snapshot ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Check-in</span>
                <p class="font-medium text-gray-900">{{ $booking->check_in->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Check-out</span>
                <p class="font-medium text-gray-900">{{ $booking->check_out->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Durasi</span>
                <p class="font-medium text-gray-900">{{ $booking->nights }} malam</p>
            </div>
            <div>
                <span class="text-gray-500">Total</span>
                <p class="font-bold text-gray-900">{{ $booking->formatted_total }}</p>
            </div>
        </div>
    </div>

    {{-- Payment Info --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 mb-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Status Pembayaran</span>
                <p class="mt-1"><x-status-badge :status="$booking->payment_status" /></p>
            </div>
            @if($booking->payment_expires_at && $booking->status === \App\Enums\BookingStatus::PendingPayment)
                <div>
                    <span class="text-gray-500">Batas Pembayaran</span>
                    <p class="font-medium text-gray-900">{{ $booking->payment_expires_at->format('d M Y H:i') }} WITA</p>
                </div>
            @endif
        </div>

        @if($booking->status === \App\Enums\BookingStatus::PendingPayment && $booking->is_hold_active)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('booking.pay', $booking->booking_code) }}"
                   class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-500 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300">
                    Bayar Sekarang
                </a>
            </div>
        @endif
    </div>

    {{-- Status Timeline --}}
    @if($booking->statusHistories->isNotEmpty())
        <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 mb-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Status</h2>

            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                <div class="space-y-4">
                    @foreach($booking->statusHistories as $history)
                        <div class="relative flex items-start gap-4 pl-10">
                            <div class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full bg-primary-500 border-2 border-white shadow"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                </p>
                                @if($history->notes)
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $history->notes }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $history->created_at->format('d M Y H:i') }} WITA
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
