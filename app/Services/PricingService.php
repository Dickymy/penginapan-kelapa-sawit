<?php

namespace App\Services;

use App\Models\RoomType;
use Carbon\Carbon;

class PricingService
{
    /**
     * Calculate number of nights (pure function).
     */
    public function calculateNights(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $checkIn->diffInDays($checkOut);
    }

    /**
     * Calculate full price quote for a booking.
     */
    public function calculateQuote(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights = $this->calculateNights($checkIn, $checkOut);
        $pricePerNight = $roomType->base_price;
        $subtotal = $nights * $pricePerNight;

        // V1: no promo or points in this SPEC
        $promotionDiscount = 0;
        $pointsDiscount = 0;
        $totalAmount = max(0, $subtotal - $promotionDiscount - $pointsDiscount);

        return [
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'promotion_discount' => $promotionDiscount,
            'points_discount' => $pointsDiscount,
            'total_amount' => $totalAmount,
            'eligible_loyalty_amount' => $totalAmount,
        ];
    }
}
