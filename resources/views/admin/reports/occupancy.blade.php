@extends('layouts.admin')

@section('title', 'Laporan Okupansi')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Okupansi</h1>

{{-- Date Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Dari</label>
            <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Sampai</label>
            <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="border-gray-300 rounded-md text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">Filter</button>
    </form>
</div>

{{-- Occupancy Rate --}}
<div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Tingkat Okupansi</h2>
        <p class="text-3xl font-bold text-gray-800">{{ $occupancyRate }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Malam Kamar Terisi</h2>
        <p class="text-3xl font-bold text-gray-800">{{ $occupiedRoomNights }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Total Malam Kamar Tersedia</h2>
        <p class="text-3xl font-bold text-gray-800">{{ $availableRoomNights }}</p>
    </div>
</div>

<p class="text-xs text-gray-400 mt-4">
    Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
</p>
@endsection
