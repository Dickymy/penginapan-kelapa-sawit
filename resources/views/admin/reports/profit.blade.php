@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Laba Rugi</h1>

{{-- Date Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Dari</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Sampai</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border-gray-300 rounded-md text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">Filter</button>
    </form>
</div>

{{-- Summary --}}
<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Pendapatan</h2>
        <p class="text-2xl font-bold text-green-600">Rp{{ number_format($revenue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Pengeluaran</h2>
        <p class="text-2xl font-bold text-red-600">Rp{{ number_format($expenses, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm text-gray-500 uppercase mb-1">Laba Bersih</h2>
        <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
            Rp{{ number_format(abs($profit), 0, ',', '.') }}{{ $profit < 0 ? ' (Rugi)' : '' }}
        </p>
    </div>
</div>

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-700">
    <strong>Catatan:</strong> Estimasi operasional, bukan laporan akuntansi resmi.
</div>

<p class="text-xs text-gray-400 mt-4">
    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
</p>
@endsection
