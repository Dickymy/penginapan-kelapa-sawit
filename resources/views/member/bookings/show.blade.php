@extends('layouts.member')

@section('title', 'Detail Booking')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Booking {{ $booking->booking_code }}</h1>
        <a href="{{ route('member.bookings.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Status</span>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                @if($booking->status === \App\Enums\BookingStatus::Confirmed) bg-green-100 text-green-800
                @elseif($booking->status === \App\Enums\BookingStatus::PendingPayment) bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-600
                @endif">
                {{ $booking->status->label() }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Kamar</p>
                <p class="font-semibold">{{ $booking->room_type_name_snapshot }}</p>
                <p class="text-sm text-gray-600">{{ $booking->room_name_snapshot }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Jumlah Tamu</p>
                <p class="font-semibold">{{ $booking->guest_count }} orang</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Check-in</p>
                <p class="font-semibold">{{ $booking->check_in->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Check-out</p>
                <p class="font-semibold">{{ $booking->check_out->format('d M Y') }}</p>
            </div>
        </div>

        <hr>

        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Harga per malam</span>
                <span>Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">{{ $booking->nights }} malam</span>
                <span>Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($booking->promotion_discount > 0)
            <div class="flex justify-between text-sm text-green-600">
                <span>Diskon promo</span>
                <span>-Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-lg pt-2 border-t">
                <span>Total</span>
                <span>{{ $booking->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold mb-3">Riwayat</h2>
        <div class="space-y-2">
            @foreach($booking->statusHistories as $history)
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-medium">{{ $history->to_status }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
