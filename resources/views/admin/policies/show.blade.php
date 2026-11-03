@extends('layouts.admin')

@section('title', $policy->title . ' - Detail Kebijakan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.policies.index') }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-gray-900 shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $policy->title }}</h1>
            <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                <span>Key: <span class="font-mono font-medium">{{ $policy->policy_key }}</span></span>
                <span>&bull;</span>
                <span>Versi: <strong class="text-gray-700">{{ $policy->version }}</strong></span>
                <span>&bull;</span>
                @if($policy->is_current)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Aktif di Publik
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        Draf
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.policies.edit', $policy) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium shadow-xs transition">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Edit Kebijakan</span>
        </a>
        <a href="{{ route('policy') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 border border-primary-200 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span>Lihat di Halaman Tamu</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Structured Preview (Matches Public Page) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tampilan Rapi (Otomatis Diformat)
            </h2>

            @if(count($sections) > 0)
                <div class="space-y-4">
                    @foreach($sections as $sec)
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                        <div class="p-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                                {{ $sec['title'] }}
                            </h3>
                            <span class="text-[10px] font-semibold text-gray-500 bg-white border border-gray-200 px-2 py-0.5 rounded-full">
                                {{ $sec['badge'] }}
                            </span>
                        </div>
                        <div class="p-4 space-y-2 text-xs sm:text-sm">
                            @foreach($sec['items'] as $item)
                                @if($item['type'] === 'numbered')
                                    <div class="flex items-start gap-2.5 p-2 rounded-lg {{ $item['is_warning'] ? 'bg-amber-50 text-amber-950 font-medium' : 'text-gray-700' }}">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 {{ $item['is_warning'] ? 'bg-amber-600 text-white' : 'bg-primary-600 text-white' }}">
                                            {{ $item['number'] }}
                                        </span>
                                        <span class="leading-relaxed">{{ $item['text'] }}</span>
                                    </div>
                                @elseif($item['type'] === 'bullet')
                                    <div class="flex items-start gap-2 p-1.5 text-gray-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500 shrink-0 mt-2"></span>
                                        <span class="leading-relaxed">{{ $item['text'] }}</span>
                                    </div>
                                @else
                                    <div class="p-2.5 bg-gray-50 rounded-lg text-gray-700 leading-relaxed">
                                        {{ $item['text'] }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Konten kebijakan kosong.</p>
            @endif
        </div>
    </div>

    {{-- Right: Meta & Raw Content --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2">Informasi Versi</h2>
            
            <dl class="space-y-3 text-xs">
                <div>
                    <dt class="text-gray-400 font-medium">Status Publikasi</dt>
                    <dd class="mt-1">
                        @if($policy->is_current)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-800">
                                &check; Sedang Tayang di Publik
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                Draf (Belum Tayang)
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium">Dibuat Pada</dt>
                    <dd class="mt-0.5 text-gray-800 font-medium">{{ $policy->created_at->translatedFormat('d F Y H:i') }}</dd>
                </div>
                @if($policy->published_at)
                <div>
                    <dt class="text-gray-400 font-medium">Terakhir Dipublikasikan</dt>
                    <dd class="mt-0.5 text-gray-800 font-medium">{{ $policy->published_at->translatedFormat('d F Y H:i') }}</dd>
                </div>
                @endif
            </dl>

            @if(!$policy->is_current)
            <div class="pt-3 border-t border-gray-100">
                <button type="button"
                        x-data
                        @click="$dispatch('open-confirm', { id: 'publish-policy' })"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-bold transition bg-primary-600 hover:bg-primary-700 text-white shadow-xs cursor-pointer">
                    Publikasikan Versi Ini
                </button>
                <x-confirm-modal
                    id="publish-policy"
                    title="Publikasikan kebijakan ini?"
                    message="Versi ini akan langsung menjadi kebijakan aktif yang ditampilkan kepada seluruh tamu di website."
                    confirm-text="Ya, Publikasikan Sekarang"
                    cancel-text="Batal"
                    variant="primary"
                    :form-action="route('admin.policies.publish', $policy)"
                    method="PATCH"
                />
            </div>
            @endif
        </div>

        {{-- Raw Textarea View --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Teks Asli Input</h3>
                <a href="{{ route('admin.policies.edit', $policy) }}" class="text-xs text-primary-600 hover:underline font-semibold">Edit Teks &rarr;</a>
            </div>
            <pre class="bg-gray-50 rounded-lg p-3 text-xs text-gray-700 font-mono whitespace-pre-wrap max-h-60 overflow-y-auto custom-scrollbar border border-gray-100">{{ $policy->content }}</pre>
        </div>
    </div>
</div>
@endsection
