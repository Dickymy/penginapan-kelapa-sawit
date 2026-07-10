<?php

namespace App\Support;

use App\Support\Phone\PhoneNormalizer;

class WhatsApp
{
    /**
     * Generate a wa.me URL from a phone number with optional prefilled message.
     *
     * Returns null if the phone number is invalid.
     */
    public static function url(?string $phone, ?string $message = null): ?string
    {
        $normalized = PhoneNormalizer::normalize($phone);

        if ($normalized === null || ! self::isValid($normalized)) {
            return null;
        }

        $url = "https://wa.me/{$normalized}";

        if ($message !== null && trim($message) !== '') {
            $url .= '?text=' . rawurlencode(trim($message));
        }

        return $url;
    }

    /**
     * Check if a normalized number has a valid length for Indonesian numbers.
     * Indonesian mobile numbers: 62 + 8-13 digits = 10-15 total digits.
     */
    public static function isValid(?string $normalized): bool
    {
        if ($normalized === null) {
            return false;
        }

        $length = strlen($normalized);

        return $length >= 10 && $length <= 15 && str_starts_with($normalized, '62');
    }

    /**
     * Generate a WhatsApp share URL (wa.me with text only, no specific phone).
     * Used for "share to WhatsApp" buttons.
     */
    public static function shareUrl(string $text): string
    {
        return 'https://wa.me/?text=' . rawurlencode($text);
    }
}
