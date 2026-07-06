<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBlock extends Model
{
    protected $fillable = [
        'room_id', 'start_date', 'end_date', 'reason_type', 'reason', 'notes', 'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Admin::class, 'created_by_admin_id'); }
}
