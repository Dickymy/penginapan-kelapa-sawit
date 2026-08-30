@extends('layouts.public')

@section('title', 'Tentang - ' . $propertyName)

@section('content')
{{-- Modern Typographic Hero --}}
<section class="relative bg-gradient-to-b from-primary-900 to-primary-800 text-white pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
    {{-- Abstract Background Patterns --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-10">
        <svg class="absolute -top-24 -right-24 w-96 h-96 text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
        <svg class="absolute top-1/2 -left-24 w-64 h-64 text-primary-300" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
    </div>
    
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <div class="inline-flex items-center justify-center px-5 py-2 bg-white/10 rounded-full backdrop-blur-md mb-8 border border-white/20">
            <svg class="w-5 h-5 text-primary-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="text-white font-bold tracking-wider uppercase text-sm">Tentang {{ $propertyName }}</span>
        </div>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold mb-8 tracking-tight leading-tight">
            Standar Baru <br class="hidden md:block"> <span class="text-primary-200">Kenyamanan Menginap</span>
        </h1>
        <p class="text-xl md:text-2xl text-primary-100 max-w-3xl mx-auto leading-relaxed font-light">
            Lebih dari sekadar tempat singgah. Kami merancang setiap detail untuk menghadirkan ketenangan, keamanan, dan kehangatan seperti di rumah sendiri.
        </p>
    </div>
</section>

{{-- Filosofi & Visi Section --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-28">
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 p-8 md:p-12 lg:p-16 relative -mt-16 md:-mt-24 z-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Dedikasi Untuk Pelayanan Sempurna</h2>
            <div class="w-24 h-1.5 bg-primary-500 mx-auto rounded-full"></div>
        </div>
        
        <div class="prose prose-lg md:prose-xl text-gray-600 max-w-none text-center leading-relaxed font-medium">
            <p class="mb-8">
                Berlokasi strategis di jantung Kota Bangun II, Kutai Kartanegara, <strong>{{ $propertyName }}</strong> lahir dari visi sederhana: menciptakan oase peristirahatan yang mengerti kebutuhan tamu modern. Baik Anda seorang profesional yang sedang dalam perjalanan dinas, maupun wisatawan yang sedang menjelajahi keindahan daerah ini.
            </p>
            <p>
                Sebagai penginapan eksklusif satu lantai, kami meniadakan hiruk-pikuk bangunan bertingkat untuk memaksimalkan ketenangan. Dengan fasilitas modern yang selalu dirawat sepenuh hati, kami berkomitmen menjadi pilihan nomor satu yang selalu Anda rindukan setiap kali berkunjung ke Kota Bangun.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 pt-12 border-t border-gray-100">
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <span class="block text-4xl font-extrabold text-primary-600 mb-2">{{ $activeRoomCount ?? '10+' }}</span>
                <span class="text-gray-900 font-bold text-lg">Kamar Eksklusif</span>
            </div>
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <span class="block text-4xl font-extrabold text-primary-600 mb-2">24/7</span>
                <span class="text-gray-900 font-bold text-lg">Layanan & Keamanan</span>
            </div>
            <div class="text-center p-6 bg-gray-50 rounded-2xl">
                <span class="block text-4xl font-extrabold text-primary-600 mb-2">100%</span>
                <span class="text-gray-900 font-bold text-lg">Fokus Kepuasan</span>
            </div>
        </div>
    </div>
</section>

{{-- Keunggulan (USP) Section Typography Driven --}}
<section class="pb-16 lg:pb-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Nilai Lebih Untuk Anda</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tiga pilar utama yang menjadikan kami berbeda dan selalu menjadi pilihan terbaik.</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Card 1 --}}
            <div class="bg-white border-2 border-gray-50 hover:border-primary-100 p-10 rounded-[2rem] shadow-sm hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] transition-all duration-500 group text-center">
                <div class="w-20 h-20 mx-auto bg-primary-50 text-primary-600 rounded-3xl flex items-center justify-center mb-8 group-hover:-translate-y-2 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Lingkungan Tenang</h3>
                <p class="text-gray-600 leading-relaxed text-lg">
                    Desain bangunan 1 lantai yang tertata rapi menciptakan atmosfer sunyi dan rileks, memastikan istirahat Anda benar-benar maksimal tanpa gangguan.
                </p>
            </div>
            
            {{-- Card 2 --}}
            <div class="bg-white border-2 border-gray-50 hover:border-primary-100 p-10 rounded-[2rem] shadow-sm hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] transition-all duration-500 group text-center">
                <div class="w-20 h-20 mx-auto bg-primary-50 text-primary-600 rounded-3xl flex items-center justify-center mb-8 group-hover:-translate-y-2 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Privasi & Keamanan</h3>
                <p class="text-gray-600 leading-relaxed text-lg">
                    Keamanan tamu adalah keutamaan. Dengan pengawasan CCTV 24 jam dan area parkir tertutup, privasi serta kendaraan Anda terjamin keamanannya.
                </p>
            </div>
            
            {{-- Card 3 --}}
            <div class="bg-white border-2 border-gray-50 hover:border-primary-100 p-10 rounded-[2rem] shadow-sm hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] transition-all duration-500 group text-center">
                <div class="w-20 h-20 mx-auto bg-primary-50 text-primary-600 rounded-3xl flex items-center justify-center mb-8 group-hover:-translate-y-2 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Harga Kompetitif</h3>
                <p class="text-gray-600 leading-relaxed text-lg">
                    Menghadirkan standar pelayanan dan fasilitas kamar setara hotel berbintang, namun dengan harga sewa yang sangat masuk akal dan ramah di kantong.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="bg-gray-900 py-20 rounded-[3rem] mx-4 sm:mx-6 lg:mx-8 mb-12 overflow-hidden relative">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 pointer-events-none opacity-20">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-500 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary-600 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6">Mulai Pengalaman Menginap Anda</h2>
        <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">Buktikan sendiri kenyamanan kelas atas yang telah kami siapkan khusus untuk Anda.</p>
        <div class="flex flex-col sm:flex-row gap-5 justify-center">
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center px-10 py-4 bg-primary-500 text-white rounded-2xl font-bold text-lg hover:bg-primary-400 hover:shadow-[0_0_20px_rgba(56,_189,_248,_0.4)] transition-all duration-300">
                Lihat Kamar
            </a>
            <a href="{{ route('location') }}" class="inline-flex items-center justify-center px-10 py-4 bg-transparent border-2 border-gray-600 text-white rounded-2xl font-bold text-lg hover:border-white hover:bg-white hover:text-gray-900 transition-all duration-300">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>
@endsection
