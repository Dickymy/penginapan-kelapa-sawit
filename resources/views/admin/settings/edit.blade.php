@extends('layouts.admin')

@section('title', 'Pengaturan ' . ucfirst($group) . ' - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Pengaturan: {{ ucfirst($group) }}</h1>
</div>

<form action="{{ route('admin.settings.update', $group) }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl space-y-5">
    @csrf
    @method('PUT')

    @foreach($fields as $key => $config)
    <div>
        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $config['label'] }}</label>
        @if($config['type'] === 'url' || $config['type'] === 'string')
            <input type="{{ $config['type'] === 'url' ? 'url' : 'text' }}" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        @elseif($config['type'] === 'time')
            <input type="time" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        @else
            <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        @endif
        <x-form-error :field="$key" />
    </div>
    @endforeach

    <div class="pt-4">
        <x-button type="submit">Simpan Pengaturan</x-button>
    </div>
</form>
@endsection
