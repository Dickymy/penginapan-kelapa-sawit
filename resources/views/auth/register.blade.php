@extends('layouts.public')

@section('title', 'Daftar - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    {{-- Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] p-6 md:p-8">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Penginapan Kelapa Sawit" class="h-16 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Akun</h1>
            <p class="text-sm text-gray-500 mt-1">Buat akun untuk menikmati manfaat member: histori, poin, dan autofill data.</p>
        </div>

        @if ($errors->any())
            <x-alert type="error" message="Beberapa data belum benar. Silakan periksa kembali." />
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ submitting: false, password: '', confirmation: '' }" @submit="submitting = true">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                       autocomplete="name"
                       placeholder="Nama lengkap Anda"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 @enderror">
                <x-form-error field="name" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       autocomplete="email"
                       placeholder="nama@email.com"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-300 @enderror">
                <x-form-error field="email" />
            </div>

            <div>
                <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" required
                       autocomplete="tel"
                       inputmode="tel"
                       placeholder="08xxxxxxxxxx"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('whatsapp') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">Format: 08xx, 628xx, atau +628xx</p>
                <x-form-error field="whatsapp" />
            </div>

            <div>
                <x-password-input name="password" label="Kata sandi" :required="true" :show-hints="true" autocomplete="new-password" />
            </div>

            <div x-data="{ confirmVal: '' }">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi kata sandi <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="password_confirmation"
                           id="password_confirmation"
                           required
                           x-model="confirmVal"
                           autocomplete="new-password"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 pr-10">
                </div>
                <div class="mt-1.5" x-show="confirmVal.length > 0" x-cloak>
                    <p class="text-xs flex items-center gap-1.5"
                       :class="confirmVal === password ? 'text-green-600' : 'text-red-500'">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"
                             x-show="confirmVal === password">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"
                             x-show="confirmVal !== password" x-cloak>
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="confirmVal === password ? 'Kata sandi cocok' : 'Konfirmasi kata sandi belum sama'"></span>
                    </p>
                </div>
                <x-form-error field="password_confirmation" />
            </div>

            <p class="text-xs text-gray-400">Contoh pola: <code class="bg-gray-100 px-1 py-0.5 rounded">SawitAman2026!</code> — jangan gunakan contoh ini secara langsung.</p>

            <button type="submit"
                    :disabled="submitting"
                    class="w-full inline-flex items-center justify-center px-4 py-3.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-500 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!submitting">Daftar</span>
                <span x-show="submitting" x-cloak>Mendaftarkan akun...</span>
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-800 font-medium">Masuk</a>
    </p>
</div>
@endsection
