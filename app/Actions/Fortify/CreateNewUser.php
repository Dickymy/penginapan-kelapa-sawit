<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Support\Phone\PhoneNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique(User::class, 'email'),
            ],
            'whatsapp' => ['required', 'string', 'max:32'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'], // normalized via model mutator
            'whatsapp' => PhoneNormalizer::normalize($input['whatsapp']),
            'password' => Hash::make($input['password']),
        ]);
    }
}
