@extends('layouts.public')

@section('title', $propertyName . ' — Penginapan di Kota Bangun II')

@section('meta')
<meta name="description" content="Penginapan Kelapa Sawit — penginapan nyaman di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur. Pesan kamar langsung tanpa perlu akun.">
<meta property="og:title" content="{{ $propertyName }} — Penginapan di Kota Bangun II">
<meta property="og:description" content="Penginapan nyaman di Kota Bangun II, Kutai Kartanegara. Pesan langsung tanpa akun atau masuk untuk menyimpan histori dan mengumpulkan poin.">
<meta property="og:type" content="website">
<link rel="canonical" href="{{ route('home') }}">
@endsection

@section('content')
{{-- Hero --}}
<section class="relative bg-gradient-to-br from-primary-700 to-primary-900 text-white py-14 md:py-18">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-3">{{ $propertyName }}</h1>
        <p class="text-sm md:text-base text-primary-200 mb-4 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kota Bangun II, Kutai Kartanegara, Kalimantan Timur
        </p>
        @if($cheapestPrice)
            <p class="text-primary-200 mb-6">Mulai dari <span class="text-2xl font-bold text-white">Rp{{ number_format($cheapestPrice, 0, ',', '.') }}</span> / malam</p>
        @endif
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="#cari-kamar" class="px-6 py-3 bg-white text-primary-700 rounded-lg font-semibold hover:bg-primary-50 transition shadow">
                Cari Kamar
            </a>
            <a href="{{ route('location') }}" class="px-6 py-3 border border-white/40 text-white rounded-lg font-medium hover:bg-white/10 transition">
                Lihat Lokasi
            </a>
        </div>
    </div>
</section>

{{-- Search Availability --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10" id="cari-kamar">
    <div class="bg-white rounded-xl shadow-lg p-5 md:p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 text-center md:text-left">Cari Kamar Tersedia</h2>
        <form action="{{ route('availability.search') }}" method="GET"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
              x-data="{
                  submitting: false,
                  checkIn: '{{ request('check_in', old('check_in', date('Y-m-d'))) }}',
                  checkOut: '{{ request('check_out', old('check_out', date('Y-m-d', strtotime('+1 day')))) }}',
                  guestCount: {{ request('guest_count', old('guest_count', 1)) }},
                  error: '',
                  adjustCheckOut() {
                      if (this.checkOut <= this.checkIn) {
                          const next = new Date(this.checkIn);
                          next.setDate(next.getDate() + 1);
                          this.checkOut = next.toISOString().split('T')[0];
                      }
                  },
                  increment() { if (this.guestCount < 10) this.guestCount++; },
                  decrement() { if (this.guestCount > 1) this.guestCount--; }
              }"
              @submit.prevent="
                  error = '';
                  if (!checkIn) { error = 'Pilih tanggal check-in'; return; }
                  if (!checkOut) { error = 'Pilih tanggal check-out'; return; }
                  if (checkOut <= checkIn) { error = 'Tanggal check-out harus setelah check-in'; return; }
                  submitting = true;
                  $el.submit();
              ">
            {{-- Inline Error --}}
            <template x-if="error">
                <p class="md:col-span-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2" x-text="error"></p>
            </template>
            <div>
                <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in <span class="text-red-500">*</span></label>
                <input type="date" name="check_in" id="check_in" min="{{ date('Y-m-d') }}" required
                       x-model="checkIn"
                       @change="adjustCheckOut()"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out <span class="text-red-500">*</span></label>
                <input type="date" name="check_out" id="check_out" :min="checkIn || '{{ date('Y-m-d', strtotime('+1 day')) }}'" required
                       x-model="checkOut"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah tamu</label>
                <div class="flex items-center border border-gray-300 rounded-lg shadow-sm overflow-hidden">
                    <button type="button" @click="decrement()"
                            class="px-3 py-2.5 text-gray-600 hover:bg-gray-100 active:bg-gray-200 transition text-lg font-bold"
                            :class="guestCount <= 1 && 'opacity-40 cursor-not-allowed'"
                            :disabled="guestCount <= 1" aria-label="Kurangi tamu">−</button>
                    <input type="number" name="guest_count" x-model="guestCount" min="1" max="10" readonly
                           class="flex-1 text-center border-0 focus:ring-0 text-sm font-medium py-2.5 bg-transparent [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" @click="increment()"
                            class="px-3 py-2.5 text-gray-600 hover:bg-gray-100 active:bg-gray-200 transition text-lg font-bold"
                            :class="guestCount >= 10 && 'opacity-40 cursor-not-allowed'"
                            :disabled="guestCount >= 10" aria-label="Tambah tamu">+</button>
                </div>
            </div>
            <div>
                <button type="submit"
                        :disabled="submitting"
                        class="w-full px-4 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!submitting">Cari Kamar</span>
                    <span x-show="submitting" x-cloak>Mencari...</span>
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Booking Options --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="text-center text-lg font-semibold text-gray-800 mb-6">Pilih cara yang paling nyaman</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Booking Langsung</h3>
            <p class="text-sm text-gray-500 mb-4">Tidak perlu membuat akun.</p>
            <a href="#cari-kamar" class="inline-block px-5 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Cari Kamar</a>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Masuk sebagai Member</h3>
            <p class="text-sm text-gray-500 mb-4">Booking tersimpan di akun dan dapat mengumpulkan poin.</p>
            <a href="{{ route('login') }}" class="inline-block px-5 py-2 border border-primary-600 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition">Masuk / Daftar</a>
        </div>
    </div>
</section>

{{-- Room Types --}}
@if($roomTypes->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Tipe Kamar</h2>
        <p class="text-gray-500 mt-2">Pilih kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition group">
            @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
            @if($cover)
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ Storage::disk('public')->url($cover->path) }}" alt="Kamar {{ $type->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
            @else
                <div class="aspect-[4/3] bg-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400 text-sm mt-2">Foto belum tersedia</span>
                </div>
            @endif
            <div class="p-5">
                <h3 class="text-lg font-semibold text-gray-800">{{ $type->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $type->capacity }} tamu &middot; {{ $type->bed_count }} tempat tidur {{ $type->bed_type ?? '' }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <span class="text-primary-600 font-bold text-lg">Rp{{ number_format($type->base_price, 0, ',', '.') }}</span>
                        <span class="text-sm text-gray-400">/ malam</span>
                    </div>
                    <a href="{{ route('rooms.show', $type->slug) }}" class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Why Choose Us --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-800 text-center mb-8">Kenapa Menginap di Sini</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="text-center">
                <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-medium text-gray-800 text-sm">Lokasi di Kota Bangun</h3>
                <p class="text-xs text-gray-500 mt-1">Akses mudah ke pusat kota</p>
            </div>
            <div class="text-center">
                <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-medium text-gray-800 text-sm">Pemesanan Mudah</h3>
                <p class="text-xs text-gray-500 mt-1">Pesan langsung tanpa akun</p>
            </div>
            <div class="text-center">
                <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-medium text-gray-800 text-sm">Informasi Kamar Jelas</h3>
                <p class="text-xs text-gray-500 mt-1">Foto, fasilitas, dan harga transparan</p>
            </div>
            <div class="text-center">
                <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                </div>
                <h3 class="font-medium text-gray-800 text-sm">Kontak WhatsApp</h3>
                <p class="text-xs text-gray-500 mt-1">Tanya langsung kapan saja</p>
            </div>
        </div>
    </div>
</section>

{{-- Property Info --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Tentang Penginapan</h2>
        <p class="text-gray-600 text-sm leading-relaxed">
            Penginapan satu lantai di Kota Bangun II dengan {{ $activeRoomCount }} kamar aktif yang siap melayani tamu.
            Kamar terus dikembangkan untuk memenuhi kebutuhan pengunjung.
        </p>
        @if($shortDescription)
        <p class="text-gray-600 text-sm leading-relaxed mt-2">{{ $shortDescription }}</p>
        @endif
    </div>
</section>

{{-- Location Preview --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Lokasi</h2>
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex-1">
                <p class="text-sm text-gray-700 font-medium">{{ $propertyName }}</p>
                @if($address)
                    <p class="text-sm text-gray-500 mt-1">{{ $address }}</p>
                @else
                    <p class="text-sm text-gray-500 mt-1">Kota Bangun II, Kecamatan Kota Bangun, Kabupaten Kutai Kartanegara, Kalimantan Timur</p>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                @php $googleLink = $mapLink ?: $mapUrl; @endphp
                @if($googleLink)
                <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Buka di Google Maps
                </a>
                @endif
                <a href="{{ route('location') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Lihat Detail Lokasi
                </a>
            </div>
        </div>
    </div>
</section>

{{-- WhatsApp CTA --}}
@php $waUrl = \App\Support\WhatsApp::url($whatsapp, 'Halo, saya ingin bertanya tentang ketersediaan kamar di Penginapan Kelapa Sawit.'); @endphp
@if($waUrl)
<section class="bg-green-50 border-y border-green-100 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Butuh bantuan sebelum memesan?</h2>
        <p class="text-gray-600 text-sm mb-5">Hubungi kami langsung via WhatsApp.</p>
        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            Chat Penginapan
        </a>
    </div>
</section>
@endif

{{-- Policy Summary --}}
@if($policy)
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Informasi Penting</h2>
        <p class="text-sm text-gray-500 mb-4">Ringkasan kebijakan penginapan sebelum Anda memesan.</p>
        <div class="text-sm text-gray-600 leading-relaxed line-clamp-4">
            {!! Str::limit(strip_tags($policy->content), 300) !!}
        </div>
        <a href="{{ route('policy') }}" class="inline-flex items-center mt-4 text-sm text-primary-600 hover:text-primary-800 font-medium">
            Lihat Kebijakan Lengkap
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
@endif

{{-- Welcome Choice (First Visit) --}}
@guest
<div x-data="{ showWelcome: false }"
     x-init="
        if (!localStorage.getItem('kelapa_sawit_welcomed')) {
            setTimeout(() => showWelcome = true, 800);
        }
     "
     @keydown.escape.window="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())">

    {{-- Desktop Modal --}}
    <template x-teleport="body">
        <div x-show="showWelcome" x-cloak class="fixed inset-0 z-50 hidden md:flex items-center justify-center">
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                 class="fixed inset-0 bg-black/40"></div>
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-7 z-10" role="dialog" aria-modal="true" aria-labelledby="welcome-title">
                <h2 id="welcome-title" class="text-xl font-bold text-gray-900 mb-2">Selamat datang di Penginapan Kelapa Sawit</h2>
                <p class="text-sm text-gray-600 mb-6">Pesan kamar langsung tanpa akun, atau masuk agar data booking tersimpan dan Anda dapat mengumpulkan poin.</p>
                <div class="space-y-3">
                    <a href="#cari-kamar" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-2.5 text-center text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        Pesan Kamar
                    </a>
                    <a href="{{ route('login') }}" @click="localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-2.5 text-center text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Masuk / Daftar
                    </a>
                    <button type="button" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                            class="block w-full px-4 py-2 text-center text-sm text-gray-500 hover:text-gray-700 transition">
                        Lihat Dulu
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Mobile Bottom Sheet --}}
    <template x-teleport="body">
        <div x-show="showWelcome" x-cloak class="fixed inset-0 z-50 md:hidden flex items-end">
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                 class="fixed inset-0 bg-black/40"></div>
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                 class="relative w-full bg-white rounded-t-2xl shadow-xl p-6 pb-8 z-10 safe-area-bottom" role="dialog" aria-modal="true" aria-labelledby="welcome-title-mobile">
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-5"></div>
                <h2 id="welcome-title-mobile" class="text-lg font-bold text-gray-900 mb-2">Selamat datang di Penginapan Kelapa Sawit</h2>
                <p class="text-sm text-gray-600 mb-5">Pesan kamar langsung tanpa akun, atau masuk agar data booking tersimpan dan Anda dapat mengumpulkan poin.</p>
                <div class="space-y-3">
                    <a href="#cari-kamar" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        Pesan Kamar
                    </a>
                    <a href="{{ route('login') }}" @click="localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-3 text-center text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Masuk / Daftar
                    </a>
                    <button type="button" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                            class="block w-full px-4 py-2 text-center text-sm text-gray-500 hover:text-gray-700 transition">
                        Lihat Dulu
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endguest
@endsection
