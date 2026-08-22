@extends('layouts.admin')
@section('title', 'Harga Dinamis - Admin')
@section('page-title', 'Harga Dinamis')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.rate-overrides.index') }}" class="mb-4">
                        <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tipe Kamar</label>
                        <select name="room_type_id" id="room_type_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ $selectedRoomTypeId == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} (Base: Rp{{ number_format($type->base_price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Kolom Form Add Override -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold mb-4">Set Harga Khusus</h3>
                            <form action="{{ route('admin.rate-overrides.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_type_id" value="{{ $selectedRoomTypeId }}">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <x-form-error field="start_date" />
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai (Termasuk)</label>
                                    <input type="date" name="end_date" required min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <x-form-error field="end_date" />
                                    <p class="text-xs text-gray-500 mt-1">Gunakan tanggal yang sama dengan Tanggal Mulai untuk set 1 hari saja.</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Baru (Rp)</label>
                                    <input type="number" name="price" required min="0" value="{{ old('price') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <x-form-error field="price" />
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Label (Opsional)</label>
                                    <input type="text" name="label" placeholder="Misal: Weekend, Lebaran" value="{{ old('label') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <x-form-error field="label" />
                                </div>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan Harga
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Kolom Tabel Overrides -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold mb-4">Daftar Harga Khusus</h3>
                            @if($overrides->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Baru</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($overrides as $override)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $override->date->translatedFormat('l, d F Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        Rp{{ number_format($override->price, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $override->label ?? '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <form action="{{ route('admin.rate-overrides.destroy', $override) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus harga khusus ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $overrides->links() }}
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Belum ada harga khusus untuk tipe kamar ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
