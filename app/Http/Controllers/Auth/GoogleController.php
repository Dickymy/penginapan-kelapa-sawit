<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        $socialAccount = SocialAccount::where('provider', 'google')
            ->where('provider_user_id', $googleUser->getId())
            ->first();

        if ($socialAccount) {
            Auth::login($socialAccount->user);
            request()->session()->regenerate();
            return redirect()->intended(config('fortify.home', '/member/dashboard'));
        }

        $email = strtolower($googleUser->getEmail());
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $email,
                'email_verified_at' => $googleUser->user['email_verified'] ?? false ? now() : null,
                'avatar_url' => $googleUser->getAvatar(),
                'is_active' => true,
            ]);
        }

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $googleUser->getId(),
            'provider_email' => $email,
            'provider_email_verified' => $googleUser->user['email_verified'] ?? false,
        ]);

        Auth::login($user);
        request()->session()->regenerate();
        return redirect()->intended(config('fortify.home', '/member/dashboard'));
    }
}
