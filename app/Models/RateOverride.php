<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateOverride extends Model
{
    protected $fillable = [
        'room_type_id',
        'date',
        'price',
        'label',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'integer',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
