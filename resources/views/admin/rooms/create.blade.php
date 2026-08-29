@extends('layouts.admin')

@section('title', 'Tambah Kamar - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Kamar</h1>
    <a href="{{ route('admin.rooms.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<form action="{{ route('admin.rooms.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-5">
    @csrf

    <div>
        <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kamar <span class="text-red-500">*</span></label>
        <select name="room_type_id" id="room_type_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <option value="">-- Pilih Tipe Kamar --</option>
            @foreach($roomTypes as $type)
                <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
        </select>
        <x-form-error field="room_type_id" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Kamar <span class="text-red-500">*</span></label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="code" />
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="name" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="floor" class="block text-sm font-medium text-gray-700 mb-1">Lantai</label>
            <input type="text" name="floor" id="floor" value="{{ old('floor') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="floor" />
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <x-form-error field="status" />
        </div>
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
        <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
        <x-form-error field="notes" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-700">Aktif</span>
            </label>
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="sort_order" />
        </div>
    </div>

    <div class="pt-4">
        <x-button type="submit">Simpan</x-button>
    </div>
</form>
@endsection
