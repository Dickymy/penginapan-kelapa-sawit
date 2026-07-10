@extends('layouts.admin')

@section('title', 'Promosi')
@section('page-title', 'Promosi')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Promosi</h1>
        <a href="{{ route('admin.promotions.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Promo</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Berlaku</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($promotions as $promo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs font-medium text-gray-800">{{ $promo->code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $promo->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $promo->type->label() }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">
                            @if($promo->type->value === 'percentage')
                                {{ $promo->value }}%
                            @else
                                Rp{{ number_format($promo->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-center text-gray-500 whitespace-nowrap">
                            {{ $promo->starts_at->format('d/m/Y') }} - {{ $promo->ends_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
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
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.promotions.edit', $promo) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
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
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-800">{{ $promo->name }}</p>
                    <p class="text-xs font-mono text-gray-500 mt-0.5">{{ $promo->code }}</p>
                </div>
                @if($promo->is_active && now()->between($promo->starts_at, $promo->ends_at))
                    <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                @elseif(now()->gt($promo->ends_at))
                    <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-red-100 text-red-700">Berakhir</span>
                @else
                    <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>
            <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                <span>{{ $promo->type->label() }}:
                    @if($promo->type->value === 'percentage')
                        <strong class="text-gray-700">{{ $promo->value }}%</strong>
                    @else
                        <strong class="text-gray-700">Rp{{ number_format($promo->value, 0, ',', '.') }}</strong>
                    @endif
                </span>
                <span>{{ $promo->starts_at->format('d/m') }} - {{ $promo->ends_at->format('d/m/Y') }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end">
                <a href="{{ route('admin.promotions.edit', $promo) }}" class="text-xs font-medium text-primary-600 hover:text-primary-800">Edit</a>
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
