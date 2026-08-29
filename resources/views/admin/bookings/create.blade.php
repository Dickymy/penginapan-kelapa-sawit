@extends('layouts.admin')

@section('title', 'Booking Manual')
@section('page-title', 'Booking Manual')

@section('content')
<div class="" x-data="{
    rooms: {{ Js::from($roomsJson) }},
    selectedRoom: '{{ old('room_id', '') }}',
    pricePerNight: {{ old('price_per_night', 0) }},
    checkIn: '{{ old('check_in', date('Y-m-d')) }}',
    checkOut: '{{ old('check_out', date('Y-m-d', strtotime('+1 day'))) }}',
    get nights() {
        if (!this.checkIn || !this.checkOut) return 0;
        const diff = new Date(this.checkOut) - new Date(this.checkIn);
        return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
    },
    get estimatedTotal() {
        return this.nights * this.pricePerNight;
    },
    get selectedRoomData() {
        return this.rooms.find(r => r.id == this.selectedRoom);
    },
    selectRoom() {
        const room = this.selectedRoomData;
        if (room && this.pricePerNight === 0) {
            this.pricePerNight = room.base_price;
        }
    },
    adjustCheckOut() {
        if (this.checkOut <= this.checkIn) {
            const next = new Date(this.checkIn);
            next.setDate(next.getDate() + 1);
            this.checkOut = next.toISOString().split('T')[0];
        }
    }
}" x-init="$watch('checkIn', () => adjustCheckOut())">
    {{-- Back --}}
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Reservasi
    </a>

    <h1 class="text-xl font-bold text-gray-800 mb-1">Booking Manual</h1>
    <p class="text-sm text-gray-500 mb-6">Buat reservasi untuk tamu dari WhatsApp, telepon, walk-in, atau OTA.</p>

    <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        {{-- Section 1: Waktu & Kamar --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">1</span>
                Waktu & Kamar
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" name="check_in" id="check_in" x-model="checkIn" required min="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="check_in" />
                </div>
                <div>
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" name="check_out" id="check_out" x-model="checkOut" :min="checkIn" required
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="check_out" />
                </div>
            </div>

            <p class="text-xs text-gray-500" x-show="nights > 0" x-cloak>
                <span x-text="nights"></span> malam
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700">Kamar</label>
                    <select name="room_id" id="room_id" required x-model="selectedRoom" @change="selectRoom()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
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
                    <label for="guest_count" class="block text-sm font-medium text-gray-700">Jumlah Tamu</label>
                    <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count', 1) }}" min="1" required
                           :max="selectedRoomData?.capacity || 10"
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1" x-show="selectedRoomData" x-cloak>
                        Kapasitas: <span x-text="selectedRoomData?.capacity"></span> orang
                    </p>
                    <x-form-error field="guest_count" />
                </div>
            </div>
        </div>

        {{-- Section 2: Data Tamu --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">2</span>
                Data Tamu
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="guest_name" class="block text-sm font-medium text-gray-700">Nama Tamu</label>
                    <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="guest_name" />
                </div>
                <div>
                    <label for="guest_whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                    <input type="tel" name="guest_whatsapp" id="guest_whatsapp" value="{{ old('guest_whatsapp') }}" required
                           inputmode="tel" placeholder="08xxxxxxxxxx"
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Format bebas, sistem akan normalisasi otomatis.</p>
                    <x-form-error field="guest_whatsapp" />
                </div>
                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="guest_email" />
                </div>
            </div>
        </div>

        {{-- Section 3: Sumber & Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">3</span>
                Sumber & Pembayaran
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700">Sumber Booking</label>
                    <select name="source" id="source" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @foreach($sources as $source)
                            <option value="{{ $source->value }}" @selected(old('source', 'whatsapp') === $source->value)>{{ $source->label() }}</option>
                        @endforeach
                    </select>
                    <x-form-error field="source" />
                </div>
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                    <select name="payment_status" id="payment_status" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="unpaid" @selected(old('payment_status', 'unpaid') === 'unpaid')>Belum Bayar</option>
                        <option value="paid" @selected(old('payment_status') === 'paid')>Sudah Bayar</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Jika sudah bayar, reservasi akan langsung dikonfirmasi.</p>
                    <x-form-error field="payment_status" />
                </div>
                <div>
                    <label for="price_per_night" class="block text-sm font-medium text-gray-700">Harga per Malam (IDR)</label>
                    <input type="number" name="price_per_night" id="price_per_night" x-model="pricePerNight" min="0" required
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1" x-show="selectedRoomData" x-cloak>
                        Harga dasar: Rp<span x-text="(selectedRoomData?.base_price || 0).toLocaleString('id-ID')"></span>
                    </p>
                    <x-form-error field="price_per_night" />
                </div>
            </div>

            <div>
                <label for="internal_notes" class="block text-sm font-medium text-gray-700">Catatan Internal <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="internal_notes" id="internal_notes" rows="2"
                          class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Catatan untuk admin, tidak terlihat oleh tamu.">{{ old('internal_notes') }}</textarea>
                <x-form-error field="internal_notes" />
            </div>
        </div>

        {{-- Estimated Total --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4" x-show="nights > 0 && pricePerNight > 0" x-cloak>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Estimasi Total</span>
                <span class="text-lg font-bold text-gray-800">
                    Rp<span x-text="estimatedTotal.toLocaleString('id-ID')"></span>
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1"><span x-text="nights"></span> malam × Rp<span x-text="parseInt(pricePerNight).toLocaleString('id-ID')"></span></p>
        </div>

        {{-- Submit --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-center">Batal</a>
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-show="!loading">Buat Reservasi</span>
                <span x-show="loading" x-cloak>Membuat Reservasi...</span>
            </button>
        </div>
    </form>
</div>
@endsection
