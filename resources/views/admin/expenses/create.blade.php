@extends('layouts.admin')

@section('title', 'Tambah Pengeluaran')

@section('content')
<div class="">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Pengeluaran</h1>

    <form method="POST" action="{{ route('admin.expenses.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf

        <div>
            <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', now()->toDateString()) }}"
                   class="w-full border-gray-300 rounded-md text-sm @error('expense_date') border-red-500 @enderror">
            @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="category" id="category" class="w-full border-gray-300 rounded-md text-sm @error('category') border-red-500 @enderror">
                @foreach(\App\Models\Expense::CATEGORIES as $cat)
                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                @endforeach
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="1"
                   class="w-full border-gray-300 rounded-md text-sm @error('amount') border-red-500 @enderror">
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full border-gray-300 rounded-md text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm hover:bg-primary-700">Simpan</button>
            <a href="{{ route('admin.expenses.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>
@endsection
