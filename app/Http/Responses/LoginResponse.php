<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();
        $name = explode(' ', $user->name)[0]; // First name only

        return redirect()->intended('/')
            ->with('toast_success', 'Selamat datang kembali, ' . $name . '.');
    }
}
