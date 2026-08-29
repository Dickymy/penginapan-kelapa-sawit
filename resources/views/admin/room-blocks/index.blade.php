@extends('layouts.admin')

@section('title', 'Block Kamar')
@section('page-title', 'Blokir Kamar')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Blokir Kamar</h1>
        <a href="{{ route('admin.room-blocks.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Blokir Baru
        </a>
    </div>

    @if($blocks->isEmpty())
        <x-empty-state message="Belum ada kamar yang diblokir." description="Blokir kamar untuk maintenance, renovasi, atau penggunaan internal." :action="route('admin.room-blocks.create')" action-text="Blokir Kamar" />
    @else
    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($blocks as $block)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $block->room->name }}</td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $block->start_date->format('d/m/Y') }} &rarr; {!! $block->end_date ? $block->end_date->format('d/m/Y') : '<span class="text-orange-500 font-medium text-xs">Tanpa batas waktu</span>' !!}</td>
                    <td class="px-4 py-3 text-gray-600">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">{{ ucfirst($block->reason_type) }}</span>
                        <span class="text-gray-500 ml-1">{{ $block->reason }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $block->createdBy?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" @click="$dispatch('open-confirm', { id: 'delete-block-{{ $block->id }}' })" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @foreach($blocks as $block)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $block->room->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $block->start_date->format('d/m/Y') }} &rarr; {!! $block->end_date ? $block->end_date->format('d/m/Y') : '<span class="text-orange-500 font-medium text-xs">Tanpa batas waktu</span>' !!}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">{{ ucfirst($block->reason_type) }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $block->reason }}</p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $block->createdBy?->name ?? '-' }}</span>
                <button type="button" @click="$dispatch('open-confirm', { id: 'delete-block-{{ $block->id }}' })" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus Block</button>
            </div>
        </div>
        @endforeach
    </div>

    {{ $blocks->links() }}

    {{-- Delete modals --}}
    @foreach($blocks as $block)
    <x-confirm-modal
        id="delete-block-{{ $block->id }}"
        title="Hapus Block Kamar?"
        message="Block kamar {{ $block->room->name }} ({{ $block->start_date->format('d/m/Y') }} - {{ $block->end_date ? $block->end_date->format('d/m/Y') : 'Tanpa batas waktu' }}) akan dihapus. Kamar akan kembali tersedia."
        confirm-text="Ya, Hapus"
        cancel-text="Batal"
        variant="danger"
        :form-action="route('admin.room-blocks.destroy', $block)"
        method="DELETE"
    />
    @endforeach
    @endif
</div>
@endsection
