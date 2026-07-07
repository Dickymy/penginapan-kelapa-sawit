@extends('layouts.admin')

@section('title', $policy->title . ' - Kebijakan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.policies.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $policy->title }}</h1>
        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
            <span>Key: <span class="font-mono">{{ $policy->policy_key }}</span></span>
            <span>Versi: {{ $policy->version }}</span>
            @if($policy->is_current)
                <x-badge type="success">Aktif</x-badge>
            @else
                <x-badge type="secondary">Draf</x-badge>
            @endif
        </div>
        @if($policy->published_at)
            <p class="text-xs text-gray-400 mt-1">Dipublikasikan: {{ $policy->published_at->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    <div class="prose prose-sm max-w-none text-gray-700">
        {!! nl2br(e($policy->content)) !!}
    </div>

    @if(!$policy->is_current)
    <div class="mt-6 pt-4 border-t">
        <button type="button"
                x-data
                @click="$dispatch('open-confirm', { id: 'publish-policy' })"
                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500">
            Publikasikan
        </button>
        <x-confirm-modal
            id="publish-policy"
            title="Publikasikan kebijakan ini?"
            message="Versi ini akan menjadi kebijakan aktif yang ditampilkan kepada publik."
            confirm-text="Ya, Publikasikan"
            cancel-text="Batal"
            variant="primary"
            :form-action="route('admin.policies.publish', $policy)"
            method="PATCH"
        />
    </div>
    @endif
</div>
@endsection
