<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Buat akun admin baru untuk production';

    public function handle(): int
    {
        $name = $this->ask('Nama admin');
        $email = $this->ask('Email admin');
        $password = $this->secret('Password (min 8 karakter)');
        $passwordConfirm = $this->secret('Konfirmasi password');

        // Validate
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirm,
        ], [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $email = strtolower(trim($email));

        // Check duplicate
        if (Admin::where('email', $email)->exists()) {
            $this->error("Admin dengan email {$email} sudah ada.");
            return self::FAILURE;
        }

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->info("Admin berhasil dibuat: {$email}");

        return self::SUCCESS;
    }
}
