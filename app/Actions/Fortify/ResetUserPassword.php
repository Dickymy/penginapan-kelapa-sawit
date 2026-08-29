<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function reset(User $user, array $input): void
    {
        $rules = $this->passwordRules();
        $rules[] = function ($attribute, $value, $fail) use ($user) {
            if (Hash::check($value, $user->password)) {
                $fail('Kata sandi baru tidak boleh sama dengan kata sandi Anda saat ini. Silakan gunakan kata sandi yang lain demi keamanan.');
            }
        };

        Validator::make($input, [
            'password' => $rules,
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
