@extends('layouts.member')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h1>

    <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-5 bg-white p-6 rounded-lg shadow-sm border border-gray-100" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 @enderror">
            <x-form-error field="name" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" value="{{ $user->email }}" disabled
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
            <p class="mt-1 text-xs text-gray-500">Email tidak dapat diubah.</p>
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-medium text-gray-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" required
                   placeholder="08xxxxxxxxxx"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('whatsapp') border-red-300 @enderror">
            <p class="mt-1 text-xs text-gray-500">Format: 08xxxxxxxxxx</p>
            <x-form-error field="whatsapp" />
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!submitting">Simpan Perubahan</span>
                <span x-show="submitting" x-cloak>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
@endsection
