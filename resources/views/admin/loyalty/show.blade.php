@extends('layouts.admin')

@section('title', 'Detail Poin - ' . $user->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Poin: {{ $user->name }}</h1>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>
    <a href="{{ route('admin.loyalty.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Kembali</a>
</div>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid md:grid-cols-2 gap-6 mb-6">
    {{-- Balance Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Saldo Poin Saat Ini</h3>
        <p class="text-3xl font-bold text-primary-700">{{ number_format($balance) }}</p>
        <p class="text-xs text-gray-400 mt-1">≈ Rp{{ number_format($balance * config('loyalty.point_value', 50), 0, ',', '.') }}</p>
    </div>

    {{-- Adjust Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-3">Penyesuaian Manual</h3>
        <form action="{{ route('admin.loyalty.adjust', $user) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Poin (+/-)</label>
                <input type="number" name="points" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="-100 atau 500">
                @error('points') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Alasan</label>
                <input type="text" name="reason" required maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Kompensasi keluhan tamu">
                @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700 transition">Simpan</button>
        </form>
    </div>
</div>

{{-- Ledger --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-medium text-gray-700">Riwayat Transaksi</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Poin</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $tx)
                <tr>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-3 text-sm">
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $tx->points > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $tx->type->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-700">{{ $tx->description }}</td>
                    <td class="px-6 py-3 text-sm text-right font-medium {{ $tx->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                    </td>
                    <td class="px-6 py-3 text-sm text-right text-gray-600">{{ number_format($tx->balance_after) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transactions->links() }}
</div>
@endsection
