@extends('layouts.admin')

@section('title', 'Pesan Kontak')

@section('content')
<div class="mb-6 md:flex md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pesan Kontak</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola pesan dan pertanyaan dari tamu.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filters -->
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <div class="flex space-x-2">
            <a href="{{ route('admin.contact-messages.index', ['status' => 'all']) }}" 
               class="px-4 py-2 text-sm font-medium rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' }}">
                Semua Pesan
            </a>
            <a href="{{ route('admin.contact-messages.index', ['status' => 'unread']) }}" 
               class="px-4 py-2 text-sm font-medium rounded-lg {{ request('status') == 'unread' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' }}">
                Belum Dibaca
                @php $unreadCount = \App\Models\ContactMessage::unread()->count(); @endphp
                @if($unreadCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium {{ request('status') == 'unread' ? 'bg-white text-primary-600' : 'bg-primary-100 text-primary-700' }}">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjek & Pesan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                    <tr class="{{ !$message->is_read ? 'bg-primary-50/30' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(!$message->is_read)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Belum Dibaca
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Telah Dibaca
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $message->name }}</div>
                            <div class="text-sm text-gray-500">{{ $message->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ $message->subject }}</div>
                            <div class="text-sm text-gray-500 line-clamp-1">{{ $message->message }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $message->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.contact-messages.show', $message) }}" class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 p-1.5 rounded-md transition" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                
                                @if(!$message->is_read)
                                <form action="{{ route('admin.contact-messages.mark-read', $message) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-1.5 rounded-md transition" title="Tandai Telah Dibaca">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </form>
                                @endif

                                <button type="button" @click="$dispatch('open-confirm', { id: 'delete-msg-{{ $message->id }}' })" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                <x-confirm-modal 
                                    id="delete-msg-{{ $message->id }}"
                                    title="Hapus Pesan" 
                                    message="Apakah Anda yakin ingin menghapus pesan ini?"
                                    confirm-text="Ya, Hapus"
                                    cancel-text="Batal"
                                    variant="danger"
                                    form-action="{{ route('admin.contact-messages.destroy', $message) }}"
                                    method="DELETE"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pesan</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request('status') === 'unread')
                                    Tidak ada pesan yang belum dibaca saat ini.
                                @else
                                    Belum ada pengunjung yang mengirim pesan kontak.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($messages->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
