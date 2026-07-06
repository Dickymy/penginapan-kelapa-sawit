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
     * Core booking creation logic.
     *
     * @return array{booking: Booking, raw_token: string}
     */
    private function createBooking(array $data, ?User $user): array
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;

        // Check idempotency
        if ($idempotencyKey) {
            $existing = Booking::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return ['booking' => $existing, 'raw_token' => ''];
            }
        }

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $roomTypeId = $data['room_type_id'];
        $guestCount = $data['guest_count'] ?? 1;

        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        $booking = DB::transaction(function () use (
            $data, $user, $checkIn, $checkOut, $roomTypeId, $guestCount,
            $idempotencyKey, $tokenHash
        ) {
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
