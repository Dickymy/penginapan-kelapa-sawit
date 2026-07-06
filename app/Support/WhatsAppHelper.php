<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Setting;

class WhatsAppHelper
{
    public static function bookingLink(Booking $booking): string
    {
        $phone = Setting::get('contact', 'whatsapp', '');
        if (empty($phone)) return '#';

        $message = "Halo, saya ingin menanyakan booking:\n"
            . "Kode: {$booking->booking_code}\n"
            . "Kamar: {$booking->room_type_name_snapshot}\n"
            . "Tanggal: {$booking->check_in->format('d/m/Y')} - {$booking->check_out->format('d/m/Y')}\n"
            . "Total: Rp" . number_format($booking->total_amount, 0, ',', '.');

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    public static function contactLink(): string
    {
        $phone = Setting::get('contact', 'whatsapp', '');
        if (empty($phone)) return '#';
        return "https://wa.me/{$phone}";
    }
}
