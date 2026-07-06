<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\LoyaltyTransactionType;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\LoyaltyPointAllocation;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyPointService
{
    public function getBalance(User $user): int
    {
        return (int) LoyaltyTransaction::where('user_id', $user->id)->sum('points');
    }

    public function awardForCompletedBooking(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::Completed) {
            return;
        }
        if (!$booking->user_id) {
            return;
        }

        $eligibleSources = config('booking.eligible_sources', ['website', 'whatsapp', 'walk_in']);
        if (!in_array($booking->source->value, $eligibleSources)) {
            return;
        }

        $idempotencyKey = "earn:booking:{$booking->id}";
        if (LoyaltyTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
            return;
        }

        $earnDivisor = config('loyalty.earn_divisor', 1000);
        $points = (int) floor($booking->eligible_loyalty_amount / $earnDivisor);
        if ($points <= 0) {
            return;
        }

        $user = User::find($booking->user_id);

        DB::transaction(function () use ($user, $booking, $points, $idempotencyKey) {
            $user = User::where('id', $user->id)->lockForUpdate()->first();
            $currentBalance = $this->getBalance($user);

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => LoyaltyTransactionType::Earn->value,
                'points' => $points,
                'balance_after' => $currentBalance + $points,
                'remaining_points' => $points,
                'description' => "Poin dari booking {$booking->booking_code}",
                'expires_at' => now()->addMonths(config('loyalty.expiry_months', 18)),
                'idempotency_key' => $idempotencyKey,
            ]);

            $user->update(['loyalty_balance_cache' => $currentBalance + $points]);
        });
    }

    public function redeemForBooking(User $user, Booking $booking, int $requestedPoints): int
    {
        $pointValue = config('loyalty.point_value', 50);
        $maxPercent = config('loyalty.max_redemption_percent', 20);
        $minRedeem = config('loyalty.min_redeem', 100);

        if ($requestedPoints < $minRedeem) {
            throw new \RuntimeException("Minimum redeem {$minRedeem} poin.");
        }

        $maxDiscount = (int) floor($booking->subtotal * $maxPercent / 100);
        $requestedDiscount = $requestedPoints * $pointValue;
        $actualDiscount = min($requestedDiscount, $maxDiscount);
        $actualPoints = (int) ceil($actualDiscount / $pointValue);

        $idempotencyKey = "redeem:booking:{$booking->id}";

        DB::transaction(function () use ($user, $booking, $actualPoints, $actualDiscount, $idempotencyKey) {
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            if (LoyaltyTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
                return;
            }

            // Get available lots (FIFO by expiry)
            $lots = LoyaltyTransaction::where('user_id', $user->id)
                ->where('remaining_points', '>', 0)
                ->where('expires_at', '>', now())
                ->whereIn('type', [LoyaltyTransactionType::Earn->value, LoyaltyTransactionType::Adjustment->value])
                ->orderBy('expires_at')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            $totalAvailable = $lots->sum('remaining_points');
            if ($totalAvailable < $actualPoints) {
                throw new \RuntimeException('Saldo poin tidak mencukupi.');
            }

            $currentBalance = $this->getBalance($user);

            $debit = LoyaltyTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => LoyaltyTransactionType::Redeem->value,
                'points' => -$actualPoints,
                'balance_after' => $currentBalance - $actualPoints,
                'remaining_points' => 0,
                'description' => "Redeem untuk booking {$booking->booking_code}",
                'idempotency_key' => $idempotencyKey,
            ]);

            // Allocate from lots (FIFO)
            $remaining = $actualPoints;
            foreach ($lots as $lot) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min($remaining, $lot->remaining_points);

                LoyaltyPointAllocation::create([
                    'debit_transaction_id' => $debit->id,
                    'credit_transaction_id' => $lot->id,
                    'points' => $take,
                ]);

                $lot->decrement('remaining_points', $take);
                $remaining -= $take;
            }

            $user->update(['loyalty_balance_cache' => $currentBalance - $actualPoints]);
        });

        return $actualDiscount;
    }

    public function reverseRedemptionForBooking(Booking $booking): void
    {
        $idempotencyKey = "reversal:redeem:booking:{$booking->id}";
        if (LoyaltyTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
            return;
        }

        $redeemTx = LoyaltyTransaction::where('booking_id', $booking->id)
            ->where('type', LoyaltyTransactionType::Redeem->value)
            ->first();

        if (!$redeemTx) {
            return;
        }

        DB::transaction(function () use ($booking, $redeemTx, $idempotencyKey) {
            $user = User::where('id', $redeemTx->user_id)->lockForUpdate()->first();
            $pointsToReturn = abs($redeemTx->points);
            $currentBalance = $this->getBalance($user);

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => LoyaltyTransactionType::Reversal->value,
                'points' => $pointsToReturn,
                'balance_after' => $currentBalance + $pointsToReturn,
                'remaining_points' => 0,
                'description' => "Pembalikan redeem booking {$booking->booking_code}",
                'source_transaction_id' => $redeemTx->id,
                'idempotency_key' => $idempotencyKey,
            ]);

            // Restore allocations
            $allocations = LoyaltyPointAllocation::where('debit_transaction_id', $redeemTx->id)->get();
            foreach ($allocations as $alloc) {
                LoyaltyTransaction::where('id', $alloc->credit_transaction_id)
                    ->increment('remaining_points', $alloc->points);
            }

            $user->update(['loyalty_balance_cache' => $currentBalance + $pointsToReturn]);
        });
    }

    public function expirePointsForUser(User $user): void
    {
        $lots = LoyaltyTransaction::where('user_id', $user->id)
            ->where('remaining_points', '>', 0)
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($lots as $lot) {
            $idempotencyKey = "expire:loyalty_transaction:{$lot->id}";
            if (LoyaltyTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
                continue;
            }

            DB::transaction(function () use ($user, $lot, $idempotencyKey) {
                $user = User::where('id', $user->id)->lockForUpdate()->first();
                $lot = LoyaltyTransaction::where('id', $lot->id)->lockForUpdate()->first();
                if ($lot->remaining_points <= 0) {
                    return;
                }

                $expirePoints = $lot->remaining_points;
                $currentBalance = $this->getBalance($user);

                $debit = LoyaltyTransaction::create([
                    'user_id' => $user->id,
                    'type' => LoyaltyTransactionType::Expire->value,
                    'points' => -$expirePoints,
                    'balance_after' => $currentBalance - $expirePoints,
                    'remaining_points' => 0,
                    'description' => 'Poin kedaluwarsa',
                    'source_transaction_id' => $lot->id,
                    'idempotency_key' => $idempotencyKey,
                ]);

                LoyaltyPointAllocation::create([
                    'debit_transaction_id' => $debit->id,
                    'credit_transaction_id' => $lot->id,
                    'points' => $expirePoints,
                ]);

                $lot->update(['remaining_points' => 0]);
                $user->update(['loyalty_balance_cache' => $currentBalance - $expirePoints]);
            });
        }
    }

    public function adjustPoints(User $user, int $points, string $reason, Admin $admin): void
    {
        DB::transaction(function () use ($user, $points, $reason, $admin) {
            $user = User::where('id', $user->id)->lockForUpdate()->first();
            $currentBalance = $this->getBalance($user);

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'type' => LoyaltyTransactionType::Adjustment->value,
                'points' => $points,
                'balance_after' => $currentBalance + $points,
                'remaining_points' => $points > 0 ? $points : 0,
                'description' => $reason,
                'expires_at' => $points > 0 ? now()->addMonths(config('loyalty.expiry_months', 18)) : null,
                'created_by_admin_id' => $admin->id,
                'idempotency_key' => 'adjust:' . $user->id . ':' . now()->timestamp . ':' . $admin->id,
            ]);

            $user->update(['loyalty_balance_cache' => $currentBalance + $points]);
        });
    }
}
