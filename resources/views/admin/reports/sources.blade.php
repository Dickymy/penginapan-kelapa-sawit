@extends('layouts.admin')

@section('title', 'Laporan Sumber Booking')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Sumber Booking</h1>

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

{{-- Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-5 py-3 text-left">Sumber</th>
                <th class="px-5 py-3 text-right">Jumlah Booking</th>
                <th class="px-5 py-3 text-right">Total Pendapatan</th>
                <th class="px-5 py-3 text-right">Rata-rata</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data as $source => $info)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">{{ ucfirst(str_replace('_', ' ', $source)) }}</td>
                <td class="px-5 py-3 text-right">{{ $info['count'] }}</td>
                <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($info['revenue'], 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right">Rp{{ number_format($info['average'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="text-xs text-gray-400 mt-4">
    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
</p>
@endsection
