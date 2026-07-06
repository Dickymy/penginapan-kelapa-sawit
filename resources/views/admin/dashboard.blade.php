@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

<div class="grid md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Booking Hari Ini</h3>
        <p class="text-2xl font-bold text-gray-800">0</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Check-in Hari Ini</h3>
        <p class="text-2xl font-bold text-gray-800">0</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Kamar Terisi</h3>
        <p class="text-2xl font-bold text-gray-800">0</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Pendapatan Bulan Ini</h3>
        <p class="text-2xl font-bold text-gray-800">Rp0</p>
    </div>
</div>

<x-empty-state message="Fitur dashboard akan menampilkan data operasional setelah sistem booking aktif." />
@endsection
