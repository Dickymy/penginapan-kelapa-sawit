@extends('layouts.public')

@section('title', 'Daftar - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Akun</h1>

    @if ($errors->any())
        <x-alert type="error" message="Beberapa data belum benar. Silakan periksa kembali." />
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                   placeholder="Nama lengkap Anda"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 @enderror">
            <x-form-error field="name" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   placeholder="nama@email.com"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-300 @enderror">
            <x-form-error field="email" />
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-medium text-gray-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" required placeholder="08xxxxxxxxxx"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('whatsapp') border-red-300 @enderror">
            <p class="mt-1 text-xs text-gray-500">Format: 08xxxxxxxxxx (tanpa spasi atau tanda baca)</p>
            <x-form-error field="whatsapp" />
        </div>

        <div>
            <x-password-input name="password" label="Kata Sandi" :required="true" :show-hints="true" autocomplete="new-password" />
        </div>

        <div>
            <x-password-input name="password_confirmation" label="Konfirmasi Kata Sandi" :required="true" autocomplete="new-password" />
        </div>

        <button type="submit"
                :disabled="submitting"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-show="!submitting">Daftar</span>
            <span x-show="submitting" x-cloak>Mendaftarkan akun...</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-800 font-medium">Masuk</a>
    </p>
</div>
@endsection
