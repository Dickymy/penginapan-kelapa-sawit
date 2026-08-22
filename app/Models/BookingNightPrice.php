<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingNightPrice extends Model
{
    protected $fillable = [
        'booking_id',
        'date',
        'price',
        'label',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
