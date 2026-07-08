@extends('layouts.admin')

@section('title', 'Detail ' . $booking->booking_code)
@section('page-title', 'Detail Reservasi')

@section('content')
<div class="max-w-4xl space-y-4">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg md:text-xl font-bold text-gray-800">{{ $booking->booking_code }}</h1>
                @if($booking->needs_attention)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">⚠</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-0.5">{{ $booking->created_at->translatedFormat('d F Y, H:i') }} WITA</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="hidden sm:inline">Kembali</span>
        </a>
    </div>

    {{-- Status Badges --}}
    <div class="flex flex-wrap gap-2">
        <x-status-badge :status="$booking->status" />
        <x-status-badge :status="$booking->payment_status" />
    </div>

    {{-- Guest Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Informasi Tamu</h2>
        <div class="space-y-2">
            <p class="text-base font-semibold text-gray-800">{{ $booking->guest_name }}</p>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600">
                @if($booking->guest_whatsapp)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        {{ $booking->guest_whatsapp }}
                    </span>
                @endif
                @if($booking->guest_email)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $booking->guest_email }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stay Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Informasi Menginap</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div>
                <p class="text-xs text-gray-400">Kamar</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->room_name_snapshot }}</p>
                <p class="text-xs text-gray-500">{{ $booking->room_type_name_snapshot }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Check-in</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->check_in->translatedFormat('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Check-out</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->check_out->translatedFormat('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Durasi</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->nights }} malam</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Tamu</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->guest_count }} orang</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Sumber</p>
                <p class="text-sm font-semibold text-gray-800">{{ $booking->source->label() }}</p>
            </div>
        </div>
    </div>

    {{-- Payment --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Pembayaran</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Harga per malam</span>
                <span class="text-gray-700">Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Subtotal ({{ $booking->nights }} malam)</span>
                <span class="text-gray-700">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($booking->promotion_discount > 0)
            <div class="flex justify-between text-green-600">
                <span>Diskon promo</span>
                <span>-Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($booking->points_discount > 0)
            <div class="flex justify-between text-green-600">
                <span>Potongan poin</span>
                <span>-Rp{{ number_format($booking->points_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between pt-2 border-t border-gray-100 font-bold">
                <span class="text-gray-800">Total</span>
                <span class="text-gray-800">{{ $booking->formatted_total }}</span>
            </div>
        </div>
    </div>

    @if($booking->special_request || $booking->internal_notes)
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        @if($booking->special_request)
        <div class="mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Permintaan Khusus</p>
            <p class="text-sm text-gray-700">{{ $booking->special_request }}</p>
        </div>
        @endif
        @if($booking->internal_notes)
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Catatan Internal</p>
            <p class="text-sm text-gray-700">{{ $booking->internal_notes }}</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Actions (Desktop) --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 p-4 md:p-5" x-data="{ showCancelForm: false }">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Aksi</h2>
        <div class="flex flex-wrap gap-2">
            @if($booking->status === \App\Enums\BookingStatus::Confirmed)
                <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        <span x-text="loading ? 'Memproses...' : 'Check-in'"></span>
                    </button>
                </form>
                <button type="button" @click="$dispatch('open-confirm', { id: 'no-show-confirm' })" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">No-show</button>
            @endif
            @if($booking->status === \App\Enums\BookingStatus::CheckedIn)
                <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        <span x-text="loading ? 'Memproses...' : 'Check-out'"></span>
                    </button>
                </form>
            @endif
            @if($booking->status === \App\Enums\BookingStatus::CheckedOut)
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf @method('PATCH')
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        <span x-text="loading ? 'Memproses...' : 'Selesaikan'"></span>
                    </button>
                </form>
            @endif
            @if($booking->status->canTransitionTo(\App\Enums\BookingStatus::Cancelled))
                <button type="button" @click="showCancelForm = !showCancelForm" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition">Batalkan</button>
            @endif
        </div>
        {{-- Cancel Form --}}
        <div x-show="showCancelForm" x-transition x-cloak class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="space-y-3" x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PATCH')
                <p class="text-sm font-medium text-red-800">Batalkan reservasi ini?</p>
                <input type="text" name="reason" placeholder="Alasan pembatalan (wajib)" required class="w-full rounded-lg border-red-300 text-sm focus:border-red-500 focus:ring-red-500">
                <div class="flex gap-2">
                    <button type="button" @click="showCancelForm = false" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</button>
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-60">
                        <span x-text="loading ? 'Membatalkan...' : 'Ya, Batalkan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Riwayat</h2>
        <div class="space-y-3">
            @foreach($booking->statusHistories->sortByDesc('created_at') as $history)
            <div class="flex items-start gap-3 text-sm">
                <span class="w-2 h-2 rounded-full bg-gray-300 mt-1.5 flex-shrink-0"></span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-gray-800">{{ \App\Enums\BookingStatus::tryFrom($history->to_status)?->label() ?? $history->to_status }}</span>
                        <span class="text-xs text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($history->reason)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $history->reason }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Mobile Sticky Action Bar --}}
@php
    $hasMobileAction = in_array($booking->status->value, ['confirmed', 'checked_in', 'checked_out']);
@endphp
@if($hasMobileAction)
<div class="fixed bottom-14 inset-x-0 z-20 md:hidden bg-white border-t border-gray-200 px-4 py-3 safe-area-bottom" x-data="{ showCancelMobile: false }">
    @if($booking->status === \App\Enums\BookingStatus::Confirmed)
        <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf @method('PATCH')
            <button type="submit" :disabled="loading" class="w-full py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:opacity-60 inline-flex items-center justify-center">
                <svg x-show="loading" x-cloak class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-text="loading ? 'Memproses Check-in...' : 'Check-in Tamu'"></span>
            </button>
        </form>
    @elseif($booking->status === \App\Enums\BookingStatus::CheckedIn)
        <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf @method('PATCH')
            <button type="submit" :disabled="loading" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-60 inline-flex items-center justify-center">
                <svg x-show="loading" x-cloak class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-text="loading ? 'Memproses Check-out...' : 'Check-out Tamu'"></span>
            </button>
        </form>
    @elseif($booking->status === \App\Enums\BookingStatus::CheckedOut)
        <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf @method('PATCH')
            <button type="submit" :disabled="loading" class="w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 disabled:opacity-60 inline-flex items-center justify-center">
                <svg x-show="loading" x-cloak class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-text="loading ? 'Memproses...' : 'Selesaikan Reservasi'"></span>
            </button>
        </form>
    @endif
</div>
@endif

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
