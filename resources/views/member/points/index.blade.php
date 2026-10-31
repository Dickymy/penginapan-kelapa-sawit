@extends('layouts.member')

@section('title', 'Poin Saya')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Poin Loyalitas</h1>

{{-- Saldo Poin Hero --}}
<div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl shadow-lg p-8 mb-8 text-white relative overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
    
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <h3 class="text-primary-100 font-medium mb-2 text-lg">Saldo Poin Anda</h3>
            <div class="flex items-baseline gap-3">
                <p class="text-5xl font-black tracking-tight">{{ number_format($balance) }}</p>
                <p class="text-primary-200 font-medium text-lg">poin</p>
            </div>
            <p class="mt-3 text-primary-50 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Setara dengan nilai tukar <strong class="text-white font-bold text-base bg-white/20 px-2 py-0.5 rounded ml-1">Rp{{ number_format($balance * $pointValue, 0, ',', '.') }}</strong>
            </p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-sm border border-white/20">
            <svg class="w-10 h-10 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
</div>

{{-- Info Grid --}}
<div class="grid md:grid-cols-2 gap-6 mb-8">
    {{-- Cara Mendapatkan Poin --}}
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8 border border-gray-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-sm border border-green-100">💰</div>
            <h3 class="text-lg font-bold text-gray-800">Cara Mendapatkan Poin</h3>
        </div>
        
        <ul class="text-sm text-gray-600 space-y-4 mb-6">
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Setiap pembayaran <strong class="text-gray-900 font-semibold bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200">Rp{{ number_format(config('loyalty.earn_divisor', 10), 0, ',', '.') }}</strong> akan mendapatkan <strong class="text-green-600 font-bold bg-green-50 px-1.5 py-0.5 rounded border border-green-100">1 poin</strong></span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Poin otomatis ditambahkan ke akun Anda setelah pembayaran booking dikonfirmasi</span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Poin berlaku selama <strong class="text-gray-900 font-semibold">{{ config('loyalty.expiry_months', 18) }} bulan</strong> sejak didapatkan</span>
            </li>
        </ul>

        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
            <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Simulasi Perolehan Poin
            </p>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Booking Rp150.000</span>
                    <span class="font-bold text-green-600">+15.000 poin</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Booking Rp300.000</span>
                    <span class="font-bold text-green-600">+30.000 poin</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Booking Rp1.000.000</span>
                    <span class="font-bold text-green-600">+100.000 poin</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cara Menukarkan Poin --}}
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8 border border-gray-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm border border-amber-100">🔄</div>
            <h3 class="text-lg font-bold text-gray-800">Cara Menukarkan Poin</h3>
        </div>
        
        <ul class="text-sm text-gray-600 space-y-4 mb-6">
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Setiap <strong class="text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">1 poin</strong> bernilai <strong class="text-gray-900 font-semibold bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200">Rp{{ number_format($pointValue, 0, ',', '.') }}</strong> yang bisa langsung memotong total tagihan Anda</span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Minimum penukaran poin adalah <strong class="text-gray-900 font-semibold">{{ number_format(config('loyalty.min_redeem', 100)) }} poin</strong></span>
            </li>
            <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Maksimal potongan yang diizinkan adalah <strong class="text-gray-900 font-semibold">{{ config('loyalty.max_redemption_percent', 20) }}%</strong> dari total transaksi booking</span>
            </li>
        </ul>

        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
            <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Simulasi Penukaran Poin
            </p>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Tukar 3.000 poin</span>
                    <span class="font-bold text-amber-600">Diskon Rp3.000</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Tukar 10.000 poin</span>
                    <span class="font-bold text-amber-600">Diskon Rp10.000</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Tukar 50.000 poin</span>
                    <span class="font-bold text-amber-600">Diskon Rp50.000</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-200">* Potongan otomatis dibatasi {{ config('loyalty.max_redemption_percent', 20) }}% dari subtotal jika melebihi batas.</p>
        </div>
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
