@extends('layouts.member')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <x-form-error field="name" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" value="{{ $user->email }}" disabled
                   class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-500">
            <p class="mt-1 text-xs text-gray-500">Email tidak dapat diubah.</p>
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <x-form-error field="whatsapp" />
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
