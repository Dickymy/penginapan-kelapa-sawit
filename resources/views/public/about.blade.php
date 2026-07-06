@extends('layouts.public')

@section('title', 'Tentang - ' . $propertyName)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tentang {{ $propertyName }}</h1>

    @if($content)
        <div class="prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($content)) !!}
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">Informasi belum tersedia.</p>
        </div>
    @endif
</div>
@endsection
