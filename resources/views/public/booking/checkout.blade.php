@extends('layouts.public')

@section('title', 'Checkout - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Progress --}}
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-2">Langkah 2 dari 4</p>
        <div class="flex items-center gap-1">
            <div class="h-1 flex-1 rounded bg-primary-600"></div>
            <div class="h-1 flex-1 rounded bg-primary-600"></div>
            <div class="h-1 flex-1 rounded bg-gray-200"></div>
            <div class="h-1 flex-1 rounded bg-gray-200"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>Cari Kamar</span>
            <span class="text-primary-600 font-medium">Data Tamu</span>
            <span>Konfirmasi</span>
            <span>Pembayaran</span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
        <a href="{{ route('availability.search', ['check_in' => $checkIn, 'check_out' => $checkOut, 'guest_count' => $guestCount]) }}"
           class="text-sm text-primary-600 hover:text-primary-800 mt-1 sm:mt-0">
            ← Ubah tanggal atau jumlah tamu
        </a>
    </div>

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
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Informasi Tamu</h2>
                    @guest
                    <p class="text-sm text-gray-500 mb-4">Tidak perlu akun untuk memesan. <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Masuk</a> agar data terisi otomatis dan booking tersimpan di akun.</p>
                    @else
                    <p class="text-sm text-gray-500 mb-4">Data di bawah terisi dari akun Anda.</p>
                    @endguest

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
                            <input type="tel" name="guest_whatsapp" id="guest_whatsapp"
                                   value="{{ old('guest_whatsapp', $user->whatsapp ?? '') }}"
                                   required
                                   inputmode="tel"
                                   autocomplete="tel"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="text-xs text-gray-400 mt-1">Format: 08xx, 628xx, atau +628xx</p>
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
                                Perkiraan jam kedatangan
                            </label>
                            <select name="arrival_estimate" id="arrival_estimate"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">— Pilih jam kedatangan —</option>
                                @foreach(\App\Support\ArrivalTimeSlots::generate() as $value => $label)
                                    <option value="{{ $value }}" {{ old('arrival_estimate') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-form-error field="arrival_estimate" />
                        </div>

                        {{-- Permintaan Khusus --}}
                        <div>
                            <label for="special_request" class="block text-sm font-medium text-gray-700 mb-1">
                                Permintaan Khusus
                            </label>
                            <textarea name="special_request" id="special_request" rows="3"
                                      placeholder="Contoh: extra bantal, kedatangan malam, dll."
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
                <div x-data="{ submitting: false }" class="hidden md:block">
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

                {{-- Mobile Sticky Footer --}}
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 md:hidden z-30 safe-area-bottom" x-data="{ submitting: false }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-lg font-bold text-primary-600">Rp{{ number_format($quote['total_amount'], 0, ',', '.') }}</p>
                        </div>
                        <button type="submit"
                                x-on:click="setTimeout(() => submitting = true, 50)"
                                :disabled="submitting"
                                class="bg-primary-600 text-white py-2.5 px-6 rounded-lg font-semibold hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center">
                            <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!submitting">Pesan Sekarang</span>
                            <span x-show="submitting" x-cloak>Memproses...</span>
                        </button>
                    </div>
                </div>
            </form>
            {{-- Spacer for mobile sticky footer --}}
            <div class="h-20 md:hidden"></div>
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
