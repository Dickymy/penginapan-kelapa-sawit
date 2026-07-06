<?php

namespace App\Support\Phone;

class PhoneNormalizer
{
    /**
     * Normalize Indonesian phone number to 62-prefixed digits only.
     *
     * Contoh:
     * - 08123456789 → 628123456789
     * - +62 812-3456-789 → 628123456789
     * - 62812345678 → 62812345678
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $phone);

        // Replace leading 0 with 62
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // If no country code, prepend 62
        if (! str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}
