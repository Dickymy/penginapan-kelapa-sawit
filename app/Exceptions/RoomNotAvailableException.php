<?php

namespace App\Exceptions;

use Exception;

class RoomNotAvailableException extends Exception
{
    public function __construct(string $message = 'Kamar tidak tersedia untuk tanggal yang dipilih.')
    {
        parent::__construct($message);
    }
}
