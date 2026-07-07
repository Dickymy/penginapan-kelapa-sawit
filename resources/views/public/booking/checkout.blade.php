@extends('layouts.public')

@section('title', 'Checkout - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>

    {{-- Session Error --}}
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm font-medium text-red-800 mb-2">Beberapa data belum benar:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Hidden Fields --}}
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <input type="hidden" name="check_in" value="{{ $checkIn }}">
                <input type="hidden" name="check_out" value="{{ $checkOut }}">
                <input type="hidden" name="guest_count" value="{{ $guestCount }}">
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

                {{-- Guest Info --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tamu</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nama --}}
                        <div class="md:col-span-2">
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="guest_name" id="guest_name"
                                   value="{{ old('guest_name', $user->name ?? '') }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <x-form-error field="guest_name" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="guest_email" id="guest_email"
                                   value="{{ old('guest_email', $user->email ?? '') }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <x-form-error field="guest_email" />
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label for="guest_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                                No. WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="guest_whatsapp" id="guest_whatsapp"
                                   value="{{ old('guest_whatsapp', $user->whatsapp ?? '') }}"
                                   required
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <x-form-error field="guest_whatsapp" />
                        </div>
                    </div>
                </div>

                {{-- Additional Info --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h2>

                    <div class="space-y-4">
                        {{-- Estimasi Kedatangan --}}
                        <div>
                            <label for="arrival_estimate" class="block text-sm font-medium text-gray-700 mb-1">
                                Estimasi Waktu Kedatangan
                            </label>
                            <input type="text" name="arrival_estimate" id="arrival_estimate"
                                   value="{{ old('arrival_estimate') }}"
                                   placeholder="Contoh: Sekitar jam 14:00"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <x-form-error field="arrival_estimate" />
                        </div>

                        {{-- Permintaan Khusus --}}
                        <div>
                            <label for="special_request" class="block text-sm font-medium text-gray-700 mb-1">
                                Permintaan Khusus
                            </label>
                            <textarea name="special_request" id="special_request" rows="3"
                                      placeholder="Contoh: Kamar di lantai bawah, extra bantal, dll."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('special_request') }}</textarea>
                            <x-form-error field="special_request" />
                        </div>
                    </div>
                </div>

                {{-- Policy Acceptance --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" name="policy_accepted" id="policy_accepted" value="1"
                               {{ old('policy_accepted') ? 'checked' : '' }}
                               required
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="policy_accepted" class="text-sm text-gray-700">
                            Saya telah membaca dan menyetujui
                            <a href="{{ route('policy') }}" target="_blank" class="text-primary-600 hover:underline">kebijakan penginapan</a>.
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <x-form-error field="policy_accepted" />
                </div>

                {{-- Submit --}}
                <div x-data="{ submitting: false }">
                    <button type="submit"
                            x-on:click="setTimeout(() => submitting = true, 50)"
                            :disabled="submitting"
                            class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition text-center disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                        <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!submitting">Pesan Sekarang</span>
                        <span x-show="submitting" x-cloak>Membuat booking...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Booking Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Booking</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipe Kamar</span>
                        <span class="font-medium text-gray-900">{{ $roomType->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check-in</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check-out</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Durasi</span>
                        <span class="font-medium text-gray-900">{{ $quote['nights'] }} malam</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tamu</span>
                        <span class="font-medium text-gray-900">{{ $guestCount }} orang</span>
                    </div>

                    <hr class="border-gray-200">

                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga per malam</span>
                        <span class="font-medium text-gray-900">Rp{{ number_format($quote['price_per_night'], 0, ',', '.') }}</span>
                    </div>

                    <hr class="border-gray-200">

                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary-600">Rp{{ number_format($quote['total_amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
