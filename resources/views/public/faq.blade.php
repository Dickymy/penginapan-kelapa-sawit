@extends('layouts.public')

@section('title', 'FAQ — Pertanyaan yang Sering Diajukan')

@section('meta')
<meta name="description" content="Temukan jawaban atas pertanyaan yang sering diajukan mengenai pemesanan, fasilitas, pembayaran, dan kebijakan di Penginapan Kelapa Sawit.">
<link rel="canonical" href="{{ route('faq') }}">
@endsection

@section('content')
<section class="bg-primary-700 text-white py-12 md:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">FAQ</h1>
        <p class="text-primary-100 max-w-2xl mx-auto text-lg">Pertanyaan yang Sering Diajukan</p>
    </div>
</section>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($faqs->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Belum ada FAQ</h2>
            <p class="text-gray-500 mt-2">Hubungi kami via WhatsApp jika ada pertanyaan!</p>
            @php $waUrl = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo, saya ingin bertanya tentang penginapan.'); @endphp
            @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-block mt-4 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Chat WhatsApp
                </a>
            @endif
        </div>
    @else
        <div class="space-y-10">
            @foreach($faqs as $category => $items)
                <div x-data="{ active: null }">
                    @if($category)
                        <h2 class="text-xl font-bold text-gray-800 mb-4">{{ ucfirst($category === 'general' ? 'umum' : $category) }}</h2>
                    @endif
                    
                    <div class="space-y-4">
                        @foreach($items as $index => $faq)
                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                            <button @click="active !== {{ $faq->id }} ? active = {{ $faq->id }} : active = null" 
                                    class="w-full px-5 py-4 text-left flex justify-between items-center focus:outline-none hover:bg-gray-50 transition">
                                <span class="font-medium text-gray-900 pr-4">{{ $faq->question }}</span>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                                     :class="{'rotate-180': active === {{ $faq->id }}}" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="active === {{ $faq->id }}" 
                                 x-collapse
                                 x-cloak>
                                <div class="px-5 pb-4 pt-1 text-gray-600 prose prose-sm max-w-none">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-12 text-center p-6 bg-gray-50 rounded-xl border border-gray-100">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Masih punya pertanyaan?</h3>
            <p class="text-gray-600 text-sm mb-4">Tim kami siap membantu Anda dengan informasi yang lebih detail.</p>
            @php $waUrl = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo, saya punya pertanyaan lebih lanjut mengenai penginapan.'); @endphp
            @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-6 py-2.5 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition font-medium">
                    Hubungi Kami via WhatsApp
                </a>
            @endif
        </div>
    @endif
</section>
@endsection
