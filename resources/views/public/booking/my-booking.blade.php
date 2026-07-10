@extends('layouts.public')

@section('title', 'Booking Saya - Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Lihat status booking dan pembayaran Anda di Penginapan Kelapa Sawit.">
@endsection

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="mx-auto w-14 h-14 bg-primary-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Booking Saya</h1>
                <p class="text-gray-500 text-sm mt-2">Lihat status booking dan pembayaran Anda.</p>
            </div>

            {{-- Error Display --}}
            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            {{-- Form --}}
            <form action="{{ route('booking.verify') }}" method="POST" class="space-y-5"
                  x-data="{ submitting: false }"
                  @submit.prevent="
                      if ($el.checkValidity()) {
                          submitting = true;
                          $el.submit();
                      } else {
                          $el.reportValidity();
                      }
                  ">
                @csrf

                {{-- Booking Code --}}
                <div>
                    <label for="booking_code" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kode Booking
                    </label>
                    <input type="text" name="booking_code" id="booking_code"
                           value="{{ old('booking_code') }}"
                           required
                           placeholder="Contoh: PKS-202607-0001"
                           autocomplete="off"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-base px-4 py-3 font-mono placeholder:font-sans placeholder:text-gray-400">
                    <p class="mt-1.5 text-xs text-gray-500">Kode booking diberikan saat pemesanan berhasil.</p>
                    <x-form-error field="booking_code" />
                </div>

                {{-- WhatsApp Number --}}
                <div>
                    <label for="guest_whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor WhatsApp
                    </label>
                    <input type="tel" name="guest_whatsapp" id="guest_whatsapp"
                           value="{{ old('guest_whatsapp') }}"
                           required
                           placeholder="Contoh: 08123456789"
                           autocomplete="tel"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-base px-4 py-3 placeholder:text-gray-400">
                    <p class="mt-1.5 text-xs text-gray-500">Nomor WhatsApp yang digunakan saat memesan.</p>
                    <x-form-error field="guest_whatsapp" />
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        :disabled="submitting"
                        class="w-full bg-primary-600 text-white py-3.5 px-6 rounded-xl font-semibold text-base hover:bg-primary-700 transition inline-flex items-center justify-center disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!submitting">Lihat Booking Saya</span>
                    <span x-show="submitting" x-cloak>Mencari...</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-white text-gray-400">atau</span>
                </div>
            </div>

            {{-- Login CTA --}}
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-3">Sudah memiliki akun?</p>
                <a href="{{ route('login') }}"
                   class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Masuk untuk melihat semua booking
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
