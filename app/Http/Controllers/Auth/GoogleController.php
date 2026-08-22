<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        // Check existing social account
        $socialAccount = SocialAccount::where('provider', 'google')
            ->where('provider_user_id', $googleUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;

            // Block inactive users
            if (!$user->is_active) {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda tidak dapat digunakan. Silakan hubungi penginapan.');
            }

            Auth::login($user);
            request()->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(config('fortify.home', '/member/dashboard'))
                ->with('toast_success', 'Selamat datang kembali, ' . $user->name . '.');
        }

        $email = strtolower($googleUser->getEmail());

        // Use transaction to prevent race conditions / duplicate users
        $result = DB::transaction(function () use ($googleUser, $email) {
            $user = User::where('email', $email)->first();
            $isNewUser = false;

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'email_verified_at' => $googleUser->user['email_verified'] ?? false ? now() : null,
                    'avatar_url' => $googleUser->getAvatar(),
                    'is_active' => true,
                ]);
                $isNewUser = true;
            } else {
                // Block inactive users
                if (!$user->is_active) {
                    return ['user' => null, 'isNewUser' => false, 'blocked' => true];
                }

                // Link existing account - update avatar if not set
                if (!$user->avatar_url && $googleUser->getAvatar()) {
                    $user->update(['avatar_url' => $googleUser->getAvatar()]);
                }

                // Mark email as verified if Google says so
                if (!$user->email_verified_at && ($googleUser->user['email_verified'] ?? false)) {
                    $user->update(['email_verified_at' => now()]);
                }
            }

            // Create social account link (use firstOrCreate to prevent duplicates)
            SocialAccount::firstOrCreate([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => $googleUser->getId(),
            ], [
                'provider_email' => $email,
                'provider_email_verified' => $googleUser->user['email_verified'] ?? false,
            ]);

            return ['user' => $user, 'isNewUser' => $isNewUser, 'blocked' => false];
        });

        // Handle blocked user
        if ($result['blocked']) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak dapat digunakan. Silakan hubungi penginapan.');
        }

        $user = $result['user'];
        $isNewUser = $result['isNewUser'];

        Auth::login($user);
        request()->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        // Determine redirect based on profile completeness
        if ($isNewUser || empty($user->whatsapp)) {
            session(['show_onboarding' => true]);

            $message = $isNewUser
                ? 'Akun Google berhasil terhubung. Selamat datang!'
                : 'Akun Google berhasil dihubungkan.';

            return redirect()->route('member.dashboard')
                ->with('toast_success', $message);
        }

        return redirect()->intended('/')
            ->with('toast_success', 'Selamat datang kembali, ' . $user->name . '.');
    }
}
