<?php

namespace App\Enums;

enum LoyaltyTransactionType: string
{
    case Earn = 'earn';
    case Redeem = 'redeem';
    case Expire = 'expire';
    case Adjustment = 'adjustment';
    case Reversal = 'reversal';

    public function label(): string
    {
        return match ($this) {
            self::Earn => 'Perolehan',
            self::Redeem => 'Penukaran',
            self::Expire => 'Kedaluwarsa',
            self::Adjustment => 'Penyesuaian',
            self::Reversal => 'Pembalikan',
        };
    }
}
