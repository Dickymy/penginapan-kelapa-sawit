@extends('layouts.public')

@section('title', 'Reset Kata Sandi - Penginapan Kelapa Sawit')

@section('content')
    @php
        $isTokenInvalid = false;
        
        // 1. Cek dari pesan error (jika form sudah pernah disubmit)
        if ($errors->has('email') && (str_contains(strtolower($errors->first('email')), 'token') || str_contains(strtolower($errors->first('email')), 'kedaluwarsa'))) {
            $isTokenInvalid = true;
        } 
        // 2. Cek langsung ke database (jika halaman baru diakses pertama kali)
        else {
            $email = $request->email;
            $token = $request->route('token');
            $record = \DB::table('password_reset_tokens')->where('email', $email)->first();
            
            if (!$record) {
                // Token sudah dihapus (sudah dipakai) atau tidak ada
                $isTokenInvalid = true;
            } elseif (!\Illuminate\Support\Facades\Hash::check($token, $record->token)) {
                // Token ada tapi tidak cocok dengan hash di database
                $isTokenInvalid = true;
            } else {
                // Cek masa berlaku token (default biasanya 60 menit)
                $expiresAt = \Carbon\Carbon::parse($record->created_at)->addMinutes(config('auth.passwords.users.expire', 60));
                if (now()->isAfter($expiresAt)) {
                    $isTokenInvalid = true;
                }
            }
        }
    @endphp

<div class="max-w-md mx-auto px-4 py-12">
    @if(!$isTokenInvalid)
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Atur Ulang Kata Sandi</h1>
    @endif

    @if($isTokenInvalid)
        <div class="text-center py-8 px-4">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-sm border border-red-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Tautan Kadaluwarsa</h2>
            <p class="text-gray-500 mb-8 leading-relaxed">Tautan reset kata sandi ini sudah tidak berlaku atau sudah pernah digunakan sebelumnya. Demi keamanan, tautan hanya bisa digunakan satu kali.</p>
            <a href="{{ route('password.request') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition w-full shadow-lg shadow-primary-600/30">
                Minta Tautan Baru
            </a>
            <div class="mt-6">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-primary-600 font-medium">Kembali ke halaman Masuk</a>
            </div>
        </div>
    @else
        @if ($errors->any())
            <x-alert type="error" message="Beberapa data belum benar. Silakan periksa kembali." />
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" 
              x-data="{ 
                  submitting: false,
                  password: '',
                  passwordConfirmation: '',
                  get isMatch() {
                      return this.password !== '' && this.password === this.passwordConfirmation;
                  },
                  get isComplete() {
                      return this.password.length >= 8 && /[A-Z]/.test(this.password) && /[a-z]/.test(this.password) && /[0-9]/.test(this.password);
                  }
              }" 
              @submit="if(!isMatch || !isComplete) { $event.preventDefault(); return; } submitting = true">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required readonly
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-gray-50 text-gray-600 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                <x-form-error field="email" />
            </div>

            <div>
                <x-password-input name="password" label="Kata Sandi Baru" :required="true" :show-hints="true" autocomplete="new-password" x-model="password" />
            </div>

            <div>
                <x-password-input name="password_confirmation" label="Konfirmasi Kata Sandi Baru" :required="true" autocomplete="new-password" x-model="passwordConfirmation" />
                
                {{-- Realtime Feedback --}}
                <div x-show="passwordConfirmation.length > 0" x-cloak class="mt-2 flex items-center gap-1.5 text-sm transition-all duration-300">
                    <svg x-show="isMatch" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-show="isMatch" class="text-green-600 font-medium">Kata sandi cocok</span>
                    
                    <svg x-show="!isMatch" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span x-show="!isMatch" class="text-red-600 font-medium">Kata sandi belum cocok</span>
                </div>
            </div>

            <button type="submit"
                    :disabled="submitting || !isMatch || !isComplete"
                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-60 disabled:cursor-not-allowed mt-2">
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!submitting">Simpan Kata Sandi Baru</span>
                <span x-show="submitting" x-cloak>Menyimpan...</span>
            </button>
        </form>
    @endif
</div>
@endsection
