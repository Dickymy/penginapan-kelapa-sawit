@extends('layouts.member')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-lg space-y-8">
    <h1 class="text-2xl font-bold text-gray-800">Edit Profil</h1>

    {{-- Update Profile Form --}}
    <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-5 bg-white p-6 rounded-lg shadow-sm border border-gray-100" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-semibold text-gray-800">Informasi Akun</h2>

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
            <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" required
                   inputmode="tel"
                   placeholder="08xxxxxxxxxx"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('whatsapp') border-red-300 @enderror">
            <p class="mt-1 text-xs text-gray-500">Format: 08xx, 628xx, atau +628xx</p>
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

    {{-- Update Password Form --}}
    <form method="POST" action="{{ route('user-password.update') }}" class="space-y-5 bg-white p-6 rounded-lg shadow-sm border border-gray-100" x-data="{ submitting: false, password: '', confirmation: '' }" @submit="submitting = true">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-semibold text-gray-800">Ubah Kata Sandi</h2>
        <p class="text-sm text-gray-500">Pastikan kata sandi baru berbeda dari kata sandi saat ini.</p>

        @if($errors->updatePassword->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->updatePassword->all() as $error)
                        <li class="flex items-start gap-1.5">
                            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" id="current_password" required
                   autocomplete="current-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>

        <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru <span class="text-red-500">*</span></label>
            <input type="password" name="password" id="new_password" required x-model="password"
                   autocomplete="new-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <div class="mt-2 space-y-1">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" :class="password.length >= 8 ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-xs" :class="password.length >= 8 ? 'text-green-700' : 'text-gray-500'">Minimal 8 karakter</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" :class="/[A-Z]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-xs" :class="/[A-Z]/.test(password) ? 'text-green-700' : 'text-gray-500'">Memiliki huruf besar</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" :class="/[a-z]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-xs" :class="/[a-z]/.test(password) ? 'text-green-700' : 'text-gray-500'">Memiliki huruf kecil</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" :class="/[0-9]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-xs" :class="/[0-9]/.test(password) ? 'text-green-700' : 'text-gray-500'">Memiliki angka</span>
                </div>
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" required x-model="confirmation"
                   autocomplete="new-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <div class="mt-1.5" x-show="confirmation.length > 0" x-cloak>
                <p class="text-xs flex items-center gap-1.5"
                   :class="confirmation === password ? 'text-green-600' : 'text-red-500'">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" x-show="confirmation === password">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" x-show="confirmation !== password" x-cloak>
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="confirmation === password ? 'Kata sandi cocok' : 'Konfirmasi kata sandi belum sama'"></span>
                </p>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!submitting">Ubah Kata Sandi</span>
                <span x-show="submitting" x-cloak>Mengubah...</span>
            </button>
        </div>
    </form>
</div>
@endsection
