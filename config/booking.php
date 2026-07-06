<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Booking Hold Duration
    |--------------------------------------------------------------------------
    |
    | Durasi hold kamar dalam menit setelah booking dibuat.
    | Booking akan expired jika pembayaran belum diterima dalam waktu ini.
    |
    */

    'hold_minutes' => (int) env('BOOKING_HOLD_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => 'IDR',

    /*
    |--------------------------------------------------------------------------
    | Eligible Sources for Loyalty
    |--------------------------------------------------------------------------
    |
    | Sumber booking yang eligible untuk mendapatkan loyalty points.
    |
    */

    'eligible_sources' => ['website', 'whatsapp', 'walk_in'],

    /*
    |--------------------------------------------------------------------------
    | Check-in & Check-out Time
    |--------------------------------------------------------------------------
    */

    'check_in_time' => env('BOOKING_CHECK_IN_TIME', '14:00'),
    'check_out_time' => env('BOOKING_CHECK_OUT_TIME', '12:00'),

];
