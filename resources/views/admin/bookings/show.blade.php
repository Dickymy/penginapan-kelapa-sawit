@extends('layouts.admin')

@section('title', 'Detail Booking ' . $booking->booking_code)

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $booking->booking_code }}</h1>
            <p class="text-sm text-gray-500 mt-1">Dibuat: {{ $booking->created_at->format('d M Y, H:i') }} WITA</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Status & Payment Badge --}}
    <div class="flex flex-wrap gap-3">
        <x-status-badge :status="$booking->status" />
        @if($booking->payment_status)
            <x-status-badge :status="$booking->payment_status" />
        @endif
        @if($booking->needs_attention)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                ⚠ Perlu Perhatian
            </span>
        @endif
    </div>

    {{-- Info --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Booking</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Tamu</p>
                <p class="font-semibold text-gray-800">{{ $booking->guest_name }}</p>
                <p class="text-sm text-gray-600">{{ $booking->guest_whatsapp }}</p>
                @if($booking->guest_email)
                    <p class="text-sm text-gray-600">{{ $booking->guest_email }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500">Kamar</p>
                <p class="font-semibold text-gray-800">{{ $booking->room_name_snapshot }}</p>
                <p class="text-sm text-gray-600">{{ $booking->room_type_name_snapshot }}</p>
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
            <div>
                <p class="text-xs text-gray-500">Jumlah Tamu</p>
                <p class="font-semibold text-gray-800">{{ $booking->guest_count }} orang</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Sumber</p>
                <p class="font-semibold text-gray-800">{{ $booking->source->label() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Pembayaran</p>
                <p class="font-bold text-lg text-gray-800">{{ $booking->formatted_total }}</p>
            </div>
        </div>

        @if($booking->special_request)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500">Permintaan Khusus</p>
                <p class="text-sm text-gray-700 mt-1">{{ $booking->special_request }}</p>
            </div>
        @endif

        @if($booking->internal_notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500">Catatan Internal</p>
                <p class="text-sm text-gray-700 mt-1">{{ $booking->internal_notes }}</p>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6" x-data="{ showCancelForm: false }">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Aksi</h2>
        <div class="flex flex-wrap gap-3">
            @if($booking->status === \App\Enums\BookingStatus::Confirmed)
                <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Check-in
                    </button>
                </form>
                <button type="button"
                        @click="$dispatch('open-confirm', { id: 'no-show-confirm' })"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">
                    No-show
                </button>
            @endif

            @if($booking->status === \App\Enums\BookingStatus::CheckedIn)
                <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Check-out
                    </button>
                </form>
            @endif

            @if($booking->status === \App\Enums\BookingStatus::CheckedOut)
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Selesaikan
                    </button>
                </form>
            @endif

            @if($booking->status->canTransitionTo(\App\Enums\BookingStatus::Cancelled))
                <button type="button"
                        @click="showCancelForm = !showCancelForm"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Batalkan Booking
                </button>
            @endif
        </div>

        {{-- Cancel Form (inline) --}}
        <div x-show="showCancelForm" x-transition x-cloak class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="space-y-3" x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PATCH')
                <p class="text-sm font-medium text-red-800">Batalkan booking ini?</p>
                <p class="text-xs text-red-700">Tindakan ini akan membatalkan reservasi. Booking yang sudah dibayar tidak akan otomatis direfund.</p>
                <div>
                    <input type="text" name="reason" placeholder="Alasan pembatalan (wajib)" required
                           class="w-full rounded-lg border-red-300 text-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="showCancelForm = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Kembali
                    </button>
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Ya, Batalkan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Riwayat Status</h2>
        <div class="space-y-3">
            @foreach($booking->statusHistories as $history)
            <div class="flex items-start gap-3 text-sm">
                <span class="text-gray-400 flex-shrink-0 w-28">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-medium text-gray-800">{{ $history->to_status }}</span>
                @if($history->reason)
                    <span class="text-gray-500">— {{ $history->reason }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- No-show Confirmation Modal --}}
@if($booking->status === \App\Enums\BookingStatus::Confirmed)
<x-confirm-modal
    id="no-show-confirm"
    title="Tandai No-show?"
    message="Tamu tidak hadir dan booking akan ditandai sebagai no-show. Tindakan ini tidak dapat dibatalkan."
    confirm-text="Ya, Tandai No-show"
    cancel-text="Batal"
    variant="warning"
    :form-action="route('admin.bookings.no-show', $booking)"
    method="PATCH"
/>
@endif
@endsection
