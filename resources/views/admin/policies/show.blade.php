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
                <x-badge type="success">Current</x-badge>
            @else
                <x-badge type="secondary">Draft</x-badge>
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
        <form action="{{ route('admin.policies.publish', $policy) }}" method="POST" onsubmit="return confirm('Publikasikan versi ini?')">
            @csrf
            @method('PATCH')
            <x-button type="submit">Publikasikan</x-button>
        </form>
    </div>
    @endif
</div>
@endsection
