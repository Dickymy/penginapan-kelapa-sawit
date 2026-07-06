<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('DEV_ADMIN_NAME');
        $email = env('DEV_ADMIN_EMAIL');
        $password = env('DEV_ADMIN_PASSWORD');

        if (empty($name) || empty($email) || empty($password)) {
            $this->command->warn('DEV_ADMIN credentials not set in .env — skipping admin seeder.');

            return;
        }

        Admin::updateOrCreate(
            ['email' => strtolower($email)],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->command->info("Admin created: {$email}");
    }
}
