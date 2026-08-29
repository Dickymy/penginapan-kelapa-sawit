@extends('layouts.public')

@section('title', 'Checkout - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-36 lg:pb-8">
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
        <a href="{{ route('availability.search', ['check_in' => $checkIn->format('Y-m-d'), 'check_out' => $checkOut->format('Y-m-d'), 'guest_count' => $guestCount]) }}"
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8"
         x-data="{
             baseTotal: {{ $quote['total_amount'] }},
             addonTotal: 0,
             usePoints: false,
             pointBalance: {{ $user ? app(\App\Services\LoyaltyPointService::class)->getBalance($user) : 0 }},
             pointValue: {{ config('loyalty.point_value', 50) }},
             get pointDiscount() {
                 if (!this.usePoints || this.pointBalance <= 0) return 0;
                 return Math.min(this.baseTotal, this.pointBalance * this.pointValue);
             },
             get finalTotal() {
                 return Math.max(0, this.baseTotal - this.pointDiscount) + this.addonTotal;
             },
             updateAddonTotal() {
                 let total = 0;
                 document.querySelectorAll('.addon-item').forEach(item => {
                     if (item.querySelector('.addon-checkbox').checked) {
                         let price = parseInt(item.dataset.price);
                         let qty = parseInt(item.querySelector('.addon-qty').value) || 1;
                         total += price * qty;
                     }
                 });
                 this.addonTotal = total;
             },
             formatPrice(val) {
                 return new Intl.NumberFormat('id-ID').format(val);
             }
         }">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('booking.store') }}" method="POST" class="space-y-6"
                  @submit="$store.checkoutForm.submitting = true">
                @csrf

                {{-- Hidden Fields --}}
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                <input type="hidden" name="guest_count" value="{{ $guestCount }}">
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

                {{-- Guest Info --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
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

                {{-- Add-ons --}}
                @if(isset($addons) && $addons->isNotEmpty())
                <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Layanan Tambahan (Opsional)</h2>
                    <div class="space-y-4">
                        @foreach($addons as $index => $addon)
                        <div class="addon-item flex items-start justify-between border-b border-gray-100 pb-4 last:border-0 last:pb-0"
                             data-price="{{ $addon->price }}"
                             x-data="{ selected: false, qty: 1 }">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="addons[{{ $index }}][addon_id]" value="{{ $addon->id }}"
                                       x-model="selected"
                                       @change="$nextTick(() => updateAddonTotal())"
                                       class="addon-checkbox mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $addon->name }}</p>
                                    @if($addon->description)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $addon->description }}</p>
                                    @endif
                                    <p class="text-sm text-primary-600 font-medium mt-1">{{ $addon->formatted_price }} @if($addon->isQuantityBased())<span class="text-xs text-gray-400 font-normal">/ unit</span>@endif</p>
                                </div>
                            </div>
                            
                            <div x-show="selected" x-cloak>
                                @if($addon->isQuantityBased())
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="if(qty > 1) { qty--; $nextTick(() => updateAddonTotal()); }" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200">-</button>
                                    <input type="number" name="addons[{{ $index }}][quantity]" x-model="qty" min="1" @input="$nextTick(() => updateAddonTotal())" :disabled="!selected" class="addon-qty w-12 text-center border-transparent focus:border-transparent focus:ring-0 p-0 text-sm font-medium" readonly>
                                    <button type="button" @click="qty++; $nextTick(() => updateAddonTotal());" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200">+</button>
                                </div>
                                @else
                                <input type="hidden" name="addons[{{ $index }}][quantity]" value="1" class="addon-qty" :disabled="!selected">
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Poin Loyalti --}}
                @auth
                <div x-show="pointBalance > 0" class="bg-white border border-gray-100 rounded-2xl p-5 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">Gunakan Poin Loyalti</h2>
                            <p class="text-sm text-gray-500">Anda memiliki <span class="font-bold text-primary-600" x-text="formatPrice(pointBalance)"></span> poin (setara Rp<span x-text="formatPrice(pointBalance * pointValue)"></span>).</p>
                        </div>
                        <div class="flex items-center h-5 mt-1">
                            <input type="checkbox" name="use_points" id="use_points" value="1"
                                   x-model="usePoints"
                                   class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Additional Info --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
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
                <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
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
                <div class="hidden md:block">
                    <button type="submit"
                            :disabled="$store.checkoutForm.submitting"
                            class="w-full bg-primary-600 text-white py-3.5 px-6 rounded-xl font-bold hover:bg-primary-500 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300 text-center disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                        <svg x-show="$store.checkoutForm.submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!$store.checkoutForm.submitting">Pesan Sekarang</span>
                        <span x-show="$store.checkoutForm.submitting" x-cloak>Membuat booking...</span>
                    </button>
                </div>

                {{-- Mobile Sticky Footer --}}
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-4 pb-8 md:hidden z-50 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-lg font-bold text-primary-600">Rp<span x-text="formatPrice(finalTotal)">{{ number_format($quote['total_amount'], 0, ',', '.') }}</span></p>
                        </div>
                        <button type="submit"
                                :disabled="$store.checkoutForm.submitting"
                                class="bg-primary-600 text-white py-2.5 px-6 rounded-lg font-semibold hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center">
                            <svg x-show="$store.checkoutForm.submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!$store.checkoutForm.submitting">Pesan Sekarang</span>
                            <span x-show="$store.checkoutForm.submitting" x-cloak>Memproses...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Booking Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white border-gray-100 border rounded-2xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] p-5 md:p-8 sticky top-24">
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

                    @if(isset($quote['night_prices']) && count($quote['night_prices']) > 0)
                        <div class="mt-4 mb-2 font-medium text-gray-700">Rincian Harga:</div>
                        <ul class="space-y-1 mb-4">
                            @foreach($quote['night_prices'] as $np)
                                <li class="flex justify-between text-sm">
                                    <span class="text-gray-500">
                                        {{ \Carbon\Carbon::parse($np['date'])->translatedFormat('d M Y') }}
                                        @if($np['label'])
                                            <span class="text-xs inline-block bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded ml-1">{{ $np['label'] }}</span>
                                        @endif
                                    </span>
                                    <span class="text-gray-900">Rp{{ number_format($np['price'], 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga per malam</span>
                            <span class="font-medium text-gray-900">Rp{{ number_format($quote['price_per_night'], 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between" x-show="addonTotal > 0" x-cloak>
                        <span class="text-gray-600">Layanan Tambahan</span>
                        <span class="font-medium text-gray-900">Rp<span x-text="formatPrice(addonTotal)">0</span></span>
                    </div>

                    <div class="flex justify-between" x-show="usePoints && pointDiscount > 0" x-cloak>
                        <span class="text-primary-600">Diskon Poin</span>
                        <span class="font-medium text-primary-600">-Rp<span x-text="formatPrice(pointDiscount)">0</span></span>
                    </div>

                    <hr class="border-gray-200">

                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary-600">Rp<span x-text="formatPrice(finalTotal)">{{ number_format($quote['total_amount'], 0, ',', '.') }}</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
