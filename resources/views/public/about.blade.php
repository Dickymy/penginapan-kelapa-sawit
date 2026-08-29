@extends('layouts.public')

@section('title', 'Tentang - ' . $propertyName)

@section('content')
{{-- Page Hero --}}
<section class="relative bg-gray-900 text-white py-24 lg:py-32 overflow-hidden">
    {{-- Hero Background Image --}}
    <img src="{{ asset('images/hero.webp') }}" 
         alt="{{ $propertyName }}" 
         class="absolute inset-0 w-full h-full object-cover opacity-40">
    
    {{-- Gradient Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 tracking-tight drop-shadow-lg">Tentang Kami</h1>
        <p class="text-lg md:text-xl text-gray-200 drop-shadow-md">Menghadirkan kenyamanan seperti di rumah sendiri di jantung Kota Bangun II.</p>
    </div>
</section>

{{-- Main Story Section --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Kenyamanan Anda Adalah Prioritas Kami</h2>
            <div class="prose prose-lg text-gray-600">
                <p>
                    {{ $propertyName }} hadir untuk menjawab kebutuhan akomodasi yang bersih, aman, dan nyaman di Kota Bangun II, Kutai Kartanegara. Berawal dari keinginan kami untuk memberikan pengalaman menginap terbaik bagi para wisatawan maupun pekerja profesional yang singgah di daerah ini.
                </p>
                <p>
                    Sebagai penginapan satu lantai yang mengedepankan suasana tenang dan asri, kami merancang setiap sudut dengan cermat. Mulai dari kamar yang luas, tempat tidur berkualitas tinggi, hingga fasilitas modern yang terawat dengan sangat baik.
                </p>
            </div>
            <div class="mt-8 flex gap-4">
                <div class="bg-primary-50 px-6 py-4 rounded-xl border border-primary-100">
                    <span class="block text-3xl font-extrabold text-primary-600 mb-1">{{ $activeRoomCount ?? '10+' }}</span>
                    <span class="text-sm font-medium text-gray-600">Kamar Nyaman</span>
                </div>
                <div class="bg-primary-50 px-6 py-4 rounded-xl border border-primary-100">
                    <span class="block text-3xl font-extrabold text-primary-600 mb-1">24/7</span>
                    <span class="text-sm font-medium text-gray-600">Pelayanan Prima</span>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
                <img src="{{ asset('images/hero.webp') }}" alt="Suasana {{ $propertyName }}" class="w-full h-full object-cover" width="800" height="600" loading="lazy" decoding="async">
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-gray-100 hidden md:block">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Top Rated</p>
                        <p class="text-sm text-gray-500">Pilihan Utama di Kota Bangun</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Keunggulan (USP) Section --}}
<section class="bg-gray-50 py-16 lg:py-24 border-y border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Mengapa Memilih Kami?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Kami memastikan setiap tamu mendapatkan pengalaman menginap yang tak terlupakan melalui fasilitas dan pelayanan terbaik.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Lingkungan Tenang</h3>
                <p class="text-gray-600">Berada di area yang jauh dari kebisingan, menjamin istirahat Anda maksimal setelah seharian beraktivitas.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Keamanan Terjamin</h3>
                <p class="text-gray-600">Sistem keamanan 24 jam dengan pemantauan CCTV dan area parkir tertutup yang aman untuk kendaraan Anda.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Harga Terjangkau</h3>
                <p class="text-gray-600">Menawarkan fasilitas premium dengan harga yang sangat kompetitif. Rasakan kemewahan tanpa harus menguras kantong.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Siap Untuk Menginap?</h2>
    <p class="text-lg text-gray-600 mb-10">Pesan kamar Anda sekarang dan nikmati pengalaman menginap tak terlupakan bersama kami.</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 transition-all duration-300">
            Lihat Kamar Tersedia
        </a>
        <a href="{{ route('location') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-800 rounded-xl font-bold hover:border-primary-600 hover:text-primary-600 transition-all duration-300">
            Hubungi Kami
        </a>
    </div>
</section>
@endsection
