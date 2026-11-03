@extends('layouts.admin')

@section('title', 'Buat Kebijakan Baru - Admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.policies.index') }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-gray-900 shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Kebijakan Baru</h1>
            <p class="text-xs text-gray-500">Buat draf kebijakan baru untuk tamu penginapan</p>
        </div>
    </div>
</div>

<form action="{{ route('admin.policies.store') }}" method="POST" 
      class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 space-y-6"
      x-data="{
          content: {{ json_encode(old('content', 'Syarat dan Ketentuan Menginap\n1. Tamu wajib menunjukkan identitas asli (KTP/SIM/Paspor) saat check-in.\n2. Dilarang membawa senjata tajam, minuman keras, atau obat-obatan terlarang.\n3. Dilarang merokok di dalam kamar. Area merokok tersedia di luar kamar.\n4. Segala bentuk kerusakan atau kehilangan barang milik penginapan yang disebabkan oleh kelalaian tamu akan dikenakan biaya penggantian.\n\nKebijakan Pembatalan\n1. Pembatalan yang dilakukan H-1 sebelum tanggal check-in (maksimal pukul 14:00 WITA) akan dikenakan biaya 50% dari total tagihan.\n2. Pembatalan pada hari-H atau tamu tidak datang (no-show) akan dikenakan biaya 100%.\n\nKebijakan Privasi\nKami sangat menghargai privasi Anda. Data pribadi yang Anda berikan saat melakukan pemesanan (nama, nomor telepon, email) hanya akan digunakan untuk keperluan reservasi, komunikasi operasional, dan program loyalitas kami. Kami tidak akan menjual atau membagikan data Anda kepada pihak ketiga tanpa persetujuan Anda.')) }},
          activeTab: 'edit',
          insertTemplate() {
              this.content = 'Syarat dan Ketentuan Menginap\n1. Tamu wajib menunjukkan identitas asli (KTP/SIM/Paspor) saat check-in.\n2. Dilarang membawa senjata tajam, minuman keras, atau obat-obatan terlarang.\n3. Dilarang merokok di dalam kamar. Area merokok tersedia di luar kamar.\n4. Segala bentuk kerusakan atau kehilangan barang milik penginapan yang disebabkan oleh kelalaian tamu akan dikenakan biaya penggantian.\n\nKebijakan Pembatalan\n1. Pembatalan yang dilakukan H-1 sebelum tanggal check-in (maksimal pukul 14:00 WITA) akan dikenakan biaya 50% dari total tagihan.\n2. Pembatalan pada hari-H atau tamu tidak datang (no-show) akan dikenakan biaya 100%.\n\nKebijakan Privasi\nKami sangat menghargai privasi Anda. Data pribadi yang Anda berikan saat melakukan pemesanan (nama, nomor telepon, email) hanya akan digunakan untuk keperluan reservasi, komunikasi operasional, dan program loyalitas kami. Kami tidak akan menjual atau membagikan data Anda kepada pihak ketiga tanpa persetujuan Anda.';
          },
          insertSection() {
              const title = prompt('Masukkan Judul Bagian Baru (contoh: Ketentuan Fasilitas & Kunci):');
              if (title) {
                  this.content += '\n\n' + title.trim() + '\n1. ';
              }
          }
      }">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="policy_key" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Policy Key <span class="text-red-500">*</span></label>
            <input type="text" name="policy_key" id="policy_key" value="{{ old('policy_key', 'guest_policy') }}" required class="w-full border-gray-300 rounded-lg shadow-2xs focus:ring-primary-500 focus:border-primary-500 text-sm">
            <x-form-error field="policy_key" />
        </div>
        <div>
            <label for="version" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Versi Kebijakan <span class="text-red-500">*</span></label>
            <input type="text" name="version" id="version" value="{{ old('version', '3.1') }}" required placeholder="Contoh: 3.1" class="w-full border-gray-300 rounded-lg shadow-2xs focus:ring-primary-500 focus:border-primary-500 text-sm">
            <x-form-error field="version" />
        </div>
    </div>

    <div>
        <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', 'Syarat dan Ketentuan & Kebijakan Privasi') }}" required class="w-full border-gray-300 rounded-lg shadow-2xs focus:ring-primary-500 focus:border-primary-500 text-sm">
        <x-form-error field="title" />
    </div>

    {{-- Content Editor with Tabs & Helper Toolbar --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <label for="content" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                Isi Konten Kebijakan <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-1.5 text-xs">
                <button type="button" @click="activeTab = 'edit'" 
                        :class="activeTab === 'edit' ? 'bg-primary-50 text-primary-700 font-bold border-primary-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border-transparent'"
                        class="px-3 py-1 rounded-lg border transition cursor-pointer">
                    Editor Teks
                </button>
                <button type="button" @click="activeTab = 'preview'" 
                        :class="activeTab === 'preview' ? 'bg-primary-50 text-primary-700 font-bold border-primary-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border-transparent'"
                        class="px-3 py-1 rounded-lg border transition cursor-pointer">
                    Live Preview Rapi
                </button>
            </div>
        </div>

        {{-- Toolbar Helpers --}}
        <div class="flex flex-wrap items-center gap-2 mb-2 p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
            <span class="text-gray-400 font-semibold px-1">Bantuan Format:</span>
            <button type="button" @click="insertSection()" class="px-2.5 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-md text-gray-700 font-medium transition cursor-pointer">
                + Sisipkan Judul Bagian
            </button>
            <button type="button" @click="insertTemplate()" class="px-2.5 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-md text-gray-700 font-medium transition cursor-pointer">
                &star; Gunakan Format Template Rapi
            </button>
            <span class="text-gray-400 text-[11px] ml-auto hidden sm:inline">Tips: Cukup ketik judul bagian, diikuti poin nomor 1, 2, 3</span>
        </div>

        {{-- Editor Pane --}}
        <div x-show="activeTab === 'edit'">
            <textarea name="content" id="content" rows="14" required 
                      x-model="content"
                      class="w-full border-gray-300 rounded-lg shadow-2xs focus:ring-primary-500 focus:border-primary-500 font-mono text-xs sm:text-sm leading-relaxed">{{ old('content') }}</textarea>
            <x-form-error field="content" />
        </div>

        {{-- Live Preview Pane --}}
        <div x-show="activeTab === 'preview'" x-cloak class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 min-h-[300px]">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Simulasi Tampilan di Halaman Tamu:</p>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-2xs space-y-3">
                <template x-for="(line, idx) in content.split('\n')" :key="idx">
                    <div>
                        <template x-if="line.trim().length > 0 && !line.trim().match(/^(\d+[\.\)]|\-|\*|•)/) && line.trim().length < 75 && !line.trim().endsWith('.')">
                            <h4 class="font-bold text-gray-900 text-sm mt-3 pb-1 border-b border-gray-100 flex items-center gap-1.5 text-primary-800">
                                <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                                <span x-text="line.trim()"></span>
                            </h4>
                        </template>
                        <template x-if="line.trim().match(/^(\d+)[\.\)]\s*(.*)/)">
                            <div class="flex items-start gap-2.5 text-xs text-gray-700 pl-1 py-1">
                                <span class="w-5 h-5 rounded-full bg-primary-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5"
                                      x-text="line.trim().match(/^(\d+)[\.\)]/)[1]"></span>
                                <span x-text="line.trim().replace(/^(\d+)[\.\)]\s*/, '')"></span>
                            </div>
                        </template>
                        <template x-if="line.trim().length > 0 && !line.trim().match(/^(\d+[\.\)]|\-|\*|•)/) && (line.trim().length >= 75 || line.trim().endsWith('.'))">
                            <p class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-2.5 rounded-lg" x-text="line.trim()"></p>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
        <a href="{{ route('admin.policies.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition font-medium">Batal</a>
        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white rounded-lg text-sm font-bold shadow-xs transition cursor-pointer">
            Simpan Draf Kebijakan
        </button>
    </div>
</form>
@endsection
