@extends('layouts.public')

@section('title', 'FAQ — Pertanyaan yang Sering Diajukan — Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Temukan jawaban atas pertanyaan yang sering diajukan mengenai pemesanan, fasilitas, pembayaran, dan kebijakan di Penginapan Kelapa Sawit, Kota Bangun II.">
<link rel="canonical" href="{{ route('faq') }}">
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
            <svg class="w-3.5 h-3.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Pusat Bantuan &amp; Informasi Tamu</span>
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-md">
            Pertanyaan yang Sering Diajukan
        </h1>
        <p class="mt-3 text-sm sm:text-base text-gray-200 max-w-xl mx-auto leading-relaxed drop-shadow-xs">
            Jawaban praktis dan cepat mengenai reservasi kamar, fasilitas, waktu check-in, hingga kemudahan metode pembayaran.
        </p>
    </div>
</section>

{{-- FAQ Interactive Container --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 space-y-8"
     x-data="{
         activeCategory: 'all',
         activeId: null
     }">

    @if($faqs->isEmpty())
        <div class="bg-white border border-gray-200 rounded-2xl p-8 sm:p-12 text-center max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-4 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Belum Ada FAQ</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
                Informasi tanya-jawab sedang disiapkan. Jika Anda membutuhkan informasi segera, silakan hubungi resepsionis kami.
            </p>
            @php $waUrl = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo, saya ingin bertanya tentang penginapan.'); @endphp
            @if($waUrl)
                <div class="mt-6">
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">
                        Chat Resepsionis via WhatsApp
                    </a>
                </div>
            @endif
        </div>
    @else
        {{-- Category Filter Pills --}}
        @if($faqs->count() > 1)
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar no-scrollbar py-1">
            <button type="button"
                    @click="activeCategory = 'all'"
                    :class="activeCategory === 'all' ? 'bg-primary-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer">
                Semua Topik
            </button>
            @foreach($faqs->keys() as $category)
            <button type="button"
                    @click="activeCategory = '{{ $category }}'"
                    :class="activeCategory === '{{ $category }}' ? 'bg-primary-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer capitalize">
                {{ $category === 'general' ? 'Informasi Umum' : ucfirst($category) }}
            </button>
            @endforeach
        </div>
        @endif

        {{-- Questions List Grouped by Category --}}
        <div class="space-y-8">
            @foreach($faqs as $category => $items)
            <div x-show="activeCategory === 'all' || activeCategory === '{{ $category }}'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                @if($category)
                <div class="flex items-center gap-2.5 mb-4">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 capitalize">
                        {{ $category === 'general' ? 'Informasi Umum & Kebijakan' : ucfirst($category) }}
                    </h2>
                </div>
                @endif

                <div class="space-y-3">
                    @foreach($items as $faq)
                    <div class="bg-white border rounded-2xl transition-all duration-200 overflow-hidden"
                         :class="activeId === {{ $faq->id }} ? 'border-primary-300 shadow-sm ring-2 ring-primary-500/10' : 'border-gray-200/90 hover:border-gray-300 shadow-xs'">
                        
                        <button type="button"
                                @click="activeId === {{ $faq->id }} ? activeId = null : activeId = {{ $faq->id }}"
                                class="w-full px-5 py-4 text-left flex justify-between items-center gap-4 focus:outline-none transition-colors duration-200 cursor-pointer"
                                :class="activeId === {{ $faq->id }} ? 'bg-primary-50/40 text-primary-900' : 'text-gray-900 hover:bg-gray-50/80'">
                            <span class="font-semibold text-sm sm:text-base leading-snug pr-2"
                                  :class="activeId === {{ $faq->id }} ? 'text-primary-800' : 'text-gray-900'">
                                {{ $faq->question }}
                            </span>
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                 :class="activeId === {{ $faq->id }} ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-500'">
                                <svg class="w-4 h-4 transform transition-transform duration-200" 
                                     :class="{'rotate-180': activeId === {{ $faq->id }}}" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <div x-show="activeId === {{ $faq->id }}" 
                             x-collapse
                             x-cloak>
                            <div class="px-5 pb-5 pt-2 text-xs sm:text-sm text-gray-700 leading-relaxed border-t border-gray-100/80 bg-gray-50/30">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Still have questions CTA Box --}}
        <div class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 rounded-3xl p-6 sm:p-8 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="text-center sm:text-left">
                <h3 class="text-base sm:text-lg font-bold tracking-tight">Masih Memiliki Pertanyaan Lain?</h3>
                <p class="text-xs sm:text-sm text-primary-200 mt-1 max-w-md leading-relaxed">
                    Staf penginapan kami siap memberikan jawaban ramah dan informasi lengkap seputar kamar dan reservasi.
                </p>
            </div>
            <div class="shrink-0">
                @php $waUrl = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo Resepsionis Penginapan Kelapa Sawit, saya memiliki pertanyaan mengenai penginapan.'); @endphp
                @if($waUrl)
                <a href="{{ $waUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Hubungi Resepsionis via WhatsApp</span>
                </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
