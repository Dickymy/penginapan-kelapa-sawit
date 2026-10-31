@extends('layouts.public')

@php $hideBottomNav = true; @endphp

@section('title', 'Checkout & Ringkasan Booking - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-36 lg:pb-12"
     x-data="{
         baseSubtotal: {{ $quote['subtotal'] }},
         baseTotal: {{ $quote['total_amount'] }},
         addonTotal: 0,
         usePoints: false,
         showPolicyError: false,
         mobileSummaryOpen: false,
         promoInput: '{{ old('promo_code', '') }}',
         promoCode: '{{ old('promo_code', '') }}',
         promoDiscount: 0,
         promoDetails: null,
         promoLoading: false,
         promoError: '',
         promoSuccess: '',
         pointBalance: {{ $user ? app(\App\Services\LoyaltyPointService::class)->getBalance($user) : 0 }},
         pointValue: {{ config('loyalty.point_value', 1) }},
         
         get discountedSubtotal() {
             return Math.max(0, this.baseSubtotal - this.promoDiscount);
         },
         get maxPointDiscount() {
             if (!this.usePoints || this.pointBalance <= 0) return 0;
             let maxAllowed = Math.floor(this.baseSubtotal * 0.2); // max 20%
             let remaining = Math.max(0, this.discountedSubtotal + this.addonTotal);
             return Math.min(this.pointBalance * this.pointValue, maxAllowed, remaining);
         },
         get pointDiscount() {
             return this.maxPointDiscount;
         },
         get totalSavings() {
             return this.promoDiscount + this.pointDiscount;
         },
         get finalTotal() {
             return Math.max(0, this.baseSubtotal + this.addonTotal - this.promoDiscount - this.pointDiscount);
         },
         async applyPromo(codeToApply = null) {
             const code = (codeToApply !== null ? codeToApply : this.promoInput).trim().toUpperCase();
             if (!code) {
                 this.promoError = 'Silakan masukkan kode promo terlebih dahulu.';
                 this.promoSuccess = '';
                 return;
             }
             this.promoLoading = true;
             this.promoError = '';
             this.promoSuccess = '';

             try {
                 const csrf = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
                 const res = await fetch('{{ route('booking.apply-promo') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': csrf || ''
                     },
                     body: JSON.stringify({
                         promo_code: code,
                         room_type_id: {{ $roomType->id }},
                         check_in: '{{ $checkIn->format('Y-m-d') }}',
                         check_out: '{{ $checkOut->format('Y-m-d') }}'
                     })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) {
                     this.promoCode = data.code;
                     this.promoInput = data.code;
                     this.promoDiscount = data.discount;
                     this.promoDetails = data;
                     this.promoSuccess = data.message;
                     this.promoError = '';
                 } else {
                     this.promoError = data.message || 'Kode promo tidak valid atau belum memenuhi ketentuan.';
                     this.promoSuccess = '';
                     this.promoDiscount = 0;
                     this.promoCode = '';
                     this.promoDetails = null;
                 }
             } catch (err) {
                 console.error('Error applying promo:', err);
                 this.promoError = 'Gagal memeriksa kode promo. Periksa koneksi internet Anda.';
             } finally {
                 this.promoLoading = false;
             }
         },
         removePromo() {
             this.promoCode = '';
             this.promoInput = '';
             this.promoDiscount = 0;
             this.promoDetails = null;
             this.promoError = '';
             this.promoSuccess = '';
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
             return new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(val)));
         }
     }"
     x-init="
         if (promoCode) {
             applyPromo(promoCode);
         }
     ">

    {{-- Progress Steps --}}
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-primary-700 mb-2">Langkah 2 dari 4 &bull; Konfirmasi &amp; Data Tamu</p>
        <div class="flex items-center gap-1.5">
            <div class="h-1.5 flex-1 rounded-full bg-primary-600"></div>
            <div class="h-1.5 flex-1 rounded-full bg-primary-600"></div>
            <div class="h-1.5 flex-1 rounded-full bg-gray-200"></div>
            <div class="h-1.5 flex-1 rounded-full bg-gray-200"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-2">
            <span class="text-gray-600 font-medium">1. Pilih Kamar</span>
            <span class="text-primary-700 font-bold">2. Data &amp; Ringkasan</span>
            <span>3. Konfirmasi</span>
            <span>4. Pembayaran</span>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Checkout Pesanan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Lengkapi data tamu dan periksa ringkasan pesanan sebelum lanjut ke pembayaran.</p>
        </div>
        <a href="{{ route('availability.search', ['check_in' => $checkIn->format('Y-m-d'), 'check_out' => $checkOut->format('Y-m-d'), 'guest_count' => $guestCount]) }}"
           class="inline-flex items-center gap-1 text-xs sm:text-sm font-semibold text-primary-600 hover:text-primary-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Ubah tanggal / tamu
        </a>
    </div>

    {{-- Session / Error Alerts --}}
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-800 mb-2">Mohon lengkapi atau perbaiki data berikut:</p>
            <ul class="list-disc list-inside text-xs sm:text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Grid Layout: Desktop 2 Cols Form + 1 Col Sticky Sidebar --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Left: Form Inputs --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Mobile Quick Order Glance Card (Enhanced Native Look) --}}
            <div class="lg:hidden bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center gap-3">
                    @if($roomType->cover_image)
                        <img src="{{ $roomType->cover_image->thumb_url ?? $roomType->cover_image->url }}"
                             alt="{{ $roomType->name }}"
                             class="w-14 h-14 rounded-xl object-cover shrink-0 border border-gray-100">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-sm font-bold text-gray-900 truncate">{{ $roomType->name }}</h3>
                            <span class="inline-flex text-[11px] font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full shrink-0">
                                {{ $quote['nights'] }} Malam
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $checkIn->translatedFormat('d M') }} &ndash; {{ $checkOut->translatedFormat('d M Y') }} &bull; {{ $guestCount }} Tamu
                        </p>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Total Tagihan</span>
                        <div class="flex items-baseline gap-1.5">
                            <span x-show="totalSavings > 0" class="text-xs text-gray-400 line-through" x-text="'Rp' + formatPrice(baseSubtotal + addonTotal)"></span>
                            <span class="text-base font-extrabold text-primary-700">Rp<span x-text="formatPrice(finalTotal)">{{ number_format($quote['total_amount'], 0, ',', '.') }}</span></span>
                        </div>
                    </div>
                    <button type="button" @click="mobileSummaryOpen = true"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition">
                        <span>Rincian Biaya</span>
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <form action="{{ route('booking.store') }}" method="POST" id="checkout-form" class="space-y-6"
                  @submit.prevent="
                      if (!document.getElementById('policy_accepted').checked) {
                          showPolicyError = true;
                          document.getElementById('policy_container').scrollIntoView({behavior: 'smooth', block: 'center'});
                          return;
                      }
                      $store.checkoutForm.submitting = true;
                      $el.submit();
                  ">
                @csrf

                {{-- Hidden Form Fields --}}
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                <input type="hidden" name="guest_count" value="{{ $guestCount }}">
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">
                <input type="hidden" name="promo_code" :value="promoCode">

                {{-- 1. Guest Information Card --}}
                <div class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Data Pemesan / Tamu</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Konfirmasi pemesanan dan bukti reservasi akan dikirim ke kontak ini.</p>
                        </div>
                    </div>

                    @guest
                    <div class="mb-5 bg-gray-50 border border-gray-200/80 rounded-xl p-3 text-xs text-gray-600 flex items-center justify-between">
                        <span>Tidak perlu akun untuk memesan (Pesan cepat). Punya akun member?</span>
                        <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 ml-2">Masuk &rarr;</a>
                    </div>
                    @else
                    <div class="mb-5 bg-primary-50/70 border border-primary-100 rounded-xl p-3 text-xs text-primary-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Data terisi otomatis dari akun member Anda (<strong>{{ $user->name }}</strong>).</span>
                    </div>
                    @endguest

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="guest_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="guest_name" id="guest_name"
                                   value="{{ old('guest_name', $user->name ?? '') }}"
                                   required
                                   placeholder="Masukkan nama sesuai identitas (KTP/SIM)"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 text-sm transition py-2.5">
                            <x-form-error field="guest_name" />
                        </div>

                        <div>
                            <label for="guest_email" class="block text-sm font-semibold text-gray-700 mb-1">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="guest_email" id="guest_email"
                                   value="{{ old('guest_email', $user->email ?? '') }}"
                                   required
                                   placeholder="nama@email.com"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 text-sm transition py-2.5">
                            <p class="text-[11px] text-gray-400 mt-1">E-voucher &amp; invoice resmi dikirim ke email ini.</p>
                            <x-form-error field="guest_email" />
                        </div>

                        <div>
                            <label for="guest_whatsapp" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="guest_whatsapp" id="guest_whatsapp"
                                   value="{{ old('guest_whatsapp', $user->whatsapp ?? '') }}"
                                   required
                                   inputmode="tel"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 text-sm transition py-2.5">
                            <p class="text-[11px] text-gray-400 mt-1">Untuk konfirmasi instan via WhatsApp.</p>
                            <x-form-error field="guest_whatsapp" />
                        </div>
                    </div>
                </div>

                {{-- 2. Promo & Voucher Card --}}
                <div class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Voucher &amp; Kode Promo</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Masukkan kode promo untuk mendapatkan potongan harga menginap.</p>
                        </div>
                    </div>

                    {{-- Active Applied Promo Banner --}}
                    <div x-show="promoCode && promoDiscount > 0" x-cloak class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3.5 mb-4 transition-all">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-bold text-xs bg-emerald-200/90 text-emerald-950 px-2 py-0.5 rounded tracking-wider" x-text="promoCode"></span>
                                        <span class="text-xs font-bold text-emerald-800" x-text="'Hemat Rp ' + formatPrice(promoDiscount)"></span>
                                    </div>
                                    <p class="text-xs font-semibold text-emerald-900 mt-1" x-text="promoDetails?.name || 'Promo Berhasil Digunakan'"></p>
                                    <p class="text-[11px] text-emerald-700 mt-0.5" x-text="promoSuccess"></p>
                                </div>
                            </div>
                            <button type="button" @click="removePromo()" class="text-xs text-red-600 hover:text-red-700 font-semibold px-2.5 py-1 rounded-lg hover:bg-red-50 transition shrink-0 cursor-pointer">
                                Batalkan
                            </button>
                        </div>
                    </div>

                    {{-- Promo Code Input Form --}}
                    <div x-show="!promoCode || promoDiscount === 0" class="space-y-3">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="text"
                                       x-model="promoInput"
                                       @keydown.enter.prevent="applyPromo()"
                                       placeholder="Ketik kode promo..."
                                       class="w-full rounded-xl border-gray-300 uppercase font-mono text-sm placeholder:normal-case placeholder:font-sans focus:border-primary-500 focus:ring-2 focus:ring-primary-500 pl-10 pr-4 py-2.5 transition">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                </div>
                            </div>
                            <button type="button"
                                    @click="applyPromo()"
                                    :disabled="promoLoading || !promoInput.trim()"
                                    class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-xs shrink-0 cursor-pointer">
                                <svg x-show="promoLoading" x-cloak class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                <span x-show="!promoLoading">Terapkan</span>
                                <span x-show="promoLoading" x-cloak>Memeriksa...</span>
                            </button>
                        </div>

                        {{-- Promo Error Warning --}}
                        <div x-show="promoError" x-cloak x-transition class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-700 flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="promoError"></span>
                        </div>
                    </div>

                    {{-- Available Promos to Tap and Apply (Controlled by Admin) --}}
                    @if(isset($activePromos) && $activePromos->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-700 mb-2.5 flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Promo Menarik Tersedia (Klik untuk Gunakan):
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($activePromos as $ap)
                            <div class="border border-emerald-200/90 bg-emerald-50/40 hover:bg-emerald-50/80 rounded-xl p-3 transition flex items-center justify-between gap-3 group">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono text-xs font-bold text-emerald-900 tracking-wider bg-white border border-emerald-200 px-1.5 py-0.5 rounded">{{ $ap->code }}</span>
                                        <span class="text-xs font-bold text-gray-900">
                                            @if($ap->type->value === 'percentage')
                                                Diskon {{ $ap->value }}%
                                            @else
                                                Potongan Rp{{ number_format($ap->value, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-600 mt-1 truncate" title="{{ $ap->name }}">{{ $ap->name }}</p>
                                    @if($ap->minimum_booking_amount > 0)
                                        <p class="text-[10px] text-gray-400 mt-0.5">Min. order Rp{{ number_format($ap->minimum_booking_amount, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <button type="button"
                                        @click="applyPromo('{{ $ap->code }}')"
                                        :disabled="promoCode === '{{ $ap->code }}'"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg transition shrink-0 cursor-pointer"
                                        :class="promoCode === '{{ $ap->code }}' ? 'bg-emerald-200 text-emerald-800 cursor-default' : 'bg-primary-600 hover:bg-primary-700 text-white'">
                                    <span x-text="promoCode === '{{ $ap->code }}' ? 'Dipakai' : 'Pakai'"></span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- 3. Add-ons Card --}}
                @if(isset($addons) && $addons->isNotEmpty())
                <div class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Layanan Tambahan (Opsional)</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih fasilitas tambahan untuk memaksimalkan kenyamanan Anda.</p>
                        </div>
                    </div>

                    @if($guestCount > 2)
                    <div class="mb-4 bg-amber-50/80 border border-amber-200 rounded-xl p-3.5 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-xs font-bold text-amber-900">Rekomendasi Penambahan Kasur (Extra Bed)</p>
                            <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">Anda memesan untuk <strong>{{ $guestCount }} orang</strong>. Anda dapat mencentang Kasur Ekstra di bawah ini agar istirahat keluarga/rekan Anda tetap lega.</p>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-3.5">
                        @foreach($addons as $index => $addon)
                        <div class="addon-item flex items-start justify-between border-b border-gray-100 pb-3.5 last:border-0 last:pb-0"
                             data-price="{{ $addon->price }}"
                             x-data="{ selected: false, qty: 1 }">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="addons[{{ $index }}][addon_id]" value="{{ $addon->id }}"
                                       x-model="selected"
                                       @change="$nextTick(() => updateAddonTotal())"
                                       class="addon-checkbox mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $addon->name }}</p>
                                    @if($addon->description)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $addon->description }}</p>
                                    @endif
                                    <p class="text-xs font-bold text-primary-700 mt-1">{{ $addon->formatted_price }} @if($addon->isQuantityBased())<span class="text-gray-400 font-normal">/ unit</span>@endif</p>
                                </div>
                            </div>

                            <div x-show="selected" x-cloak class="shrink-0 ml-2">
                                @if($addon->isQuantityBased())
                                <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg p-1">
                                    <button type="button" @click="if(qty > 1) { qty--; $nextTick(() => updateAddonTotal()); }" class="w-7 h-7 rounded bg-white text-gray-700 flex items-center justify-center hover:bg-gray-100 shadow-2xs font-bold cursor-pointer">-</button>
                                    <input type="number" name="addons[{{ $index }}][quantity]" x-model="qty" min="1" @input="$nextTick(() => updateAddonTotal())" :disabled="!selected" class="addon-qty w-10 text-center border-0 bg-transparent p-0 text-xs font-bold" readonly>
                                    <button type="button" @click="qty++; $nextTick(() => updateAddonTotal());" class="w-7 h-7 rounded bg-white text-gray-700 flex items-center justify-center hover:bg-gray-100 shadow-2xs font-bold cursor-pointer">+</button>
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

                {{-- 4. Poin Loyalti (Member Only) --}}
                @auth
                <div x-show="pointBalance > 0" class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-gray-900">Tukarkan Poin Loyalti</h2>
                                <p class="text-xs text-gray-600 mt-0.5">Saldo: <strong class="text-primary-700" x-text="formatPrice(pointBalance)"></strong> poin (Nilai: Rp<span x-text="formatPrice(pointBalance * pointValue)"></span>).</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Dapat digunakan memotong hingga 20% biaya menginap.</p>
                            </div>
                        </div>
                        <div class="flex items-center h-5 mt-1 shrink-0">
                            <input type="checkbox" name="use_points" id="use_points" value="1"
                                   x-model="usePoints"
                                   class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded cursor-pointer">
                        </div>
                    </div>
                </div>
                @endauth

                {{-- 5. Additional Info --}}
                <div class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Informasi Tambahan</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Perkiraan jam kedatangan dan catatan untuk staf penginapan.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="arrival_estimate" class="block text-sm font-semibold text-gray-700 mb-1">
                                Perkiraan Jam Kedatangan (Check-in)
                            </label>
                            <select name="arrival_estimate" id="arrival_estimate"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 transition py-2.5">
                                <option value="">— Pilih perkiraan jam kedatangan —</option>
                                @foreach(\App\Support\ArrivalTimeSlots::generate() as $value => $label)
                                    <option value="{{ $value }}" {{ old('arrival_estimate') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-form-error field="arrival_estimate" />
                        </div>

                        <div>
                            <label for="special_request" class="block text-sm font-semibold text-gray-700 mb-1">
                                Permintaan Khusus <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <textarea name="special_request" id="special_request" rows="2"
                                      placeholder="Contoh: Kamar di lantai dasar, bantal tambahan, kedatangan larut malam, dll."
                                      class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 transition">{{ old('special_request') }}</textarea>
                            <x-form-error field="special_request" />
                        </div>
                    </div>
                </div>

                {{-- 6. Policy Acceptance --}}
                <div id="policy_container" class="bg-white border border-gray-200/90 rounded-2xl p-5 md:p-7 shadow-xs transition-all duration-300"
                     :class="{ 'ring-2 ring-red-500 bg-red-50/50': showPolicyError }">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" name="policy_accepted" id="policy_accepted" value="1"
                               {{ old('policy_accepted') ? 'checked' : '' }}
                                @change="showPolicyError = false"
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition-colors cursor-pointer"
                               :class="{ 'border-red-500': showPolicyError }">
                        <label for="policy_accepted" class="text-xs sm:text-sm text-gray-700 leading-relaxed cursor-pointer">
                            Saya telah membaca dan menyetujui seluruh
                            <a href="{{ route('policy') }}" target="_blank" class="text-primary-600 font-bold hover:underline">kebijakan penginapan</a>,
                            termasuk waktu check-in (14:00 WITA), check-out (12:00 WITA), dan ketentuan pembatalan.
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <p x-show="showPolicyError" x-cloak x-transition class="text-xs text-red-600 mt-2 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Anda wajib mencentang persetujuan kebijakan untuk menyelesaikan booking.
                    </p>
                    <x-form-error field="policy_accepted" />
                </div>

                {{-- Desktop Submit Button --}}
                <div class="hidden md:block pt-2">
                    <button type="submit"
                            :disabled="$store.checkoutForm.submitting"
                            class="w-full bg-primary-600 text-white py-4 px-6 rounded-2xl font-bold text-base hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/20 transition-all duration-300 text-center disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                        <svg x-show="$store.checkoutForm.submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!$store.checkoutForm.submitting" class="flex items-center gap-2">
                            <span x-text="finalTotal === 0 ? 'Konfirmasi Reservasi' : 'Lanjut ke Pembayaran'"></span>
                            <span>&bull;</span>
                            <span x-text="'Rp ' + formatPrice(finalTotal)">Rp{{ number_format($quote['total_amount'], 0, ',', '.') }}</span>
                            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                        <span x-show="$store.checkoutForm.submitting" x-cloak>Memproses booking...</span>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-2.5 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Pembayaran dijamin aman melalui saluran resmi &amp; terenkripsi.
                    </p>
                </div>

                {{-- Mobile Floating Bottom Bar --}}
                <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200/90 px-4 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] md:hidden z-40 shadow-[0_-8px_30px_rgba(0,0,0,0.08)]">
                    <div class="flex items-center justify-between gap-3 max-w-md mx-auto">
                        <div class="flex-1 min-w-0" @click="mobileSummaryOpen = true">
                            <div class="flex items-center gap-1 cursor-pointer">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</p>
                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span x-show="totalSavings > 0" class="text-xs text-gray-400 line-through" x-text="'Rp' + formatPrice(baseSubtotal + addonTotal)"></span>
                                <p class="text-lg font-black text-primary-700 leading-tight">Rp<span x-text="formatPrice(finalTotal)">{{ number_format($quote['total_amount'], 0, ',', '.') }}</span></p>
                            </div>
                            <p x-show="totalSavings > 0" class="text-[10px] font-bold text-emerald-600 leading-none mt-0.5">
                                Hemat Rp<span x-text="formatPrice(totalSavings)"></span>
                            </p>
                        </div>
                        <button type="submit"
                                :disabled="$store.checkoutForm.submitting"
                                class="shrink-0 bg-primary-600 text-white py-3 px-5 rounded-xl font-bold text-sm hover:bg-primary-700 active:scale-95 transition shadow-sm disabled:opacity-60 flex items-center gap-1.5 cursor-pointer">
                            <svg x-show="$store.checkoutForm.submitting" x-cloak class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!$store.checkoutForm.submitting" x-text="finalTotal === 0 ? 'Konfirmasi Reservasi' : 'Pesan Sekarang'">Pesan Sekarang</span>
                            <span x-show="$store.checkoutForm.submitting" x-cloak>Memproses...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right: Ringkasan Booking Sidebar (Desktop Sticky & Follows Scroll) --}}
        <div class="lg:col-span-1 relative">
            <div class="sticky top-24 max-h-[calc(100vh-6.5rem)] overflow-y-auto overscroll-contain pr-1 custom-scrollbar">
                <div class="bg-white border border-gray-200/90 rounded-2xl shadow-xs p-5 sm:p-6 space-y-4">
                    {{-- Header --}}
                    <div class="flex items-center justify-between pb-3.5 border-b border-gray-100">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Ringkasan Booking</h2>
                            <p class="text-[11px] text-gray-500">Rincian akomodasi &amp; tarif</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $quote['nights'] }} Malam
                        </span>
                    </div>

                    {{-- Room Card Preview (Clean & Elegant) --}}
                    <div class="flex items-start gap-3.5">
                        @if($roomType->cover_image)
                            <img src="{{ $roomType->cover_image->thumb_url ?? $roomType->cover_image->url }}"
                                 alt="{{ $roomType->name }}"
                                 class="w-16 h-16 rounded-xl object-cover shrink-0 border border-gray-100">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 border border-gray-100">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $roomType->name }}</h3>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1 flex-wrap">
                                <span>{{ $roomType->capacity }} Tamu</span>
                                @if($roomType->bed_type)
                                    <span>&bull; {{ $roomType->bed_type }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Dates & Schedule Box (Clean 2-Column Strip) --}}
                    <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Check-in</span>
                                <span class="font-bold text-gray-900 text-xs sm:text-sm block mt-0.5">{{ $checkIn->translatedFormat('d M Y') }}</span>
                                <span class="text-[11px] text-gray-500 block">Mulai 14:00 WITA</span>
                            </div>
                            <div class="border-l border-gray-200/60 pl-3">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Check-out</span>
                                <span class="font-bold text-gray-900 text-xs sm:text-sm block mt-0.5">{{ $checkOut->translatedFormat('d M Y') }}</span>
                                <span class="text-[11px] text-gray-500 block">Sebelum 12:00 WITA</span>
                            </div>
                        </div>
                    </div>

                    {{-- Itemized Price Breakdown --}}
                    <div class="space-y-2.5 text-xs sm:text-sm pt-1">
                        {{-- Base Room Price --}}
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Sewa Kamar ({{ $quote['nights'] }} malam)</span>
                            <span class="font-semibold text-gray-900">Rp{{ number_format($quote['subtotal'], 0, ',', '.') }}</span>
                        </div>

                        {{-- Dynamic Night Prices breakdown if exists --}}
                        @if(isset($quote['night_prices']) && count($quote['night_prices']) > 1)
                        <div class="bg-gray-50/70 border border-gray-100 rounded-lg p-2.5 text-[11px] space-y-1">
                            @foreach($quote['night_prices'] as $np)
                            <div class="flex justify-between text-gray-500">
                                <span>
                                    {{ \Carbon\Carbon::parse($np['date'])->translatedFormat('d M') }}
                                    @if($np['label'])
                                        <span class="text-[9px] bg-primary-100 text-primary-700 px-1 py-0.2 rounded ml-1">{{ $np['label'] }}</span>
                                    @endif
                                </span>
                                <span class="font-mono text-gray-700">Rp{{ number_format($np['price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Addons --}}
                        <div class="flex justify-between items-center text-gray-600" x-show="addonTotal > 0" x-cloak>
                            <span>Layanan Tambahan</span>
                            <span class="font-semibold text-gray-900">+Rp<span x-text="formatPrice(addonTotal)"></span></span>
                        </div>

                        {{-- Promo Discount (Clean Emerald Line) --}}
                        <div class="flex justify-between items-center text-emerald-700" x-show="promoDiscount > 0" x-cloak>
                            <span class="flex items-center gap-1.5 font-medium">
                                <span>Diskon Promo</span>
                                <span class="font-mono text-xs font-bold bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded border border-emerald-200" x-text="promoCode"></span>
                            </span>
                            <span class="font-bold text-emerald-700">-Rp<span x-text="formatPrice(promoDiscount)"></span></span>
                        </div>

                        {{-- Points Discount --}}
                        <div class="flex justify-between items-center text-emerald-700" x-show="usePoints && pointDiscount > 0" x-cloak>
                            <span class="font-medium">Potongan Poin Loyalti</span>
                            <span class="font-bold text-emerald-700">-Rp<span x-text="formatPrice(pointDiscount)"></span></span>
                        </div>

                        {{-- Total Savings Callout (Professional & Subtle) --}}
                        <div x-show="totalSavings > 0" x-cloak class="py-2 px-3 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-between text-xs text-emerald-800 font-semibold">
                            <span>Total Hemat</span>
                            <span>Rp<span x-text="formatPrice(totalSavings)"></span></span>
                        </div>

                        {{-- Final Total --}}
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-baseline">
                                <div>
                                    <span class="text-sm font-bold text-gray-900 block">Total Pembayaran</span>
                                    <span class="text-[10px] text-gray-400">Termasuk pajak &amp; biaya layanan</span>
                                </div>
                                <div class="text-right">
                                    <span x-show="totalSavings > 0" class="text-xs text-gray-400 line-through block" x-text="'Rp ' + formatPrice(baseSubtotal + addonTotal)"></span>
                                    <span class="text-xl sm:text-2xl font-black text-gray-900">Rp<span x-text="formatPrice(finalTotal)">{{ number_format($quote['total_amount'], 0, ',', '.') }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trust Guarantees --}}
                    <div class="pt-3 border-t border-gray-100 space-y-1.5 text-[11px] text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Konfirmasi instan &amp; kamar terjamin</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Pembayaran aman (QRIS, VA, E-Wallet)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Full Breakdown Bottom Sheet (Modal) --}}
    <div x-show="mobileSummaryOpen" x-cloak class="fixed inset-0 z-50 md:hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs" @click="mobileSummaryOpen = false"></div>

        {{-- Slide-up Sheet --}}
        <div class="fixed inset-x-0 bottom-0 max-h-[85vh] bg-white rounded-t-3xl shadow-2xl overflow-y-auto p-5 pb-8"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            {{-- Drag Handle --}}
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>

            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Rincian Pemesanan</h3>
                    <p class="text-xs text-gray-500">{{ $quote['nights'] }} malam menginap</p>
                </div>
                <button type="button" @click="mobileSummaryOpen = false" class="p-1 rounded-full text-gray-400 hover:text-gray-600 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                {{-- Room and stay preview --}}
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                    <div class="flex items-center gap-3">
                        @if($roomType->cover_image)
                            <img src="{{ $roomType->cover_image->thumb_url ?? $roomType->cover_image->url }}"
                                 alt="{{ $roomType->name }}"
                                 class="w-12 h-12 rounded-lg object-cover shrink-0">
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-gray-900 text-sm truncate">{{ $roomType->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $checkIn->translatedFormat('d M Y') }} &ndash; {{ $checkOut->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Prices itemized --}}
                <div class="space-y-2.5 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Biaya Kamar ({{ $quote['nights'] }} malam)</span>
                        <span class="font-semibold text-gray-900">Rp{{ number_format($quote['subtotal'], 0, ',', '.') }}</span>
                    </div>

                    @if(isset($quote['night_prices']) && count($quote['night_prices']) > 1)
                    <div class="bg-gray-50/70 border border-gray-100 rounded-lg p-2 text-[11px] space-y-1">
                        @foreach($quote['night_prices'] as $np)
                        <div class="flex justify-between text-gray-500">
                            <span>{{ \Carbon\Carbon::parse($np['date'])->translatedFormat('d M') }}</span>
                            <span class="font-mono text-gray-700">Rp{{ number_format($np['price'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="flex justify-between" x-show="addonTotal > 0" x-cloak>
                        <span>Layanan Tambahan</span>
                        <span class="font-semibold text-gray-900">+Rp<span x-text="formatPrice(addonTotal)"></span></span>
                    </div>

                    <div class="flex justify-between text-emerald-700 font-semibold" x-show="promoDiscount > 0" x-cloak>
                        <span>Diskon Promo (<span class="font-mono" x-text="promoCode"></span>)</span>
                        <span>-Rp<span x-text="formatPrice(promoDiscount)"></span></span>
                    </div>

                    <div class="flex justify-between text-emerald-700 font-semibold" x-show="usePoints && pointDiscount > 0" x-cloak>
                        <span>Potongan Poin Loyalti</span>
                        <span>-Rp<span x-text="formatPrice(pointDiscount)"></span></span>
                    </div>

                    <div x-show="totalSavings > 0" x-cloak class="p-2.5 bg-emerald-50 rounded-lg text-emerald-800 text-xs font-semibold flex justify-between">
                        <span>Total Hemat Anda</span>
                        <span>Rp<span x-text="formatPrice(totalSavings)"></span></span>
                    </div>

                    <div class="pt-3 border-t border-gray-200 flex justify-between items-baseline font-bold text-base text-gray-900">
                        <div>
                            <span>Total Pembayaran</span>
                            <span class="text-[10px] text-gray-400 font-normal block">Termasuk pajak &amp; biaya</span>
                        </div>
                        <span class="text-xl font-black text-primary-700">Rp<span x-text="formatPrice(finalTotal)"></span></span>
                    </div>
                </div>

                {{-- Schedule footnote --}}
                <div class="bg-gray-50 rounded-xl p-3 text-xs text-gray-500 space-y-1 border border-gray-100">
                    <p class="flex justify-between">
                        <span>Waktu Check-in:</span>
                        <strong class="text-gray-800">14:00 WITA</strong>
                    </p>
                    <p class="flex justify-between">
                        <span>Waktu Check-out:</span>
                        <strong class="text-gray-800">12:00 WITA</strong>
                    </p>
                </div>

                <div class="pt-2">
                    <button type="button" @click="mobileSummaryOpen = false"
                            class="w-full bg-primary-600 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-primary-700 transition shadow-xs cursor-pointer">
                        Lanjutkan Pengisian Form
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @media (max-width: 768px) {
        footer {
            padding-bottom: max(7rem, env(safe-area-inset-bottom)) !important;
        }
    }
</style>
@endpush
