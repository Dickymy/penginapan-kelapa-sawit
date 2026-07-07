@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Pengeluaran</h1>
    <a href="{{ route('admin.expenses.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
        + Tambah Pengeluaran
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex items-end gap-4">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Kategori</label>
            <select name="category" class="border-gray-300 rounded-md text-sm">
                <option value="">Semua</option>
                @foreach(\App\Models\Expense::CATEGORIES as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">Filter</button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-5 py-3 text-left">Tanggal</th>
                <th class="px-5 py-3 text-left">Kategori</th>
                <th class="px-5 py-3 text-left">Deskripsi</th>
                <th class="px-5 py-3 text-right">Jumlah</th>
                <th class="px-5 py-3 text-left">Oleh</th>
                <th class="px-5 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($expenses as $expense)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">{{ $expense->expense_date->format('d/m/Y') }}</td>
                <td class="px-5 py-3">
                    <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                    </span>
                </td>
                <td class="px-5 py-3">{{ Str::limit($expense->description, 50) }}</td>
                <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $expense->createdBy?->name ?? '-' }}</td>
                <td class="px-5 py-3 text-center space-x-2">
                    <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-primary-600 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="inline"
                          x-data="{ confirming: false }">
                        @csrf @method('DELETE')
                        <button type="button" x-show="!confirming" @click="confirming = true" class="text-red-600 hover:underline text-xs">Hapus</button>
                        <span x-show="confirming" x-cloak class="inline-flex items-center gap-1">
                            <button type="submit" class="text-red-700 font-medium text-xs hover:underline">Ya, hapus</button>
                            <button type="button" @click="confirming = false" class="text-gray-500 text-xs hover:underline">Batal</button>
                        </span>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">Belum ada data pengeluaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $expenses->links() }}
</div>
@endsection
