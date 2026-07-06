@extends('layouts.admin')

@section('title', 'Buat Kebijakan - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Buat Kebijakan Baru</h1>
    <a href="{{ route('admin.policies.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<form action="{{ route('admin.policies.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-3xl space-y-5">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="policy_key" class="block text-sm font-medium text-gray-700 mb-1">Policy Key <span class="text-red-500">*</span></label>
            <input type="text" name="policy_key" id="policy_key" value="{{ old('policy_key', 'guest_policy') }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="policy_key" />
        </div>
        <div>
            <label for="version" class="block text-sm font-medium text-gray-700 mb-1">Versi <span class="text-red-500">*</span></label>
            <input type="text" name="version" id="version" value="{{ old('version') }}" required placeholder="1.0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="version" />
        </div>
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="title" />
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-500">*</span></label>
        <textarea name="content" id="content" rows="10" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('content') }}</textarea>
        <x-form-error field="content" />
    </div>

    <div class="pt-4">
        <x-button type="submit">Simpan</x-button>
    </div>
</form>
@endsection
