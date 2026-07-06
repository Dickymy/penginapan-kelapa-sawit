@extends('layouts.member')

@section('title', 'Dashboard Member')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang, {{ auth()->user()->name }}!</h1>

<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Saldo Poin</h3>
        <p class="text-2xl font-bold text-primary-700">0</p>
        <p class="text-xs text-gray-400 mt-1">≈ Rp0</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Booking Aktif</h3>
        <p class="text-2xl font-bold text-gray-800">0</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Booking</h3>
        <p class="text-2xl font-bold text-gray-800">0</p>
    </div>
</div>

<div class="mt-8">
    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700 transition">
        Pesan Kamar
    </a>
</div>
@endsection
