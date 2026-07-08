@extends('layouts.admin')

@section('title', 'Block Kamar Baru')
@section('page-title', 'Blokir Kamar Baru')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('admin.room-blocks.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <h1 class="text-xl font-bold text-gray-800 mb-1">Blokir Kamar</h1>
    <p class="text-sm text-gray-500 mb-6">Kamar yang diblokir tidak akan tersedia untuk reservasi selama periode ini.</p>

    <form method="POST" action="{{ route('admin.room-blocks.store') }}" class="space-y-5 bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        @csrf

        <div>
            <label for="room_id" class="block text-sm font-medium text-gray-700">Kamar</label>
            <select name="room_id" id="room_id" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Pilih Kamar</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                        {{ $room->name }} ({{ $room->roomType->name }})
                    </option>
                @endforeach
            </select>
            <x-form-error field="room_id" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <x-form-error field="start_date" />
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Selesai</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <x-form-error field="end_date" />
            </div>
        </div>

        <div>
            <label for="reason_type" class="block text-sm font-medium text-gray-700">Tipe Alasan</label>
            <select name="reason_type" id="reason_type" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Pilih</option>
                <option value="maintenance" @selected(old('reason_type') === 'maintenance')>Maintenance / Perbaikan</option>
                <option value="renovation" @selected(old('reason_type') === 'renovation')>Renovasi</option>
                <option value="reserved" @selected(old('reason_type') === 'reserved')>Penggunaan Internal</option>
                <option value="other" @selected(old('reason_type') === 'other')>Lainnya</option>
            </select>
            <x-form-error field="reason_type" />
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <input type="text" name="reason" id="reason" value="{{ old('reason') }}" required maxlength="255" placeholder="Contoh: Perbaikan AC kamar"
                   class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            <x-form-error field="reason" />
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
            <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
            <x-form-error field="notes" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
            <a href="{{ route('admin.room-blocks.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-center">Batal</a>
            <button type="submit" x-data="{ loading: false }" @click="if ($el.form.checkValidity()) { loading = true; }" :disabled="loading"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-60">
                <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                <span x-show="!loading">Blokir Kamar</span>
                <span x-show="loading" x-cloak>Memproses...</span>
            </button>
        </div>
    </form>
</div>
@endsection
