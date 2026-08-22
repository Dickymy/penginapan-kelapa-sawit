<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Search available room types for given dates and guest count.
     * Informational — not authoritative for booking.
     */
    public function searchAvailableRoomTypes(Carbon $checkIn, Carbon $checkOut, int $guestCount): Collection
    {
        $roomTypes = RoomType::active()
            ->where('capacity', '>=', $guestCount)
            ->ordered()
            ->with(['images', 'facilities' => fn ($q) => $q->active()->ordered()])
            ->get();

        return $roomTypes->map(function (RoomType $roomType) use ($checkIn, $checkOut) {
            $availableRooms = $this->findAvailableRooms($roomType->id, $checkIn, $checkOut);

            return [
                'room_type' => $roomType,
                'available_count' => $availableRooms->count(),
                'available_rooms' => $availableRooms,
            ];
        })->filter(fn ($item) => $item['available_count'] > 0)->values();
    }

    /**
     * Find available physical rooms for a specific room type.
     */
    public function findAvailableRooms(int $roomTypeId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): Collection
    {
        $rooms = Room::where('room_type_id', $roomTypeId)
            ->sellable()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $rooms->filter(fn (Room $room) => $this->isRoomAvailable($room->id, $checkIn, $checkOut, $excludeBookingId));
    }

    /**
     * Check if a specific room is available for given dates.
     */
    public function isRoomAvailable(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool
    {
        // Check booking conflicts
        $hasBookingConflict = Booking::where('room_id', $roomId)
            ->where('check_in', '<', $checkOut->toDateString())
            ->where('check_out', '>', $checkIn->toDateString())
            ->where(function (Builder $query) {
                $query->whereIn('status', [
                    BookingStatus::Confirmed->value,
                    BookingStatus::CheckedIn->value,
                ])
                ->orWhere(function (Builder $q) {
                    $q->where('status', BookingStatus::PendingPayment->value)
                      ->where('payment_expires_at', '>', now());
                });
            })
            ->when($excludeBookingId, fn (Builder $q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();

        if ($hasBookingConflict) {
            return false;
        }

        // Check room block conflicts
        $hasBlockConflict = \DB::table('room_blocks')
            ->where('room_id', $roomId)
            ->where('start_date', '<', $checkOut->toDateString())
            ->where('end_date', '>', $checkIn->toDateString())
            ->exists();

        return ! $hasBlockConflict;
    }

    /**
     * Authoritative check — throws if room not available.
     * Used inside transaction after locking.
     */
    public function assertRoomAvailableForBooking(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): void
    {
        if (! $this->isRoomAvailable($roomId, $checkIn, $checkOut, $excludeBookingId)) {
            throw new RoomNotAvailableException(
                'Kamar tidak tersedia untuk tanggal yang dipilih.'
            );
        }
    }
}
