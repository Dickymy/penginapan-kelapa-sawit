<?php

namespace App\Enums;

enum PromotionUsageStatus: string
{
    case Reserved = 'reserved';
    case Consumed = 'consumed';
    case Released = 'released';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Direservasi',
            self::Consumed => 'Digunakan',
            self::Released => 'Dilepaskan',
        };
    }
}
