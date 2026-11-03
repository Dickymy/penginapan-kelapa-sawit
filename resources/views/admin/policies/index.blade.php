@extends('layouts.admin')

@section('title', 'Kebijakan - Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kebijakan Tamu</h1>
        <p class="text-xs text-gray-500">Kelola syarat &amp; ketentuan menginap, pembatalan, dan privasi</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('policy') }}" target="_blank" class="inline-flex items-center gap-1 px-3.5 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-2xs transition">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span>Lihat Halaman Tamu</span>
        </a>
        <a href="{{ route('admin.policies.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-bold hover:bg-primary-700 shadow-xs transition">
            <span>+ Buat Kebijakan</span>
        </a>
    </div>
</div>

@if($policies->isEmpty())
    <x-empty-state message="Belum ada kebijakan." />
@else
<div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
            <tr>
                <th class="px-5 py-3.5">Key</th>
                <th class="px-5 py-3.5">Versi</th>
                <th class="px-5 py-3.5">Judul Kebijakan</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5">Tanggal</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($policies as $policy)
            <tr class="hover:bg-gray-50/80 transition">
                <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ $policy->policy_key }}</td>
                <td class="px-5 py-4 font-bold text-gray-900">{{ $policy->version }}</td>
                <td class="px-5 py-4 font-semibold text-gray-800">{{ $policy->title }}</td>
                <td class="px-5 py-4">
                    @if($policy->is_current)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                            &check; Aktif di Publik
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            Draf
                        </span>
                    @endif
                </td>
                <td class="px-5 py-4 text-xs text-gray-500">{{ $policy->created_at->format('d/m/Y') }}</td>
                <td class="px-5 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('admin.policies.show', $policy) }}" class="px-2.5 py-1 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-md transition">Detail</a>
                        <a href="{{ route('admin.policies.edit', $policy) }}" class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">Edit</a>
                        @if(!$policy->is_current)
                        <button type="button"
                                x-data
                                @click="$dispatch('open-confirm', { id: 'publish-policy-{{ $policy->id }}' })"
                                class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md transition cursor-pointer">Publikasikan</button>
                        <x-confirm-modal
                            id="publish-policy-{{ $policy->id }}"
                            title="Publikasikan kebijakan ini?"
                            message="Versi ini akan langsung menjadi kebijakan aktif yang ditampilkan kepada seluruh tamu di website."
                            confirm-text="Ya, Publikasikan Sekarang"
                            cancel-text="Batal"
                            variant="primary"
                            :form-action="route('admin.policies.publish', $policy)"
                            method="PATCH"
                        />
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
