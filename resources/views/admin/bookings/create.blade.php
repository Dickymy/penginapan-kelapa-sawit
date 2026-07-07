@extends('layouts.admin')

@section('title', 'Booking Manual')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Manual</h1>

    <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-6 bg-white p-6 rounded-lg shadow">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-700">Kamar</label>
                <select name="room_id" id="room_id" required class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="">Pilih Kamar</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                            {{ $room->name }} ({{ $room->roomType->name }})
                        </option>
                    @endforeach
                </select>
                <x-form-error field="room_id" />
            </div>

            <div>
                <label for="source" class="block text-sm font-medium text-gray-700">Sumber</label>
                <select name="source" id="source" required class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($sources as $source)
                        <option value="{{ $source->value }}" @selected(old('source') === $source->value)>{{ $source->label() }}</option>
                    @endforeach
                </select>
                <x-form-error field="source" />
            </div>

            <div>
                <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in</label>
                <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="check_in" />
            </div>

            <div>
                <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out</label>
                <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="check_out" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="guest_name" class="block text-sm font-medium text-gray-700">Nama Tamu</label>
                <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="guest_name" />
            </div>

            <div>
                <label for="guest_whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                <input type="text" name="guest_whatsapp" id="guest_whatsapp" value="{{ old('guest_whatsapp') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="guest_whatsapp" />
            </div>

            <div>
                <label for="guest_email" class="block text-sm font-medium text-gray-700">Email (opsional)</label>
                <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="guest_email" />
            </div>

            <div>
                <label for="guest_count" class="block text-sm font-medium text-gray-700">Jumlah Tamu</label>
                <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count', 1) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="guest_count" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="price_per_night" class="block text-sm font-medium text-gray-700">Harga per Malam (IDR)</label>
                <input type="number" name="price_per_night" id="price_per_night" value="{{ old('price_per_night') }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="price_per_night" />
            </div>

            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                <select name="payment_status" id="payment_status" required class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="unpaid" @selected(old('payment_status') === 'unpaid')>Belum Bayar</option>
                    <option value="paid" @selected(old('payment_status') === 'paid')>Sudah Bayar</option>
                </select>
                <x-form-error field="payment_status" />
            </div>
        </div>

        <div>
            <label for="internal_notes" class="block text-sm font-medium text-gray-700">Catatan Internal (opsional)</label>
            <textarea name="internal_notes" id="internal_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('internal_notes') }}</textarea>
            <x-form-error field="internal_notes" />
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit"
                    x-data="{ loading: false }" @click="if ($el.form.checkValidity()) { loading = true; }" :disabled="loading"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-show="!loading">Simpan Booking</span>
                <span x-show="loading" x-cloak>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
@endsection
