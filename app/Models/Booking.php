<?php

namespace App\Models;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'invoice_number', 'idempotency_key',
        'user_id', 'room_id', 'created_by_admin_id',
        'source', 'status', 'payment_status',
        'check_in', 'check_out', 'nights', 'guest_count',
        'guest_name', 'guest_email', 'guest_whatsapp',
        'arrival_estimate', 'special_request',
        'room_type_name_snapshot', 'room_name_snapshot',
        'price_per_night_snapshot', 'subtotal',
        'promotion_id', 'promotion_code_snapshot', 'promotion_discount',
        'points_redeemed', 'points_discount',
        'total_amount', 'currency', 'eligible_loyalty_amount',
        'payment_expires_at', 'policy_version_id', 'policy_accepted_at',
        'guest_access_token_hash',
        'claimed_at', 'claim_method',
        'checked_in_at', 'checked_out_at', 'completed_at',
        'cancelled_at', 'cancellation_reason', 'cancellation_notes', 'cancelled_by_admin_id',
        'needs_attention', 'attention_reason', 'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'source' => BookingSource::class,
            'check_in' => 'date',
            'check_out' => 'date',
            'nights' => 'integer',
            'guest_count' => 'integer',
            'price_per_night_snapshot' => 'integer',
            'subtotal' => 'integer',
            'promotion_discount' => 'integer',
            'points_redeemed' => 'integer',
            'points_discount' => 'integer',
            'total_amount' => 'integer',
            'eligible_loyalty_amount' => 'integer',
            'payment_expires_at' => 'datetime',
            'policy_accepted_at' => 'datetime',
            'claimed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'needs_attention' => 'boolean',
        ];
    }

    // Relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class)->orderBy('created_at');
    }

    // Accessors

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === BookingStatus::Expired;
    }

    public function getIsHoldActiveAttribute(): bool
    {
        return $this->status === BookingStatus::PendingPayment
            && $this->payment_expires_at
            && $this->payment_expires_at->isFuture();
    }
}
