<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;

class PricingService
{
    public function __construct(
        private ?PromotionService $promotionService = null,
        private ?LoyaltyPointService $loyaltyService = null,
    ) {}

    /**
     * Calculate number of nights (pure function).
     */
    public function calculateNights(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $checkIn->diffInDays($checkOut);
    }

    /**
     * Calculate full price quote for a booking (no promo/points).
     */
    public function calculateQuote(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights = $this->calculateNights($checkIn, $checkOut);
        $pricePerNight = $roomType->base_price;
        $subtotal = $nights * $pricePerNight;

        return [
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'promotion_discount' => 0,
            'points_discount' => 0,
            'points_redeemed' => 0,
            'total_amount' => $subtotal,
            'eligible_loyalty_amount' => $subtotal,
            'promotion' => null,
        ];
    }

    /**
     * Calculate quote with promotion code applied.
     */
    public function calculateQuoteWithPromo(RoomType $roomType, Carbon $checkIn, Carbon $checkOut, string $promoCode, ?User $user = null): array
    {
        $quote = $this->calculateQuote($roomType, $checkIn, $checkOut);

        $result = $this->promotionService->validateForQuote($promoCode, $quote['subtotal'], $user);
        $discount = $result['discount'];

        $totalAmount = max(0, $quote['subtotal'] - $discount);

        $quote['promotion_discount'] = $discount;
        $quote['total_amount'] = $totalAmount;
        $quote['eligible_loyalty_amount'] = $totalAmount;
        $quote['promotion'] = $result['promotion'];

        return $quote;
    }

    /**
     * Calculate quote with loyalty points applied.
     */
    public function calculateQuoteWithPoints(RoomType $roomType, Carbon $checkIn, Carbon $checkOut, int $requestedPoints, User $user): array
    {
        $quote = $this->calculateQuote($roomType, $checkIn, $checkOut);

        $pointValue = config('loyalty.point_value', 50);
        $maxPercent = config('loyalty.max_redemption_percent', 20);
        $minRedeem = config('loyalty.min_redeem', 100);

        $balance = $this->loyaltyService->getBalance($user);

        if ($requestedPoints < $minRedeem) {
            throw new \RuntimeException("Minimum redeem {$minRedeem} poin.");
        }

        if ($requestedPoints > $balance) {
            throw new \RuntimeException('Saldo poin tidak mencukupi.');
        }

        $maxDiscount = (int) floor($quote['subtotal'] * $maxPercent / 100);
        $requestedDiscount = $requestedPoints * $pointValue;
        $actualDiscount = min($requestedDiscount, $maxDiscount);
        $actualPoints = (int) ceil($actualDiscount / $pointValue);

        $totalAmount = max(0, $quote['subtotal'] - $actualDiscount);

        $quote['points_discount'] = $actualDiscount;
        $quote['points_redeemed'] = $actualPoints;
        $quote['total_amount'] = $totalAmount;
        // Points discount is NOT eligible for loyalty earn
        $quote['eligible_loyalty_amount'] = $totalAmount;

        return $quote;
    }
}
