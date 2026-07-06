@extends('layouts.member')

@section('title', 'Poin Saya')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Poin Loyalitas</h1>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Saldo Poin</h3>
        <p class="text-3xl font-bold text-primary-700">{{ number_format($balance) }}</p>
        <p class="text-xs text-gray-400 mt-1">≈ Rp{{ number_format($balance * $pointValue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Informasi</h3>
        <ul class="text-xs text-gray-500 space-y-1 mt-2">
            <li>• Setiap Rp{{ number_format(config('loyalty.earn_divisor', 1000), 0, ',', '.') }} pembayaran = 1 poin</li>
            <li>• 1 poin = Rp{{ number_format($pointValue, 0, ',', '.') }} saat redeem</li>
            <li>• Minimum redeem: {{ number_format(config('loyalty.min_redeem', 100)) }} poin</li>
            <li>• Maksimum {{ config('loyalty.max_redemption_percent', 20) }}% dari subtotal booking</li>
            <li>• Poin berlaku {{ config('loyalty.expiry_months', 18) }} bulan dari tanggal perolehan</li>
        </ul>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-medium text-gray-700">Riwayat Transaksi Poin</h3>
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
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada transaksi poin. Booking kamar untuk mulai mengumpulkan poin!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transactions->links() }}
</div>
@endsection
