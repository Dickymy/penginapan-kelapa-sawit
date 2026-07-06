@extends('layouts.admin')

@section('title', 'Block Kamar')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Block Kamar</h1>
        <a href="{{ route('admin.room-blocks.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm font-medium">
            + Block Baru
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($blocks as $block)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $block->room->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $block->start_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $block->end_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $block->reason_type }}</td>
                    <td class="px-4 py-3 text-sm">{{ $block->reason }}</td>
                    <td class="px-4 py-3 text-sm">{{ $block->createdBy?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <form method="POST" action="{{ route('admin.room-blocks.destroy', $block) }}" onsubmit="return confirm('Hapus block ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada block kamar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $blocks->links() }}
</div>
@endsection
