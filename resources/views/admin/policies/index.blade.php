@extends('layouts.admin')

@section('title', 'Kebijakan - Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kebijakan</h1>
    <a href="{{ route('admin.policies.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
        + Buat Kebijakan
    </a>
</div>

@if($policies->isEmpty())
    <x-empty-state message="Belum ada kebijakan." />
@else
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Key</th>
                <th class="px-4 py-3">Version</th>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($policies as $policy)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-gray-700">{{ $policy->policy_key }}</td>
                <td class="px-4 py-3">{{ $policy->version }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $policy->title }}</td>
                <td class="px-4 py-3">
                    @if($policy->is_current)
                        <x-badge type="success">Current</x-badge>
                    @else
                        <x-badge type="secondary">Draft</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $policy->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.policies.show', $policy) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Lihat</a>
                        @if(!$policy->is_current)
                        <form action="{{ route('admin.policies.publish', $policy) }}" method="POST" class="inline" onsubmit="return confirm('Publikasikan versi ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Publikasikan</button>
                        </form>
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
