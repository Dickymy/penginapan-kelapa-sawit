@extends('layouts.admin')

@section('title', 'Block Kamar Baru')

@section('content')
<div class="max-w-xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Block Kamar Baru</h1>

    <form method="POST" action="{{ route('admin.room-blocks.store') }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

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

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="start_date" />
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                <x-form-error field="end_date" />
            </div>
        </div>

        <div>
            <label for="reason_type" class="block text-sm font-medium text-gray-700">Tipe Alasan</label>
            <select name="reason_type" id="reason_type" required class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">Pilih</option>
                <option value="maintenance" @selected(old('reason_type') === 'maintenance')>Maintenance</option>
                <option value="renovation" @selected(old('reason_type') === 'renovation')>Renovasi</option>
                <option value="reserved" @selected(old('reason_type') === 'reserved')>Reserved (owner)</option>
                <option value="other" @selected(old('reason_type') === 'other')>Lainnya</option>
            </select>
            <x-form-error field="reason_type" />
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700">Alasan</label>
            <input type="text" name="reason" id="reason" value="{{ old('reason') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300">
            <x-form-error field="reason" />
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (opsional)</label>
            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
            <x-form-error field="notes" />
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.room-blocks.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">Simpan Block</button>
        </div>
    </form>
</div>
@endsection
