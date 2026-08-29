@extends('layouts.admin')

@section('title', 'Layanan Tambahan (Add-ons)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Layanan Tambahan</h1>
        <a href="{{ route('admin.addons.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Add-on
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Nama Layanan</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Urutan</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($addons as $addon)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $addon->name }}</p>
                        @if($addon->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $addon->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $addon->formatted_price }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $addon->isSingle() ? 'Hanya Ceklis' : 'Kuantitas' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $addon->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $addon->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $addon->sort_order }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.addons.edit', $addon) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <button type="button" @click="$dispatch('open-confirm', { id: 'delete-addon-{{ $addon->id }}' })" class="text-red-600 hover:text-red-800 font-medium ml-2">Hapus</button>
                        
                        <x-confirm-modal 
                            id="delete-addon-{{ $addon->id }}"
                            title="Hapus Layanan Tambahan" 
                            message="Apakah Anda yakin ingin menghapus layanan {{ $addon->name }}? Tindakan ini tidak dapat dibatalkan."
                            confirm-text="Ya, Hapus"
                            cancel-text="Batal"
                            variant="danger"
                            form-action="{{ route('admin.addons.destroy', $addon) }}"
                        />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada layanan tambahan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
