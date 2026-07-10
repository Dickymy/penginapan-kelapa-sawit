@extends('layouts.admin')

@section('title', 'Poin Loyalitas')
@section('page-title', 'Poin Loyalitas')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Poin Loyalitas</h1>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Poin</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">{{ number_format($user->loyalty_balance_cache) }} <span class="font-normal text-xs text-gray-400">poin</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.loyalty.show', $user) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <x-empty-state message="Belum ada member dengan poin." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($users as $user)
        <a href="{{ route('admin.loyalty.show', $user) }}" class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                </div>
                <div class="text-right ml-3">
                    <p class="text-lg font-bold text-primary-600">{{ number_format($user->loyalty_balance_cache) }}</p>
                    <p class="text-[10px] text-gray-400">poin</p>
                </div>
            </div>
        </a>
        @empty
        <x-empty-state message="Belum ada member dengan poin." />
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
