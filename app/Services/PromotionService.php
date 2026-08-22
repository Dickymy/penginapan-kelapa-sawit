<?php

namespace App\Services;

use App\Enums\PromotionType;
use App\Enums\PromotionUsageStatus;
use App\Models\Booking;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function validateForQuote(string $code, int $subtotal, ?User $user = null): array
    {
        $promo = Promotion::where('code', strtoupper(trim($code)))->first();

        if (!$promo) {
            throw new \RuntimeException('Kode promo tidak ditemukan.');
        }
        if (!$promo->is_active) {
            throw new \RuntimeException('Promo tidak aktif.');
        }
        if (now()->lt($promo->starts_at) || now()->gt($promo->ends_at)) {
            throw new \RuntimeException('Promo di luar masa berlaku.');
        }
        if ($subtotal < $promo->minimum_booking_amount) {
            throw new \RuntimeException("Minimum booking Rp" . number_format($promo->minimum_booking_amount, 0, ',', '.'));
        }

        // Check quota
        if ($promo->usage_quota !== null) {
            $usedCount = PromotionUsage::where('promotion_id', $promo->id)
                ->whereIn('status', [PromotionUsageStatus::Reserved->value, PromotionUsageStatus::Consumed->value])->count();
            if ($usedCount >= $promo->usage_quota) {
                throw new \RuntimeException('Kuota promo habis.');
            }
        }

        // Check per-user limit
        if ($user && $promo->max_usage_per_user !== null) {
            $userUsed = PromotionUsage::where('promotion_id', $promo->id)
                ->where('user_id', $user->id)
                ->whereIn('status', [PromotionUsageStatus::Reserved->value, PromotionUsageStatus::Consumed->value])->count();
            if ($userUsed >= $promo->max_usage_per_user) {
                throw new \RuntimeException('Anda sudah menggunakan promo ini.');
            }
        }

        $discount = $this->calculateDiscount($promo, $subtotal);

        return ['promotion' => $promo, 'discount' => $discount];
    }

    public function calculateDiscount(Promotion $promo, int $subtotal): int
    {
        if ($promo->type === PromotionType::Fixed) {
            return min($promo->value, $subtotal);
        }

        // Percentage
        $discount = (int) floor($subtotal * $promo->value / 100);
        if ($promo->maximum_discount !== null) {
            $discount = min($discount, $promo->maximum_discount);
        }

        return min($discount, $subtotal);
    }

    public function reserveForBooking(Promotion $promo, Booking $booking, ?User $user = null): void
    {
        DB::transaction(function () use ($promo, $booking, $user) {
            $promo = Promotion::where('id', $promo->id)->lockForUpdate()->first();

            if ($promo->usage_quota !== null) {
                $usedCount = PromotionUsage::where('promotion_id', $promo->id)
                    ->whereIn('status', [PromotionUsageStatus::Reserved->value, PromotionUsageStatus::Consumed->value])->count();
                if ($usedCount >= $promo->usage_quota) {
                    throw new \RuntimeException('Kuota promo habis.');
                }
            }

            PromotionUsage::create([
                'promotion_id' => $promo->id,
                'booking_id' => $booking->id,
                'user_id' => $user?->id,
                'status' => PromotionUsageStatus::Reserved->value,
                'discount_amount' => $booking->promotion_discount,
                'reserved_at' => now(),
            ]);
        });
    }

    public function consumeForBooking(Booking $booking): void
    {
        PromotionUsage::where('booking_id', $booking->id)
            ->where('status', PromotionUsageStatus::Reserved->value)
            ->update([
                'status' => PromotionUsageStatus::Consumed->value,
                'consumed_at' => now(),
            ]);
    }

    public function releaseForBooking(Booking $booking): void
    {
        PromotionUsage::where('booking_id', $booking->id)
            ->where('status', PromotionUsageStatus::Reserved->value)
            ->update([
                'status' => PromotionUsageStatus::Released->value,
                'released_at' => now(),
            ]);
    }
}
