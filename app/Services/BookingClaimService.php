<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Collection;

class BookingClaimService
{
    public function getClaimableBookings(User $user): Collection
    {
        if (!$user->hasVerifiedEmail()) {
            return collect();
        }

        return Booking::whereNull('user_id')
            ->where('guest_email', strtolower($user->email))
            ->get();
    }

    public function claimByEmail(User $user, Booking $booking): void
    {
        if ($booking->user_id !== null) {
            throw new \RuntimeException('Booking sudah diklaim.');
        }

        if (!$user->hasVerifiedEmail()) {
            throw new \RuntimeException('Email harus terverifikasi untuk mengklaim booking.');
        }

        if (strtolower($user->email) !== strtolower($booking->guest_email)) {
            throw new \RuntimeException('Email tidak cocok dengan booking.');
        }

        $booking->update([
            'user_id' => $user->id,
            'claimed_at' => now(),
            'claim_method' => 'email_match',
        ]);
    }
}
