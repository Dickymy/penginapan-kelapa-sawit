@extends('layouts.public')

@section('title', 'Cek Booking - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
        <div class="text-center mb-6">
            <div class="mx-auto w-12 h-12 bg-primary-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Cek Status Booking</h1>
            <p class="text-gray-500 text-sm mt-2">Masukkan kode booking dan data verifikasi Anda.</p>
        </div>

        {{-- Error Display --}}
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        <form action="{{ route('booking.verify') }}" method="POST" class="space-y-5" x-data="{ method: '{{ old('access_token') ? 'token' : (old('guest_email') || old('guest_whatsapp') ? 'contact' : 'token') }}' }">
            @csrf

            {{-- Booking Code --}}
            <div>
                <label for="booking_code" class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Booking <span class="text-red-500">*</span>
                </label>
                <input type="text" name="booking_code" id="booking_code"
                       value="{{ old('booking_code') }}"
                       required
                       placeholder="Contoh: BKG-202607-0001"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 font-mono">
                <x-form-error field="booking_code" />
            </div>

            {{-- Verification Method Tabs --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Verifikasi</label>
                <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                    <button type="button"
                            @click="method = 'token'"
                            :class="method === 'token' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="flex-1 py-2.5 px-4 text-sm font-medium transition">
                        Token Akses
                    </button>
                    <button type="button"
                            @click="method = 'contact'"
                            :class="method === 'contact' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="flex-1 py-2.5 px-4 text-sm font-medium transition border-l border-gray-200">
                        Email / WhatsApp
                    </button>
                </div>
            </div>

            {{-- Token Verification --}}
            <div x-show="method === 'token'" x-transition>
                <label for="access_token" class="block text-sm font-medium text-gray-700 mb-1">
                    Token Akses
                </label>
                <input type="text" name="access_token" id="access_token"
                       value="{{ old('access_token') }}"
                       placeholder="Masukkan token yang Anda terima saat booking"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <p class="mt-1 text-xs text-gray-500">Token diberikan saat booking berhasil dibuat.</p>
                <x-form-error field="access_token" />
            </div>

            {{-- Email/WhatsApp Verification --}}
            <div x-show="method === 'contact'" x-transition x-cloak class="space-y-4">
                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="email" name="guest_email" id="guest_email"
                           value="{{ old('guest_email') }}"
                           placeholder="Email yang digunakan saat booking"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="guest_email" />
                </div>
                <div>
                    <label for="guest_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                        No. WhatsApp
                    </label>
                    <input type="text" name="guest_whatsapp" id="guest_whatsapp"
                           value="{{ old('guest_whatsapp') }}"
                           placeholder="No. WhatsApp yang digunakan saat booking"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <x-form-error field="guest_whatsapp" />
                </div>
                <p class="text-xs text-gray-500">Isi salah satu: email atau nomor WhatsApp.</p>
            </div>

            {{-- Submit --}}
            <div x-data="{ submitting: false }">
                <button type="submit"
                        @click="if ($el.form.checkValidity()) { submitting = true; }"
                        :disabled="submitting"
                        class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition inline-flex items-center justify-center disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!submitting">Cek Status</span>
                    <span x-show="submitting" x-cloak>Memeriksa...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
