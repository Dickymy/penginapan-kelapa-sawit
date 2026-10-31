@extends('layouts.admin')

@section('title', 'Promosi')
@section('page-title', 'Promosi')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Promosi &amp; Kode Voucher</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola kupon diskon, voucher, dan visibilitasnya pada halaman checkout pemesanan.</p>
        </div>
        <a href="{{ route('admin.promotions.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Promo</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    {{-- Global Checkout Listing Toggle Banner --}}
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl {{ $showPromoListing ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center shrink-0">
                    @if($showPromoListing)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-bold text-gray-900">Daftar "Promo Menarik Tersedia" di Checkout</h2>
                        @if($showPromoListing)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Aktif Ditampilkan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                Disembunyikan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1 max-w-2xl leading-relaxed">
                        @if($showPromoListing)
                            Daftar pilihan promo publik ditampilkan kepada pengunjung di halaman checkout (menampilkan promo yang statusnya aktif dan opsi tampil dicentang).
                        @else
                            Daftar promo publik di checkout <strong>disembunyikan sepenuhnya</strong>. Tamu yang memiliki dan mengetik kode promo yang valid secara manual tetap bisa menggunakan promo tersebut!
                        @endif
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.promotions.toggle-checkout-listing') }}" method="POST" class="shrink-0">
                @csrf
                <input type="hidden" name="show_promo_listing" value="{{ $showPromoListing ? '0' : '1' }}">
                @if($showPromoListing)
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        <span>Sembunyikan Daftar di Checkout</span>
                    </button>
                @else
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Tampilkan Daftar di Checkout</span>
                    </button>
                @endif
            </form>
        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Berlaku</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tampil di Checkout</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($promotions as $promo)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-4 py-3.5 font-mono text-xs font-bold text-gray-900">
                            <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded border border-gray-200/80">{{ $promo->code }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-800 font-medium">{{ $promo->name }}</td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">{{ $promo->type->label() }}</td>
                        <td class="px-4 py-3.5 text-right font-semibold text-gray-900">
                            @if($promo->type->value === 'percentage')
                                {{ $promo->value }}%
                            @else
                                Rp{{ number_format($promo->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-center text-gray-500 whitespace-nowrap">
                            {{ $promo->starts_at->format('d/m/Y') }} &ndash; {{ $promo->ends_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @if($promo->show_in_listing)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Tampil
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200" title="Disembunyikan dari daftar publik, hanya bisa digunakan jika kode diketik langsung">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    Manual Saja
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @if($promo->is_active && now()->between($promo->starts_at, $promo->ends_at))
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                            @elseif($promo->is_active && now()->lt($promo->starts_at))
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Dijadwalkan</span>
                            @elseif(now()->gt($promo->ends_at))
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Berakhir</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('admin.promotions.edit', $promo) }}" class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800 text-xs font-semibold px-2.5 py-1 rounded-md hover:bg-primary-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <x-empty-state message="Belum ada promo." :action="route('admin.promotions.create')" action-text="Buat Promo" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($promotions as $promo)
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900">{{ $promo->name }}</p>
                    <p class="text-xs font-mono font-bold text-gray-600 mt-0.5 bg-gray-100 inline-block px-1.5 py-0.5 rounded">{{ $promo->code }}</p>
                </div>
                <div class="flex flex-col items-end gap-1 shrink-0">
                    @if($promo->is_active && now()->between($promo->starts_at, $promo->ends_at))
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                    @elseif(now()->gt($promo->ends_at))
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full bg-red-100 text-red-700">Berakhir</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                    @endif

                    @if($promo->show_in_listing)
                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-blue-50 text-blue-700 border border-blue-100">Tampil di Checkout</span>
                    @else
                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 border border-amber-200">Manual Saja</span>
                    @endif
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100">
                <span>{{ $promo->type->label() }}:
                    @if($promo->type->value === 'percentage')
                        <strong class="text-gray-800 font-bold">{{ $promo->value }}%</strong>
                    @else
                        <strong class="text-gray-800 font-bold">Rp{{ number_format($promo->value, 0, ',', '.') }}</strong>
                    @endif
                </span>
                <span>{{ $promo->starts_at->format('d/m') }} &ndash; {{ $promo->ends_at->format('d/m/Y') }}</span>
            </div>

            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-end">
                <a href="{{ route('admin.promotions.edit', $promo) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Promo
                </a>
            </div>
        </div>
        @empty
        <x-empty-state message="Belum ada promo." :action="route('admin.promotions.create')" action-text="Buat Promo" />
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $promotions->links() }}</div>
</div>
@endsection
