@extends('layouts.admin')

@section('title', 'Tambah Promo')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Promo</h1>
    <a href="{{ route('admin.promotions.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Kembali</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.promotions.store') }}" method="POST" class="space-y-4">
        @csrf
        @include('admin.promotions._form')
        <div class="pt-4">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700 transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
