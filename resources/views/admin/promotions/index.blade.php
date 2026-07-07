@extends('layouts.admin')

@section('title', 'Promosi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Promosi</h1>
    <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700 transition">+ Tambah Promo</a>
</div>

@if(session('success'))
    <x-alert type="success" :message="session('success')" />
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Berlaku</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($promotions as $promo)
                <tr>
                    <td class="px-6 py-4 text-sm font-mono font-medium text-gray-900">{{ $promo->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $promo->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $promo->type->label() }}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-900">
                        @if($promo->type->value === 'percentage')
                            {{ $promo->value }}%
                        @else
                            Rp{{ number_format($promo->value, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-center text-gray-500">
                        {{ $promo->starts_at->format('d/m/Y') }} - {{ $promo->ends_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($promo->is_active)
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <a href="{{ route('admin.promotions.edit', $promo) }}" class="text-primary-600 hover:text-primary-800 text-sm">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada promo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $promotions->links() }}
</div>
@endsection
