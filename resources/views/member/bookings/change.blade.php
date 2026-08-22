@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('member.bookings.show', $booking) }}" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
            <svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Ajukan Perubahan Booking</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h2 class="font-semibold text-slate-800">Booking Saat Ini ({{ $booking->booking_code }})</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                <div>
                    <span class="text-slate-500 block">Check-in</span>
                    <span class="font-medium">{{ $booking->check_in->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Check-out</span>
                    <span class="font-medium">{{ $booking->check_out->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Tipe Kamar</span>
                    <span class="font-medium">{{ $booking->room->roomType->name }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('member.booking-changes.store', $booking) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Perubahan</label>
                <select name="type" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="reschedule" {{ old('type') == 'reschedule' ? 'selected' : '' }}>Reschedule (Ubah Tanggal)</option>
                    <option value="room_change" {{ old('type') == 'room_change' ? 'selected' : '' }}>Ubah Tipe Kamar</option>
                    <option value="guest_update" {{ old('type') == 'guest_update' ? 'selected' : '' }}>Ubah Jumlah Tamu</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check-in Baru</label>
                    <input type="date" name="check_in" value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}" 
                        min="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('check_in')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Baru</label>
                    <input type="date" name="check_out" value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}" 
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('check_out')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Kamar Baru</label>
                    <select name="room_type_id" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                        @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $booking->room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                {{ $roomType->name }} (Mulai Rp{{ number_format($roomType->base_price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Tamu Baru</label>
                    <input type="number" name="guest_count" value="{{ old('guest_count', $booking->guest_count) }}" 
                        min="1" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('guest_count')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-sm mt-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-2">
                    <p><strong>Penting:</strong></p>
                    <ul class="list-disc ml-4 space-y-1">
                        <li>Pengajuan perubahan akan ditinjau oleh Admin (proses manual).</li>
                        <li>Ketersediaan kamar dan selisih harga akan dihitung ulang secara sistem.</li>
                        <li>Jika harga tipe kamar atau tanggal baru lebih mahal, Anda harus membayar selisih biayanya.</li>
                        <li>Jika lebih murah, sistem akan memberikan refund sesuai kebijakan.</li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6 mt-6">
                <a href="{{ route('member.bookings.show', $booking) }}" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
