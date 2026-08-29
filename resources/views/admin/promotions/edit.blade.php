@extends('layouts.admin')

@section('title', 'Edit Promo - ' . $promotion->code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Promo: {{ $promotion->code }}</h1>
    <a href="{{ route('admin.promotions.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Kembali</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 ">
    <form action="{{ route('admin.promotions.update', $promotion) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.promotions._form', ['promotion' => $promotion])
        <div class="pt-4">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700 transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection
