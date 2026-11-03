@extends('layouts.public')

@section('title', 'Lokasi & Petunjuk Arah — Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Lokasi dan panduan rute menuju Penginapan Kelapa Sawit di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur. Akses jalan mulus dan parkir kendaraan luas.">
<link rel="canonical" href="{{ route('location') }}">
@endsection

@section('content')
{{-- Page Hero --}}
<section class="relative text-white py-14 md:py-20 bg-primary-950 overflow-hidden"
         x-data="{ scrollY: 0 }" 
         @scroll.window.passive="scrollY = window.scrollY">
    <x-hero-background />

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center will-change-transform"
         :style="`transform: translateY(${scrollY * 0.15}px); opacity: ${Math.max(0, 1 - scrollY / 380)}`">
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-primary-200 border border-white/20 mb-3 shadow-xs">
            <svg class="w-3.5 h-3.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Panduan Lokasi &amp; Aksesibilitas</span>
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-md">
            Lokasi Penginapan &amp; Petunjuk Arah
        </h1>
        <p class="mt-3 text-sm sm:text-base text-gray-200 max-w-xl mx-auto leading-relaxed drop-shadow-xs">
            Akomodasi tenang dan strategis di Kota Bangun II, Kutai Kartanegara. Akses jalan mulus dengan fasilitas parkir luas dan aman.
        </p>
    </div>
</section>

{{-- Main Location Container --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 space-y-10">
    @php 
        $googleLink = $mapLink ?: $mapUrl; 
        $fullAddress = $address ?: 'Kota Bangun II, Kecamatan Kota Bangun, Kabupaten Kutai Kartanegara, Kalimantan Timur';
    @endphp

    {{-- 2-Column Responsive Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left / Main Column (Map & Directions) --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Map Container Card --}}
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xs overflow-hidden">
                <div class="relative bg-gray-100">
                    @if($mapEmbedUrl)
                        <iframe src="{{ $mapEmbedUrl }}" width="100%" height="380" class="w-full border-0 block" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Penginapan Kelapa Sawit"></iframe>
                    @elseif($mapUrl && str_contains($mapUrl, 'google.com/maps/embed'))
                        <iframe src="{{ $mapUrl }}" width="100%" height="380" class="w-full border-0 block" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Penginapan Kelapa Sawit"></iframe>
                    @else
                        <div class="w-full bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center p-8 text-center" style="min-height: 320px;">
                            <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">Peta Interaktif</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm">Penginapan Kelapa Sawit terletak di kawasan Kota Bangun II, Kutai Kartanegara.</p>
                            @if($googleLink)
                            <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl text-xs font-semibold hover:bg-primary-700 transition">
                                <span>Buka di Google Maps Langsung</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Map Action Bar --}}
                <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50">
                    <span class="text-xs text-gray-500 text-center sm:text-left flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Gunakan Google Maps untuk navigasi GPS langsung ke lokasi.</span>
                    </span>
                    @if($googleLink)
                    <a href="{{ $googleLink }}" target="_blank" rel="noopener"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Navigasi Google Maps</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Cara Menuju ke Sini (Panduan Aksesibilitas) --}}
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xs p-6 sm:p-7 space-y-4">
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Panduan Akses &amp; Rute</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs sm:text-sm">
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">Akses Jalan Aspal</h4>
                            <p class="text-gray-600 leading-relaxed text-xs">Jalur jalan beraspal halus dan dapat dilalui segala jenis kendaraan roda 4 maupun roda 2 dengan nyaman.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">Parkir Luas &amp; Aman</h4>
                            <p class="text-gray-600 leading-relaxed text-xs">Tersedia area parkir pribadi di halaman penginapan tanpa biaya tambahan (gratis untuk tamu menginap).</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">Resepsionis Standby</h4>
                            <p class="text-gray-600 leading-relaxed text-xs">Jika Anda membutuhkan arahan selama di perjalanan, staf kami siap memandu langsung via WhatsApp.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right / Sidebar Column (Address, Contacts & Nearby) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Address Card with Copy Button --}}
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xs p-6 space-y-5"
                 x-data="{ copied: false }">
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-semibold border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Resepsionis Aktif Setiap Hari</span>
                    </div>
                    <span class="text-xs text-gray-400">Kota Bangun II</span>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-gray-900">Penginapan Kelapa Sawit</h2>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                            {{ $fullAddress }}
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                    <button type="button"
                            @click="
                                navigator.clipboard.writeText('{{ addslashes($fullAddress) }}');
                                copied = true;
                                setTimeout(() => copied = false, 2500);
                            "
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                        <svg x-show="!copied" class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="copied ? 'Alamat Tersalin! ✓' : 'Salin Alamat Lengkap'">Salin Alamat Lengkap</span>
                    </button>
                </div>
            </div>

            {{-- Direct Contacts Card --}}
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-xs text-gray-400">Kontak Langsung</h3>

                @if($whatsapp)
                @php 
                    $waClean = \App\Support\Phone\PhoneNormalizer::normalize($whatsapp);
                    $waDirectionsUrl = \App\Support\WhatsApp::url($whatsapp, 'Halo Penginapan Kelapa Sawit, saya ingin meminta petunjuk arah menuju lokasi penginapan.'); 
                @endphp
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-emerald-800">WhatsApp Resepsionis</p>
                        <p class="text-xs text-gray-600 mt-0.5 font-mono">{{ $whatsapp }}</p>
                        <div class="mt-2.5 flex items-center gap-2">
                            <a href="{{ $waDirectionsUrl }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-bold transition">
                                <span>Kirim Pesan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if($email)
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-700">Email Resmi</p>
                        <a href="mailto:{{ $email }}" class="text-xs text-primary-600 hover:underline block truncate mt-0.5">{{ $email }}</a>
                    </div>
                </div>
                @endif
            </div>

            {{-- Toko Sembako & Fasilitas Terdekat Banner --}}
            <div class="bg-gradient-to-br from-primary-900 to-primary-950 rounded-3xl p-6 text-white shadow-xs space-y-3">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-white/10 text-primary-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                    <h4 class="font-bold text-sm">Toko Sembako &amp; Kebutuhan</h4>
                </div>
                <p class="text-xs text-primary-200 leading-relaxed">
                    Penginapan kami juga mengelola toko sembako langsung di area penginapan untuk kemudahan kebutuhan sehari-hari tamu.
                </p>
                <div class="pt-1">
                    <a href="{{ route('nearby-places') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white hover:text-primary-200 transition underline underline-offset-4">
                        <span>Lihat Toko Sembako &amp; Tempat Sekitar</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
