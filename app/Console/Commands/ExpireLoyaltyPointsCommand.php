<?php

namespace App\Console\Commands;

use App\Models\LoyaltyTransaction;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Console\Command;

class ExpireLoyaltyPointsCommand extends Command
{
    protected $signature = 'loyalty:expire-points';

    protected $description = 'Expire loyalty points yang sudah melewati batas waktu';

    public function handle(LoyaltyPointService $service): int
    {
        $userIds = LoyaltyTransaction::where('remaining_points', '>', 0)
            ->where('expires_at', '<=', now())
            ->distinct()
            ->pluck('user_id');

        $count = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $service->expirePointsForUser($user);
                $count++;
            }
        }

        $this->info("Processed {$count} user(s) with expired points.");

        return self::SUCCESS;
    }
}
