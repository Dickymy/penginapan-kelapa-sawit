@extends('layouts.admin')

@section('title', 'Pengeluaran')
@section('page-title', 'Pengeluaran')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Pengeluaran</h1>
        <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Pengeluaran</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-col sm:flex-row gap-2">
        <select name="category" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Kategori</option>
            @foreach(\App\Models\Expense::CATEGORIES as $cat)
                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Filter</button>
        @if(request('category'))
            <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 text-center">Hapus</a>
        @endif
    </form>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded-full">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs truncate">{{ $expense->description }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 whitespace-nowrap">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                                <button type="button" @click="$dispatch('open-confirm', { id: 'delete-expense-{{ $expense->id }}' })" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <x-empty-state message="Belum ada data pengeluaran." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($expenses as $expense)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $expense->description }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex px-2 py-0.5 text-[10px] bg-gray-100 text-gray-600 rounded-full">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</span>
                        <span class="text-xs text-gray-400">{{ $expense->expense_date->format('d/m/Y') }}</span>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-800 ml-3">Rp{{ number_format($expense->amount, 0, ',', '.') }}</p>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-xs font-medium text-primary-600">Edit</a>
                <button type="button" @click="$dispatch('open-confirm', { id: 'delete-expense-{{ $expense->id }}' })" class="text-xs font-medium text-red-600">Hapus</button>
            </div>
        </div>
        @empty
        <x-empty-state message="Belum ada data pengeluaran." />
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $expenses->withQueryString()->links() }}</div>

    {{-- Delete modals --}}
    @foreach($expenses as $expense)
    <x-confirm-modal
        id="delete-expense-{{ $expense->id }}"
        title="Hapus Pengeluaran?"
        message="Data pengeluaran '{{ Str::limit($expense->description, 40) }}' (Rp{{ number_format($expense->amount, 0, ',', '.') }}) akan dihapus."
        confirm-text="Ya, Hapus"
        cancel-text="Batal"
        variant="danger"
        :form-action="route('admin.expenses.destroy', $expense)"
        method="DELETE"
    />
    @endforeach
</div>
@endsection
