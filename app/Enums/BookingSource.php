<?php

namespace App\Enums;

enum BookingSource: string
{
    case Website = 'website';
    case Whatsapp = 'whatsapp';
    case BookingCom = 'booking_com';
    case Agoda = 'agoda';
    case Traveloka = 'traveloka';
    case WalkIn = 'walk_in';
    case Phone = 'phone';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Whatsapp => 'WhatsApp',
            self::BookingCom => 'Booking.com',
            self::Agoda => 'Agoda',
            self::Traveloka => 'Traveloka',
            self::WalkIn => 'Walk-in',
            self::Phone => 'Telepon',
            self::Other => 'Lainnya',
        };
    }
}
