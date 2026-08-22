@extends('layouts.admin')

@section('title', 'Detail Pesan Kontak')

@section('content')
<div class="mb-6 md:flex md:items-center md:justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.contact-messages.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pesan</h1>
            <p class="mt-1 text-sm text-gray-500">Pesan dari {{ $contactMessage->name }}</p>
        </div>
    </div>
    
    <div class="mt-4 md:mt-0 flex space-x-3">
        <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Hapus
            </button>
        </form>
        <a href="mailto:{{ $contactMessage->email }}?subject=RE: {{ rawurlencode($contactMessage->subject) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            Balas via Email
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ $contactMessage->subject }}
        </h3>
        <span class="text-sm text-gray-500">{{ $contactMessage->created_at->format('d M Y, H:i') }} ({{ $contactMessage->created_at->diffForHumans() }})</span>
    </div>
    <div class="px-6 py-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Isi Pesan</h4>
                    <div class="prose prose-sm max-w-none text-gray-800 bg-gray-50 rounded-lg p-5 border border-gray-100">
                        {!! nl2br(e($contactMessage->message)) !!}
                    </div>
                </div>
            </div>
            
            <div class="space-y-6 bg-gray-50/50 p-5 rounded-xl border border-gray-100 h-fit">
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</h4>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $contactMessage->name }}</p>
                </div>
                
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</h4>
                    <p class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $contactMessage->email }}" class="text-primary-600 hover:underline">{{ $contactMessage->email }}</a>
                    </p>
                </div>
                
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon / WhatsApp</h4>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($contactMessage->phone)
                            @php
                                $waPhone = preg_replace('/[^0-9]/', '', $contactMessage->phone);
                                if(str_starts_with($waPhone, '0')) {
                                    $waPhone = '62' . substr($waPhone, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-green-600 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ $contactMessage->phone }}
                            </a>
                        @else
                            <span class="text-gray-400 italic">Tidak tersedia</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
