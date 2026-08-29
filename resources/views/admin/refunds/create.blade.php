@extends('layouts.admin')

@section('title', 'Buat Refund')

@section('content')
<div class="mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Buat Refund</h1>
        <p class="text-gray-600 mt-1">Booking: {{ $booking->booking_code }} — {{ $booking->guest_name }}</p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600">Total Booking:</span>
                    <span class="ml-2">Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Pembayaran:</span>
                    <span class="ml-2">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Maksimal Refund:</span>
                    <span class="ml-2 font-bold text-green-700">Rp{{ number_format($maxRefundable, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.refunds.store', $booking) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Jumlah Refund <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                    min="1" max="{{ $maxRefundable }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    required>
                <p class="text-xs text-gray-500 mt-1">Maksimal: Rp{{ number_format($maxRefundable, 0, ',', '.') }}</p>
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Alasan Refund <span class="text-red-500">*</span>
                </label>
                <input type="text" name="reason" id="reason" value="{{ old('reason') }}"
                    maxlength="255"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Contoh: Tamu membatalkan karena alasan darurat"
                    required>
                @error('reason')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan (opsional)
                </label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Catatan internal tambahan...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    Simpan Refund
                </button>
                <a href="{{ route('admin.bookings.show', $booking) }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
