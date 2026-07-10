<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Setting;
use App\Support\Phone\PhoneNormalizer;

class WhatsAppHelper
{
    public static function bookingLink(Booking $booking): string
    {
        $phone = Setting::get('contact', 'whatsapp', '');
        $message = "Halo, saya ingin menanyakan booking:\n"
            . "Kode: {$booking->booking_code}\n"
            . "Kamar: {$booking->room_type_name_snapshot}\n"
            . "Tanggal: {$booking->check_in->format('d/m/Y')} - {$booking->check_out->format('d/m/Y')}\n"
            . "Total: Rp" . number_format($booking->total_amount, 0, ',', '.');

        return WhatsApp::url($phone, $message) ?? '#';
    }

    public static function contactLink(): string
    {
        $phone = Setting::get('contact', 'whatsapp', '');

        return WhatsApp::url($phone) ?? '#';
    }
}
