@extends('layouts.public')

@section('title', 'Kebijakan & Tata Tertib Penginapan — Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Tata tertib menginap, waktu check-in & check-out, ketentuan pembatalan reservasi, dan komitmen privasi tamu di Penginapan Kelapa Sawit, Kota Bangun II.">
<link rel="canonical" href="{{ route('policy') }}">
@endsection

@section('content')
{{-- Reading Progress Bar --}}
<div class="fixed top-0 left-0 right-0 h-1 bg-transparent z-50 pointer-events-none"
     x-data="{ progress: 0 }"
     @scroll.window.passive="progress = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100">
    <div class="h-full bg-primary-600 transition-all duration-150 ease-out" :style="`width: ${progress}%`"></div>
</div>

{{-- Page Hero --}}
<section class="relative text-white py-14 md:py-20 bg-primary-950 overflow-hidden"
         x-data="{ scrollY: 0 }" 
         @scroll.window.passive="scrollY = window.scrollY">
    <x-hero-background />

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center will-change-transform"
         :style="`transform: translateY(${scrollY * 0.15}px); opacity: ${Math.max(0, 1 - scrollY / 380)}`">
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-primary-200 border border-white/20 mb-3 shadow-xs">
            <svg class="w-3.5 h-3.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Panduan &amp; Tata Tertib Tamu</span>
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-md">
            Kebijakan Penginapan
        </h1>
        <p class="mt-3 text-sm sm:text-base text-gray-200 max-w-2xl mx-auto leading-relaxed drop-shadow-xs">
            Panduan sederhana untuk menjaga kenyamanan, keamanan, dan ketenangan seluruh tamu selama menginap di Penginapan Kelapa Sawit.
        </p>

        @if($policy)
        <div class="mt-5 inline-flex flex-wrap items-center justify-center gap-2 text-xs text-primary-200 bg-primary-900/60 backdrop-blur-md px-4 py-1.5 rounded-full border border-primary-700/50">
            <span class="font-medium text-white">Versi {{ $policy->version }}</span>
            <span>&bull;</span>
            <span>Terakhir Diperbarui: {{ $policy->published_at ? $policy->published_at->translatedFormat('d F Y') : 'Terbaru' }}</span>
        </div>
        @endif
    </div>
</section>

{{-- Policy Content Container --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 space-y-8">
    @if($policy && count($sections) > 0)

        {{-- Welcome Note Card --}}
        <div class="bg-gradient-to-r from-primary-50/70 via-white to-primary-50/40 rounded-2xl p-5 sm:p-6 border border-primary-100/80 shadow-xs flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                <p class="font-bold text-gray-900 text-sm sm:text-base mb-1">Selamat Datang di Penginapan Kelapa Sawit</p>
                <p>Kami berkomitmen memberikan pengalaman menginap yang bersih, tenang, dan menyenangkan. Tata tertib ini dirancang untuk menjaga kenyamanan bersama antara Anda, tamu lainnya, dan staf kami. Terima kasih atas kerja sama dan pengertian Anda.</p>
            </div>
        </div>

        {{-- Main Document Card --}}
        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xs p-6 sm:p-10 divide-y divide-gray-100">
            @foreach($sections as $index => $sec)
            <div id="{{ $sec['id'] }}" class="py-8 first:pt-2 last:pb-2">
                {{-- Section Header --}}
                <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                            {{ $sec['color'] === 'amber' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $sec['color'] === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $sec['color'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $sec['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ $sec['color'] === 'primary' ? 'bg-primary-100 text-primary-700' : '' }}">
                            
                            @if($sec['icon'] === 'calendar-cancel')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM10 15l4 4m0-4l-4 4"/></svg>
                            @elseif($sec['icon'] === 'shield')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @elseif($sec['icon'] === 'credit-card')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            @elseif($sec['icon'] === 'clock')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @endif
                        </div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">
                            {{ $sec['title'] }}
                        </h2>
                    </div>

                    <span class="inline-flex text-[11px] font-semibold px-2.5 py-1 rounded-full border
                        {{ $sec['color'] === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                        {{ $sec['color'] === 'blue' ? 'bg-blue-50 text-blue-800 border-blue-200' : '' }}
                        {{ $sec['color'] === 'emerald' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : '' }}
                        {{ $sec['color'] === 'indigo' ? 'bg-indigo-50 text-indigo-800 border-indigo-200' : '' }}
                        {{ $sec['color'] === 'primary' ? 'bg-primary-50 text-primary-800 border-primary-200' : '' }}">
                        {{ $sec['badge'] }}
                    </span>
                </div>

                {{-- Section Body Items --}}
                <div class="space-y-3.5 pl-0 sm:pl-13">
                    @foreach($sec['items'] as $item)
                        @if($item['type'] === 'numbered')
                            <div class="flex items-start gap-3.5">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 bg-primary-50 text-primary-700 border border-primary-200">
                                    {{ $item['number'] }}
                                </span>
                                <div class="flex-1 text-xs sm:text-sm text-gray-700 leading-relaxed pt-0.5">
                                    {!! nl2br(e($item['text'])) !!}
                                </div>
                            </div>
                        @elseif($item['type'] === 'bullet')
                            <div class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-primary-500 shrink-0 mt-2"></span>
                                <div class="flex-1 text-xs sm:text-sm text-gray-700 leading-relaxed">
                                    {!! nl2br(e($item['text'])) !!}
                                </div>
                            </div>
                        @else
                            {{-- Paragraph --}}
                            <div class="text-xs sm:text-sm text-gray-700 leading-relaxed bg-gray-50/70 p-4 rounded-xl border border-gray-100">
                                {!! nl2br(e($item['text'])) !!}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="pt-2 text-xs text-gray-500 text-center sm:text-left">
            <span>Dokumen ini berlaku untuk seluruh reservasi langsung maupun melalui staf Penginapan Kelapa Sawit.</span>
        </div>

    @else
        {{-- Empty / Maintenance State --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-8 sm:p-12 text-center max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Kebijakan Sedang Diperbarui</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
                Manajemen sedang memperbarui dokumen kebijakan penginapan. Silakan hubungi staf kami jika membutuhkan informasi lebih lanjut sebelum melakukan pemesanan.
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <a href="{{ route('home') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif

    {{-- Help & Contact Box --}}
    <div class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 rounded-3xl p-6 sm:p-8 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-5">
        <div class="text-center sm:text-left">
            <h3 class="text-base sm:text-lg font-bold tracking-tight">Ada Pertanyaan Mengenai Ketentuan Kami?</h3>
            <p class="text-xs sm:text-sm text-primary-200 mt-1 max-w-md leading-relaxed">
                Tim resepsionis kami siap membantu memberikan klarifikasi dan solusi ramah untuk rencana menginap Anda.
            </p>
        </div>
        <div class="shrink-0">
            @php $waClean = \App\Support\Phone\PhoneNormalizer::normalize($whatsapp); @endphp
            @if($waClean)
            <a href="https://wa.me/{{ $waClean }}?text={{ urlencode('Halo Resepsionis Penginapan Kelapa Sawit, saya ingin menanyakan perihal ketentuan menginap.') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Tanya Resepsionis via WhatsApp</span>
            </a>
            @else
            <a href="{{ route('location') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white hover:bg-gray-100 text-gray-900 font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                <span>Lihat Kontak &amp; Lokasi</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
