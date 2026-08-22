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

    protected $guarded = ['id'];

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
            'confirmation_email_sent_at' => 'datetime',
            'payment_email_sent_at' => 'datetime',
            'reminder_email_sent_at' => 'datetime',
            'checkout_email_sent_at' => 'datetime',
            'cancellation_email_sent_at' => 'datetime',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(BookingChangeRequest::class);
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function nightPrices()
    {
        return $this->hasMany(BookingNightPrice::class)->orderBy('date');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    // Accessors

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp' . number_format($this->subtotal, 0, ',', '.');
    }

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
