@extends('layouts.admin')

@section('title', 'Laporan Okupansi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Okupansi</h1>
    <a href="{{ route('admin.reports.occupancy.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Unduh CSV
    </a>
</div>

{{-- Date Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap"
          x-data="{
              start: '{{ $startDate->toDateString() }}',
              end: '{{ $endDate->toDateString() }}'
          }"
          x-init="$watch('start', val => { if (end < val) end = val; })">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Dari</label>
            <input type="date" name="start_date" x-model="start" class="border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Sampai</label>
            <input type="date" name="end_date" x-model="end" :min="start" class="border-gray-300 rounded-md text-sm">
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
