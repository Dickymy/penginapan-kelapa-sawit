@extends('layouts.public')

@section('title', 'Status Booking - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Status Booking</h1>

    {{-- Booking Card --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Kode Booking</p>
                <p class="text-xl font-bold text-gray-900">{{ $booking->booking_code }}</p>
            </div>
            <div class="mt-2 sm:mt-0">
                @php
                    $statusColor = match($booking->status->value) {
                        'pending_payment' => 'yellow',
                        'confirmed' => 'blue',
                        'checked_in' => 'blue',
                        'checked_out' => 'green',
                        'completed' => 'green',
                        'cancelled' => 'red',
                        'expired' => 'red',
                        'no_show' => 'red',
                        default => 'gray',
                    };
                    $statusLabel = match($booking->status->value) {
                        'pending_payment' => 'Menunggu Pembayaran',
                        'confirmed' => 'Dikonfirmasi',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Kedaluwarsa',
                        'no_show' => 'No Show',
                        default => $booking->status->value,
                    };
                @endphp
                <x-badge :color="$statusColor">{{ $statusLabel }}</x-badge>
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
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Status Pembayaran</span>
                @php
                    $paymentColor = match($booking->payment_status->value) {
                        'unpaid' => 'yellow',
                        'paid' => 'green',
                        'refunded' => 'blue',
                        'partial_refund' => 'blue',
                        default => 'gray',
                    };
                    $paymentLabel = match($booking->payment_status->value) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                        'refunded' => 'Dikembalikan',
                        'partial_refund' => 'Sebagian Dikembalikan',
                        default => $booking->payment_status->value,
                    };
                @endphp
                <p class="mt-1"><x-badge :color="$paymentColor">{{ $paymentLabel }}</x-badge></p>
            </div>
            @if($booking->payment_expires_at && $booking->status->value === 'pending_payment')
                <div>
                    <span class="text-gray-500">Batas Pembayaran</span>
                    <p class="font-medium text-gray-900">{{ $booking->payment_expires_at->format('d M Y H:i') }} WITA</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Status Timeline --}}
    @if($booking->statusHistories->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
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
