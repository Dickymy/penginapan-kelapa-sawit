<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;

class PricingService
{
    public function __construct(
        private PromotionService $promotionService,
        private LoyaltyPointService $loyaltyService,
    ) {}

    /**
     * Calculate number of nights (pure function).
     */
    public function calculateNights(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $checkIn->diffInDays($checkOut);
    }

    public function calculateQuote(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights = $this->calculateNights($checkIn, $checkOut);
        $basePrice = $roomType->base_price;

        // Ambil semua rate_overrides untuk room_type + rentang tanggal
        $overridesData = \App\Models\RateOverride::where('room_type_id', $roomType->id)
            ->whereBetween('date', [$checkIn->format('Y-m-d'), $checkOut->copy()->subDay()->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($override) => $override->date->format('Y-m-d'));

        $nightPrices = [];
        $subtotal = 0;

        for ($i = 0; $i < $nights; $i++) {
            $date = $checkIn->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');
            $override = $overridesData->get($dateKey);
            $price = $override ? $override->price : $basePrice;
            $label = $override ? $override->label : null;

            $nightPrices[] = [
                'date' => $dateKey,
                'price' => $price,
                'label' => $override ? ($label ?? 'Override') : null
            ];
            $subtotal += $price;
        }

        return [
            'nights' => $nights,
            'price_per_night' => $basePrice, // base price untuk backward compat
            'night_prices' => $nightPrices,  // breakdown per malam
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
     * Calculate quote with addons.
     */
    public function calculateQuoteWithAddons(array $baseQuote, array $selectedAddons): array
    {
        $addonTotal = 0;
        $addonDetails = [];
        foreach ($selectedAddons as $item) {
            // We do not use active() here so that previously purchased addons can still carry over even if deactivated by admin
            $addon = \App\Models\Addon::findOrFail($item['addon_id']);
            
            // Security: force quantity to 1 if addon type is 'single'
            $qty = $addon->isSingle() ? 1 : $item['quantity'];

            $subtotal = $addon->price * $qty;
            $addonTotal += $subtotal;
            $addonDetails[] = [
                'addon_id' => $addon->id,
                'name' => $addon->name,
                'quantity' => $qty,
                'unit_price' => $addon->price,
                'subtotal' => $subtotal,
            ];
        }

        $baseQuote['addon_total'] = $addonTotal;
        $baseQuote['addon_details'] = $addonDetails;
        $baseQuote['total_amount'] += $addonTotal;
        $baseQuote['eligible_loyalty_amount'] += $addonTotal;
        
        return $baseQuote;
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
