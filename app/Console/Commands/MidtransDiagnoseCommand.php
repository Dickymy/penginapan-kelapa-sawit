<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class MidtransDiagnoseCommand extends Command
{
    protected $signature = 'midtrans:diagnose';
    protected $description = 'Diagnose Midtrans integration configuration (safe, no secrets exposed)';

    public function handle(): int
    {
        $this->info('=== Midtrans Integration Diagnosis ===');
        $this->newLine();

        // Environment
        $isProduction = config('midtrans.is_production');
        $this->line('Environment: ' . ($isProduction ? '🔴 PRODUCTION' : '🟢 Sandbox'));

        // Keys configured (yes/no only)
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');
        $this->line('Server Key configured: ' . ($serverKey ? '✅ Yes' : '❌ No'));
        $this->line('Client Key configured: ' . ($clientKey ? '✅ Yes' : '❌ No'));

        // Sanitization & 3DS
        $this->line('Sanitized: ' . (config('midtrans.is_sanitized') ? 'Yes' : 'No'));
        $this->line('3DS: ' . (config('midtrans.is_3ds') ? 'Yes' : 'No'));

        $this->newLine();

        // Webhook route
        $webhookRoute = Route::getRoutes()->getByName('webhook.midtrans');
        if ($webhookRoute) {
            $this->line('Webhook route: ✅ ' . $webhookRoute->uri());
        } else {
            $this->error('Webhook route: ❌ Not found');
        }

        // APP_URL
        $appUrl = config('app.url');
        $this->line('APP_URL: ' . $appUrl);

        // Expected notification URL
        $notificationUrl = rtrim($appUrl, '/') . '/webhook/midtrans';
        $this->line('Expected Notification URL: ' . $notificationUrl);

        $this->newLine();

        // Warnings
        if (!$serverKey) {
            $this->warn('⚠️  MIDTRANS_SERVER_KEY belum diisi di .env');
        }
        if (!$clientKey) {
            $this->warn('⚠️  MIDTRANS_CLIENT_KEY belum diisi di .env');
        }
        if ($appUrl === 'http://localhost' || str_contains($appUrl, 'localhost')) {
            $this->warn('⚠️  APP_URL masih localhost — webhook Midtrans tidak akan bisa menjangkau.');
            $this->warn('   Gunakan ngrok atau tunnel lain untuk testing.');
        }
        if ($isProduction) {
            $this->warn('⚠️  Mode PRODUCTION aktif! Pastikan ini disengaja.');
        }

        $this->newLine();
        $this->info('Diagnosis selesai. Tidak ada secret yang ditampilkan.');

        return self::SUCCESS;
    }
}
