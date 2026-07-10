@extends('layouts.admin')

@section('title', 'Tambah Promo')
@section('page-title', 'Tambah Promo')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.promotions.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <h1 class="text-xl font-bold text-gray-800 mb-6">Tambah Promo</h1>

    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <form action="{{ route('admin.promotions.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.promotions._form')
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
                <a href="{{ route('admin.promotions.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition text-center">Batal</a>
                <button type="submit" x-data="{ loading: false }" @click="if ($el.form.checkValidity()) { loading = true; }" :disabled="loading"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-60">
                    <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    <span x-show="!loading">Simpan Promo</span>
                    <span x-show="loading" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
