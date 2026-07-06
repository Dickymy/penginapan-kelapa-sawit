@extends('layouts.public')

@section('title', 'Lupa Password - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Lupa Password</h1>
    <p class="text-sm text-gray-600 mb-6 text-center">Masukkan email Anda dan kami akan mengirim link reset password.</p>

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <x-form-error field="email" />
        </div>

        <x-button class="w-full justify-center">Kirim Link Reset</x-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-800">Kembali ke halaman masuk</a>
    </p>
</div>
@endsection
