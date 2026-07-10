<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        session(['show_onboarding' => true]);

        return redirect()->route('member.dashboard')
            ->with('toast_success', 'Selamat datang di Penginapan Kelapa Sawit!');
    }
}
