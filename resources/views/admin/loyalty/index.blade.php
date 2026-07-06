@extends('layouts.admin')

@section('title', 'Loyalty Points')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Loyalty Points</h1>
</div>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Poin</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">{{ number_format($user->loyalty_balance_cache) }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.loyalty.show', $user) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada member dengan poin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
