<?php

namespace App\Enums;

use App\Exceptions\InvalidStatusTransitionException;

enum BookingStatus: string
{
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Confirmed => 'Dikonfirmasi',
            self::CheckedIn => 'Check-in',
            self::CheckedOut => 'Check-out',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::Expired => 'Kedaluwarsa',
            self::NoShow => 'Tidak Datang',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingPayment => 'bg-yellow-100 text-yellow-800',
            self::Confirmed => 'bg-blue-100 text-blue-800',
            self::CheckedIn => 'bg-purple-100 text-purple-800',
            self::CheckedOut => 'bg-teal-100 text-teal-800',
            self::Completed => 'bg-green-100 text-green-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Expired => 'bg-gray-100 text-gray-800',
            self::NoShow => 'bg-orange-100 text-orange-800',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PendingPayment => [self::Confirmed, self::Expired, self::Cancelled],
            self::Confirmed => [self::CheckedIn, self::Cancelled, self::NoShow],
            self::CheckedIn => [self::CheckedOut],
            self::CheckedOut => [self::Completed],
            self::Completed => [],
            self::Cancelled => [],
            self::Expired => [],
            self::NoShow => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }

    public function transitionTo(self $target): self
    {
        if (! $this->canTransitionTo($target)) {
            throw new InvalidStatusTransitionException($this, $target);
        }

        return $target;
    }

    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }

    public function isBlocking(): bool
    {
        return in_array($this, [
            self::PendingPayment,
            self::Confirmed,
            self::CheckedIn,
        ]);
    }
}
