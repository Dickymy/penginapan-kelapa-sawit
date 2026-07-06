<?php

namespace App\Exceptions;

use App\Enums\BookingStatus;
use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(
        public readonly BookingStatus $from,
        public readonly BookingStatus $to,
    ) {
        parent::__construct(
            "Transisi status tidak valid: {$from->value} → {$to->value}"
        );
    }
}
