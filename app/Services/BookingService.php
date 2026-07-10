<?php

namespace App\Services;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\PolicyVersion;
use App\Models\Room;
use App\Models\User;
use App\Support\Phone\PhoneNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        private AvailabilityService $availability,
        private PricingService $pricing,
        private DocumentSequenceService $sequence,
    ) {}

    /**
     * Create guest booking (no user_id).
     *
     * @return array{booking: Booking, raw_token: string}
     */
    public function createGuestBooking(array $data): array
    {
        return $this->createBooking($data, null);
    }

    /**
     * Create member booking (with user_id).
     *
     * @return array{booking: Booking, raw_token: string}
     */
    public function createMemberBooking(array $data, User $user): array
    {
        return $this->createBooking($data, $user);
    }

    /**
     * Expire a pending booking.
     */
    public function expirePendingBooking(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::PendingPayment) {
            return;
        }

        $booking->update(['status' => BookingStatus::Expired]);

        BookingStatusHistory::create([
            'booking_id' => $booking->id,
            'from_status' => BookingStatus::PendingPayment->value,
            'to_status' => BookingStatus::Expired->value,
            'reason' => 'Batas waktu pembayaran terlampaui',
            'actor_type' => 'system',
            'created_at' => now(),
        ]);
    }

    /**
     * Generate a per-intent idempotency key based on booking fingerprint.
     * This ensures different room/date/guest combinations produce different keys.
     */
    private function generateIntentKey(array $data): string
    {
        $fingerprint = implode('|', [
            $data['room_type_id'],
            $data['check_in'],
            $data['check_out'],
            $data['guest_count'] ?? 1,
            $data['guest_email'] ?? '',
        ]);

        return hash('sha256', $fingerprint . '|' . ($data['idempotency_key'] ?? ''));
    }

    /**
     * Core booking creation logic.
     *
     * @return array{booking: Booking, raw_token: string}
     */
    private function createBooking(array $data, ?User $user): array
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $roomTypeId = $data['room_type_id'];
        $guestCount = $data['guest_count'] ?? 1;

        // Generate per-intent idempotency key (fingerprinted)
        $idempotencyKey = isset($data['idempotency_key']) ? $this->generateIntentKey($data) : null;

        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        $booking = DB::transaction(function () use (
            $data, $user, $checkIn, $checkOut, $roomTypeId, $guestCount,
            $idempotencyKey, $tokenHash, &$rawToken
        ) {
            // Idempotency check INSIDE transaction to prevent TOCTOU race
            if ($idempotencyKey) {
                $existing = Booking::where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
                if ($existing) {
                    $rawToken = ''; // No new token for existing booking
                    return $existing;
                }
            }
            // Find and lock an available room
            $room = $this->findAndLockRoom($roomTypeId, $checkIn, $checkOut);

            // Assert availability after lock
            $this->availability->assertRoomAvailableForBooking(
                $room->id, $checkIn, $checkOut
            );

            // Verify capacity
            $roomType = $room->roomType;
            if ($guestCount > $roomType->capacity) {
                throw new RoomNotAvailableException(
                    "Jumlah tamu ({$guestCount}) melebihi kapasitas kamar ({$roomType->capacity})."
                );
            }

            // Calculate price server-side
            $quote = $this->pricing->calculateQuote($roomType, $checkIn, $checkOut);

            // Generate booking code
            $bookingCode = $this->sequence->generateBookingCode();

            // Get current policy
            $policy = PolicyVersion::current()->first();

            // Create booking
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'idempotency_key' => $idempotencyKey,
                'user_id' => $user?->id,
                'room_id' => $room->id,
                'source' => BookingSource::Website->value,
                'status' => BookingStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Unpaid->value,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'nights' => $quote['nights'],
                'guest_count' => $guestCount,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'] ?? null,
                'guest_whatsapp' => PhoneNormalizer::normalize($data['guest_whatsapp']),
                'arrival_estimate' => $data['arrival_estimate'] ?? null,
                'special_request' => $data['special_request'] ?? null,
                'room_type_name_snapshot' => $roomType->name,
                'room_name_snapshot' => $room->name,
                'price_per_night_snapshot' => $quote['price_per_night'],
                'subtotal' => $quote['subtotal'],
                'promotion_discount' => $quote['promotion_discount'],
                'points_discount' => $quote['points_discount'],
                'total_amount' => $quote['total_amount'],
                'currency' => 'IDR',
                'eligible_loyalty_amount' => $quote['eligible_loyalty_amount'],
                'payment_expires_at' => now()->addMinutes(config('booking.hold_minutes', 30)),
                'policy_version_id' => $policy?->id,
                'policy_accepted_at' => $policy ? now() : null,
                'guest_access_token_hash' => $tokenHash,
            ]);

            // Write status history
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => null,
                'to_status' => BookingStatus::PendingPayment->value,
                'reason' => 'Booking dibuat',
                'actor_type' => $user ? 'user' : 'system',
                'actor_id' => $user?->id,
                'created_at' => now(),
            ]);

            return $booking;
        });

        return ['booking' => $booking, 'raw_token' => $rawToken];
    }

    /**
     * Create manual booking by admin (walk-in, WhatsApp, phone, etc.).
     *
     * Expiry logic per source:
     * - Website: 30min hold (automatic expiry if unpaid)
     * - OTA (Booking.com, Agoda, Traveloka): confirmed reservation, no auto-expiry
     * - WhatsApp/Phone: admin chooses status; hold_until if provided
     * - Walk-in: typically confirmed immediately
     */
    public function createManualBooking(array $data, \App\Models\Admin $admin): Booking
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $roomId = $data['room_id'];
        $guestCount = $data['guest_count'] ?? 1;

        $booking = DB::transaction(function () use ($data, $admin, $checkIn, $checkOut, $roomId, $guestCount) {
            $room = Room::where('id', $roomId)->lockForUpdate()->first();
            $this->availability->assertRoomAvailableForBooking($room->id, $checkIn, $checkOut);

            $roomType = $room->roomType;
            $pricePerNight = $data['price_per_night'] ?? $roomType->base_price;
            $nights = $this->pricing->calculateNights($checkIn, $checkOut);
            $subtotal = $nights * $pricePerNight;
            $totalAmount = $subtotal;

            $bookingCode = $this->sequence->generateBookingCode();

            $source = $data['source'];
            $paymentStatus = $data['payment_status'] ?? 'unpaid';
            $isPaid = $paymentStatus === 'paid';

            // Determine booking status and expiry based on source
            $status = $this->determineManualBookingStatus($source, $isPaid);
            $paymentExpiresAt = $this->determineManualBookingExpiry($source, $isPaid, $data);

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'room_id' => $room->id,
                'created_by_admin_id' => $admin->id,
                'source' => $source,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'nights' => $nights,
                'guest_count' => $guestCount,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'] ?? null,
                'guest_whatsapp' => PhoneNormalizer::normalize($data['guest_whatsapp']),
                'room_type_name_snapshot' => $roomType->name,
                'room_name_snapshot' => $room->name,
                'price_per_night_snapshot' => $pricePerNight,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'currency' => 'IDR',
                'eligible_loyalty_amount' => $totalAmount,
                'internal_notes' => $data['internal_notes'] ?? null,
                'payment_expires_at' => $paymentExpiresAt,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => null,
                'to_status' => $status,
                'reason' => 'Booking manual oleh admin',
                'actor_type' => 'admin',
                'actor_id' => $admin->id,
                'created_at' => now(),
            ]);

            return $booking;
        });

        return $booking;
    }

    /**
     * Determine initial status for manual booking based on source and payment.
     */
    private function determineManualBookingStatus(string $source, bool $isPaid): string
    {
        // If already paid, always confirmed
        if ($isPaid) {
            return BookingStatus::Confirmed->value;
        }

        // OTA bookings are confirmed reservations (payment handled externally)
        $otaSources = [
            BookingSource::BookingCom->value,
            BookingSource::Agoda->value,
            BookingSource::Traveloka->value,
        ];

        if (in_array($source, $otaSources)) {
            return BookingStatus::Confirmed->value;
        }

        // Walk-in without payment is unusual but still confirmed (guest is physically present)
        if ($source === BookingSource::WalkIn->value) {
            return BookingStatus::Confirmed->value;
        }

        // WhatsApp/Phone/Website unpaid: pending_payment with hold
        return BookingStatus::PendingPayment->value;
    }

    /**
     * Determine payment expiry for manual booking.
     * Non-website sources should NOT auto-expire after 30 minutes.
     */
    private function determineManualBookingExpiry(string $source, bool $isPaid, array $data): ?string
    {
        // Paid bookings don't expire
        if ($isPaid) {
            return null;
        }

        // OTA and walk-in: no automatic expiry
        $noExpirySources = [
            BookingSource::BookingCom->value,
            BookingSource::Agoda->value,
            BookingSource::Traveloka->value,
            BookingSource::WalkIn->value,
        ];

        if (in_array($source, $noExpirySources)) {
            return null;
        }

        // Admin provides custom hold duration (in minutes)
        if (!empty($data['hold_minutes'])) {
            return now()->addMinutes((int) $data['hold_minutes'])->toDateTimeString();
        }

        // WhatsApp/Phone: longer hold (24 hours default for admin-created bookings)
        if (in_array($source, [BookingSource::Whatsapp->value, BookingSource::Phone->value])) {
            return now()->addHours(24)->toDateTimeString();
        }

        // Website source (rare for manual): standard 30 minute hold
        return now()->addMinutes(config('booking.hold_minutes', 30))->toDateTimeString();
    }

    /**
     * Find first available room and lock it.
     */
    private function findAndLockRoom(int $roomTypeId, Carbon $checkIn, Carbon $checkOut): Room
    {
        $candidateRooms = Room::where('room_type_id', $roomTypeId)
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($candidateRooms as $candidate) {
            // Lock the room row
            $lockedRoom = Room::where('id', $candidate->id)->lockForUpdate()->first();

            if ($lockedRoom && $this->availability->isRoomAvailable($lockedRoom->id, $checkIn, $checkOut)) {
                return $lockedRoom;
            }
        }

        throw new RoomNotAvailableException(
            'Tidak ada kamar tersedia untuk tanggal yang dipilih.'
        );
    }
}
