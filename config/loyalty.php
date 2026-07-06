<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Earn Divisor
    |--------------------------------------------------------------------------
    |
    | Setiap Rp {earn_divisor} eligible amount = 1 poin.
    | floor(eligible_amount / earn_divisor) = jumlah poin.
    |
    */

    'earn_divisor' => (int) env('LOYALTY_EARN_DIVISOR', 1000),

    /*
    |--------------------------------------------------------------------------
    | Point Value (Rupiah)
    |--------------------------------------------------------------------------
    |
    | Nilai 1 poin dalam Rupiah saat redemption.
    |
    */

    'point_value' => (int) env('LOYALTY_POINT_VALUE', 50),

    /*
    |--------------------------------------------------------------------------
    | Minimum Redeem
    |--------------------------------------------------------------------------
    |
    | Jumlah minimum poin yang harus dimiliki untuk melakukan redemption.
    |
    */

    'min_redeem' => (int) env('LOYALTY_MIN_REDEEM', 100),

    /*
    |--------------------------------------------------------------------------
    | Maximum Redemption Percent
    |--------------------------------------------------------------------------
    |
    | Persentase maksimum subtotal booking yang dapat dibayar dengan poin.
    |
    */

    'max_redemption_percent' => (int) env('LOYALTY_MAX_REDEMPTION_PERCENT', 20),

    /*
    |--------------------------------------------------------------------------
    | Expiry Months
    |--------------------------------------------------------------------------
    |
    | Jumlah bulan sebelum poin yang diperoleh expired.
    |
    */

    'expiry_months' => (int) env('LOYALTY_EXPIRY_MONTHS', 18),

];
