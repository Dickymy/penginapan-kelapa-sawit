@extends('layouts.admin')

@section('title', 'Detail Booking ' . $booking->booking_code)

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Booking {{ $booking->booking_code }}</h1>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    {{-- Info --}}
    <div class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-semibold">{{ $booking->status->label() }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Sumber</p>
            <p class="font-semibold">{{ $booking->source->label() }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Tamu</p>
            <p class="font-semibold">{{ $booking->guest_name }}</p>
            <p class="text-sm text-gray-600">{{ $booking->guest_whatsapp }}</p>
            @if($booking->guest_email)
                <p class="text-sm text-gray-600">{{ $booking->guest_email }}</p>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500">Kamar</p>
            <p class="font-semibold">{{ $booking->room_name_snapshot }} ({{ $booking->room_type_name_snapshot }})</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Check-in</p>
            <p class="font-semibold">{{ $booking->check_in->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Check-out</p>
            <p class="font-semibold">{{ $booking->check_out->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Malam</p>
            <p class="font-semibold">{{ $booking->nights }} malam</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total</p>
            <p class="font-semibold text-lg">{{ $booking->formatted_total }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Aksi</h2>
        <div class="flex flex-wrap gap-3">
            @if($booking->status === \App\Enums\BookingStatus::Confirmed)
                <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Check-in</button>
                </form>
                <form method="POST" action="{{ route('admin.bookings.no-show', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md text-sm hover:bg-yellow-700">No-show</button>
                </form>
            @endif

            @if($booking->status === \App\Enums\BookingStatus::CheckedIn)
                <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Check-out</button>
                </form>
            @endif

            @if($booking->status === \App\Enums\BookingStatus::CheckedOut)
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Selesai</button>
                </form>
            @endif

            @if($booking->status->canTransitionTo(\App\Enums\BookingStatus::Cancelled))
                <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" x-data="{ showReason: false }">
                    @csrf @method('PATCH')
                    <button type="button" @click="showReason = true" x-show="!showReason" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Batalkan</button>
                    <div x-show="showReason" class="flex gap-2 items-end">
                        <input type="text" name="reason" placeholder="Alasan pembatalan" required class="rounded-md border-gray-300 text-sm">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Konfirmasi Batal</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Riwayat Status</h2>
        <div class="space-y-2">
            @foreach($booking->statusHistories as $history)
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-medium">{{ $history->to_status }}</span>
                @if($history->reason)
                    <span class="text-gray-500">— {{ $history->reason }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
