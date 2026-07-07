<?php

namespace App\Services;

use App\Models\Booking;
use App\Support\Phone\PhoneNormalizer;
use Illuminate\Http\Request;

class BookingAccessService
{
    /**
     * Session key prefix for booking access grants.
     */
    private const SESSION_PREFIX = 'booking_access_';

    /**
     * Access grant expiry in minutes.
     */
    private const GRANT_EXPIRY_MINUTES = 60;

    /**
     * Check if the current request has authorized access to a booking.
     *
     * Access is granted for:
     * - Admin (via admin guard)
     * - Member who owns the booking
     * - Guest with a valid session access grant
     * - Guest who just created the booking (session flash)
     */
    public function hasAccess(Request $request, Booking $booking): bool
    {
        // Admin always has access
        if (auth('admin')->check()) {
            return true;
        }

        // Member who owns the booking
        $user = auth()->user();
        if ($user && $booking->user_id && $booking->user_id === $user->id) {
            return true;
        }

        // Session-based access grant (from verification or just-created)
        if ($this->hasSessionGrant($request, $booking)) {
            return true;
        }

        return false;
    }

    /**
     * Grant session access to a booking after verification.
     */
    public function grantAccess(Request $request, Booking $booking): void
    {
        $key = self::SESSION_PREFIX . $booking->booking_code;
        $request->session()->put($key, [
            'granted_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(self::GRANT_EXPIRY_MINUTES)->timestamp,
        ]);
    }

    /**
     * Grant temporary access for a just-created booking (valid for current session).
     */
    public function grantCreationAccess(Request $request, Booking $booking): void
    {
        $this->grantAccess($request, $booking);
    }

    /**
     * Verify guest identity using access token.
     */
    public function verifyByToken(Booking $booking, string $token): bool
    {
        if (empty($booking->guest_access_token_hash) || empty($token)) {
            return false;
        }

        $inputHash = hash('sha256', $token);
        return hash_equals($booking->guest_access_token_hash, $inputHash);
    }

    /**
     * Verify guest identity using email.
     */
    public function verifyByEmail(Booking $booking, string $email): bool
    {
        if (empty($booking->guest_email) || empty($email)) {
            return false;
        }

        return strtolower($email) === strtolower($booking->guest_email);
    }

    /**
     * Verify guest identity using WhatsApp number.
     */
    public function verifyByWhatsApp(Booking $booking, string $whatsapp): bool
    {
        if (empty($booking->guest_whatsapp) || empty($whatsapp)) {
            return false;
        }

        $normalized = PhoneNormalizer::normalize($whatsapp);
        return $normalized === $booking->guest_whatsapp;
    }

    /**
     * Check if there's a valid session grant for this booking.
     */
    private function hasSessionGrant(Request $request, Booking $booking): bool
    {
        $key = self::SESSION_PREFIX . $booking->booking_code;
        $grant = $request->session()->get($key);

        if (!$grant) {
            return false;
        }

        // Check expiry
        if (isset($grant['expires_at']) && now()->timestamp > $grant['expires_at']) {
            $request->session()->forget($key);
            return false;
        }

        return true;
    }
}
