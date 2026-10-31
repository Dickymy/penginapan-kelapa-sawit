@extends('layouts.member')

@section('title', 'Profil & Akun')

@section('content')
<div class="space-y-10 pb-8">

    {{-- Profile Hero --}}
    <div class="bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 rounded-3xl shadow-xl p-8 sm:p-10 text-white relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 -mr-12 -mt-12 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-20 -mb-12 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8 text-center sm:text-left">
            <div class="relative">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white/20 shadow-2xl">
                @else
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-white/10 border-4 border-white/20 shadow-2xl flex items-center justify-center backdrop-blur-md">
                        <span class="text-5xl sm:text-6xl font-bold text-white drop-shadow-lg">{{ mb_substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-2 border-white rounded-full" title="Akun Aktif"></div>
            </div>
            
            <div class="flex-1">
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-2 drop-shadow-sm">{{ $user->name }}</h1>
                <p class="text-primary-100 flex items-center justify-center sm:justify-start gap-2 mb-4 font-medium">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $user->email }}
                </p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                    <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium text-primary-50 backdrop-blur-md">
                        <svg class="w-4 h-4 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Member sejak {{ $user->created_at->translatedFormat('F Y') }}
                    </div>
                    @if($user->whatsapp)
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium text-primary-50 backdrop-blur-md">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                            </svg>
                            {{ $user->whatsapp }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Quick Menu --}}
    <div class="lg:hidden grid grid-cols-2 gap-4">
        <a href="{{ route('member.claim.index') }}" class="flex flex-col items-center justify-center gap-2.5 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 active:scale-95 transition-transform">
            <div class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="text-sm font-semibold text-gray-700">Klaim Booking</span>
        </a>
        <button type="button" @click="$dispatch('open-confirm', { id: 'member-logout' })" class="flex flex-col items-center justify-center gap-2.5 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 active:scale-95 transition-transform">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <span class="text-sm font-semibold text-red-600">Keluar Akun</span>
        </button>
    </div>

    {{-- Update Profile Accordion --}}
    <div x-data="{ open: false }" class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-6 sm:p-8 hover:bg-gray-50 transition-colors focus:outline-none">
            <div class="flex items-center gap-5 text-left">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Informasi Pribadi</h3>
                    <p class="text-sm text-gray-500 mt-1">Perbarui nama lengkap dan nomor WhatsApp Anda.</p>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 text-gray-400 transition-transform duration-300" :class="{ 'transform rotate-180': open }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </button>
        
        <div x-show="open" x-collapse x-cloak>
            <div class="px-6 sm:px-8 pb-6 sm:pb-8 pt-2">
                <form method="POST" action="{{ route('member.profile.update') }}" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       autocomplete="name"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-colors @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <x-form-error field="name" />
                            </div>

                            <div class="md:col-span-2 lg:col-span-1">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" value="{{ $user->email }}" disabled
                                           class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                                </div>
                                <p class="mt-1.5 text-xs text-gray-500">Email digunakan untuk login dan tidak dapat diubah.</p>
                            </div>

                            <div class="md:col-span-2 lg:col-span-1">
                                <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                        </svg>
                                    </div>
                                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" required
                                           inputmode="tel"
                                           autocomplete="tel"
                                           placeholder="08xxxxxxxxxx"
                                           class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-colors @error('whatsapp') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                <p class="mt-1.5 text-xs text-gray-500">Format: 08xx, 628xx, atau +628xx</p>
                                <x-form-error field="whatsapp" />
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 mt-2">
                            <button type="submit"
                                    :disabled="submitting"
                                    class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!submitting">Simpan Profil</span>
                                <span x-show="submitting" x-cloak>Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Update Password Accordion --}}
    <div x-data="{ open: {{ $errors->updatePassword->any() ? 'true' : 'false' }} }" class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden mt-6">
        <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-6 sm:p-8 hover:bg-gray-50 transition-colors focus:outline-none">
            <div class="flex items-center gap-5 text-left">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Keamanan Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">Ubah kata sandi untuk menjaga keamanan akun Anda.</p>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 text-gray-400 transition-transform duration-300" :class="{ 'transform rotate-180': open }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </button>
        
        <div x-show="open" x-collapse x-cloak>
            <div class="px-6 sm:px-8 pb-6 sm:pb-8 pt-2">
                <form x-data="{
                        submitting: false,
                        password: '',
                        confirmation: '',
                        currentPassword: '',
                        errors: [],
                        success: false,
                        async submitPassword() {
                            this.submitting = true;
                            this.errors = [];
                            this.success = false;

                            try {
                                const response = await fetch('{{ route('user-password.update') }}', {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        current_password: this.currentPassword,
                                        password: this.password,
                                        password_confirmation: this.confirmation
                                    })
                                });

                                if (response.ok) {
                                    this.success = true;
                                    this.currentPassword = '';
                                    this.password = '';
                                    this.confirmation = '';
                                    setTimeout(() => { this.success = false; }, 5000);
                                } else if (response.status === 422) {
                                    const data = await response.json();
                                    if (data.errors) {
                                        this.errors = Object.values(data.errors).flat();
                                    } else if (data.message) {
                                        this.errors = [data.message];
                                    }
                                } else {
                                    this.errors = ['Terjadi kesalahan. Silakan coba lagi.'];
                                }
                            } catch (e) {
                                this.errors = ['Gagal menghubungi server. Periksa koneksi Anda.'];
                            }

                            this.submitting = false;
                        }
                    }"
                    @submit.prevent="submitPassword()">

                    <div x-show="success" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <p class="text-sm text-green-700 font-medium">Kata sandi berhasil diperbarui!</p>
                        </div>
                    </div>

                    <div x-show="errors.length > 0" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <ul class="text-sm text-red-700 space-y-1">
                                <template x-for="error in errors" :key="error">
                                    <li x-text="error"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    @if($errors->updatePassword->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                <ul class="text-sm text-red-700 space-y-1">
                                    @foreach($errors->updatePassword->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="current_password" id="current_password" required
                                       x-model="currentPassword"
                                       autocomplete="off"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-colors pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi Baru <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="password" id="new_password" required x-model="password"
                                       autocomplete="new-password"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-colors pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                            
                            <div class="mt-3 bg-gray-50 rounded-lg p-3 border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" :class="password.length >= 8 ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-xs font-medium" :class="password.length >= 8 ? 'text-green-700' : 'text-gray-500'">Minimal 8 karakter</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" :class="/[A-Z]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-xs font-medium" :class="/[A-Z]/.test(password) ? 'text-green-700' : 'text-gray-500'">Minimal 1 huruf besar</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" :class="/[a-z]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-xs font-medium" :class="/[a-z]/.test(password) ? 'text-green-700' : 'text-gray-500'">Minimal 1 huruf kecil</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" :class="/[0-9]/.test(password) ? 'text-green-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-xs font-medium" :class="/[0-9]/.test(password) ? 'text-green-700' : 'text-gray-500'">Minimal 1 angka</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required x-model="confirmation"
                                       autocomplete="new-password"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-colors pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                            <div class="mt-2" x-show="confirmation.length > 0" x-cloak>
                                <p class="text-sm flex items-center gap-1.5 font-medium"
                                   :class="confirmation === password ? 'text-green-600' : 'text-red-500'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" x-show="confirmation === password">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" x-show="confirmation !== password" x-cloak>
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="confirmation === password ? 'Kata sandi cocok' : 'Konfirmasi kata sandi belum sama'"></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 mt-2">
                            <button type="submit"
                                    :disabled="submitting || (password.length > 0 && password !== confirmation)"
                                    class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-black hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!submitting">Perbarui Kata Sandi</span>
                                <span x-show="submitting" x-cloak>Memperbarui...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
