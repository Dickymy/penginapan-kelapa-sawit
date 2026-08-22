@extends('layouts.admin')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Pendapatan</h1>
    <a href="{{ route('admin.reports.revenue.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Unduh CSV
    </a>
</div>

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

{{-- Total --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-sm text-gray-500 uppercase mb-1">Total Pendapatan</h2>
    <p class="text-3xl font-bold text-gray-800">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
    <p class="text-xs text-gray-400 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
</div>

{{-- By Source --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800">Pendapatan per Sumber</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-5 py-3 text-left">Sumber</th>
                <th class="px-5 py-3 text-right">Jumlah Booking</th>
                <th class="px-5 py-3 text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($bySource as $source => $info)
            <tr>
                <td class="px-5 py-3">{{ ucfirst(str_replace('_', ' ', $source)) }}</td>
                <td class="px-5 py-3 text-right">{{ $info['count'] }}</td>
                <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($info['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-5 py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
