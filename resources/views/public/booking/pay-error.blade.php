@extends('layouts.public')

@section('title', 'Pembayaran Tidak Dapat Diproses')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-3">Pembayaran Tidak Dapat Diproses</h1>
        <p class="text-gray-600 mb-6">{{ $message }}</p>

        <div class="space-y-3">
            <a href="{{ route('booking.pay', $booking->booking_code) }}"
               class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                Coba Lagi
            </a>

            @php
                $waNumber = \App\Models\Setting::get('contact', 'whatsapp', '');
                $waPayErrorUrl = \App\Support\WhatsApp::url($waNumber, 'Halo, saya butuh bantuan untuk booking ' . $booking->booking_code);
            @endphp
            @if($waPayErrorUrl)
            <a href="{{ $waPayErrorUrl }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                </svg>
                Hubungi WhatsApp
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
