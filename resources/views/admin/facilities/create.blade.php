@extends('layouts.admin')

@section('title', 'Tambah Fasilitas - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Fasilitas</h1>
    <a href="{{ route('admin.facilities.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<form action="{{ route('admin.facilities.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl space-y-5">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="name" />
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400">(opsional)</span></label>
        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="slug" />
    </div>

    <div>
        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon <span class="text-gray-400">(nama icon/class)</span></label>
        <input type="text" name="icon" id="icon" value="{{ old('icon') }}" placeholder="wifi, tv, ac" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="icon" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
        <x-form-error field="description" />
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
