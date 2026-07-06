@extends('layouts.public')

@section('title', 'Verifikasi Email - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Verifikasi Email</h1>
    <p class="text-sm text-gray-600 mb-6 text-center">
        Kami telah mengirim link verifikasi ke email Anda. Silakan cek inbox dan klik link tersebut untuk mengaktifkan akun.
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" message="Link verifikasi baru telah dikirim ke email Anda." />
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="text-center">
        @csrf
        <x-button>Kirim Ulang Link Verifikasi</x-button>
    </form>
</div>
@endsection
