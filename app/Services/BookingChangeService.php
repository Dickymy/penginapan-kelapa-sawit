<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingChangeRequest;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\RoomNotAvailableException;

class BookingChangeService
{
    public function __construct(
        private AvailabilityService $availabilityService,
        private PricingService $pricingService,
        private MidtransPaymentService $paymentService
    ) {}

    /**
     * Preview the change request.
     */
    public function previewChange(Booking $booking, array $requestedData): array
    {
        $checkIn = Carbon::parse($requestedData['check_in']);
        $checkOut = Carbon::parse($requestedData['check_out']);
        $roomTypeId = $requestedData['room_type_id'] ?? $booking->room->room_type_id;
        $guestCount = $requestedData['guest_count'] ?? $booking->guest_count;

        // Validasi ketersediaan
        // Kita tidak tahu exact room ID yang baru jika ganti tipe kamar, jadi cek tipe kamarnya saja
        $excludeBookingId = ($roomTypeId == $booking->room->room_type_id) ? $booking->id : null;
        $availableRooms = $this->availabilityService->findAvailableRooms($roomTypeId, $checkIn, $checkOut, $excludeBookingId);
        
        $isAvailable = $availableRooms->isNotEmpty();

        // Hitung harga baru
        $roomType = \App\Models\RoomType::find($roomTypeId);
        $quote = $this->pricingService->calculateQuote($roomType, $checkIn, $checkOut);
        
        // Harga baru tanpa memperhitungkan poin/promo sebelumnya yang mungkin sudah di-apply. 
        // Jika kebijakan hotel mengharuskan diskon dibawa, logika bisa disesuaikan.
        // Di sini kita asumsikan gross amount dari tipe kamar yang baru dikurangi dengan total yang *sudah dibayar* sebelumnya.
        $newTotal = $quote['subtotal']; // Ini belum termasuk diskon dari booking asli.
        
        // Untuk sederhana, difference = new_subtotal - old_subtotal.
        // Jika booking pakai poin/promo, kita bisa pertahankan diskon tersebut.
        $newTotalWithOldDiscounts = max(0, $newTotal - $booking->promotion_discount - $booking->points_discount);
        
        $priceDifference = $newTotalWithOldDiscounts - $booking->total_amount;

        return [
            'is_available' => $isAvailable,
            'available_room_id' => $isAvailable ? $availableRooms->first()->id : null,
            'new_total' => $newTotalWithOldDiscounts,
            'price_difference' => $priceDifference,
            'nights' => $quote['nights'],
        ];
    }

    /**
     * Submit a new change request.
     */
    public function submitRequest(Booking $booking, array $requestedData, string $type, int $userId): BookingChangeRequest
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw new \RuntimeException('Hanya booking yang sudah dikonfirmasi yang dapat diajukan perubahan.');
        }

        $preview = $this->previewChange($booking, $requestedData);
        if (!$preview['is_available']) {
            throw new RoomNotAvailableException('Kamar tidak tersedia untuk tanggal atau tipe yang diminta.');
        }

        $originalData = [
            'check_in' => $booking->check_in->format('Y-m-d'),
            'check_out' => $booking->check_out->format('Y-m-d'),
            'room_type_id' => $booking->room->room_type_id,
            'room_id' => $booking->room_id,
            'guest_count' => $booking->guest_count,
            'total_amount' => $booking->total_amount,
        ];

        // Ensure requested data has the available room ID so admin doesn't have to guess
        $requestedData['room_id'] = $preview['available_room_id'];

        return BookingChangeRequest::create([
            'booking_id' => $booking->id,
            'user_id' => $userId,
            'type' => $type,
            'original_data' => $originalData,
            'requested_data' => $requestedData,
            'price_difference' => $preview['price_difference'],
            'status' => 'pending',
        ]);
    }

    /**
     * Admin approves the request.
     */
    public function approveRequest(BookingChangeRequest $request, int $adminId, ?string $notes = null): void
    {
        DB::transaction(function () use ($request, $adminId, $notes) {
            $booking = Booking::where('id', $request->booking_id)->lockForUpdate()->first();
            
            if ($request->status !== 'pending') {
                throw new \RuntimeException('Pengajuan sudah diproses sebelumnya.');
            }

            $requestedData = $request->requested_data;
            $newCheckIn = Carbon::parse($requestedData['check_in']);
            $newCheckOut = Carbon::parse($requestedData['check_out']);
            $newRoomId = $requestedData['room_id'];

            // Re-validate availability
            $this->availabilityService->assertRoomAvailableForBooking($newRoomId, $newCheckIn, $newCheckOut, $booking->id);

            // Update Booking
            $room = \App\Models\Room::with('roomType')->find($newRoomId);
            $quote = $this->pricingService->calculateQuote($room->roomType, $newCheckIn, $newCheckOut);
            
            $booking->update([
                'check_in' => $newCheckIn,
                'check_out' => $newCheckOut,
                'nights' => $quote['nights'],
                'room_id' => $newRoomId,
                'room_type_name_snapshot' => $room->roomType->name,
                'room_name_snapshot' => $room->name,
                'guest_count' => $requestedData['guest_count'] ?? $booking->guest_count,
                'total_amount' => $booking->total_amount + $request->price_difference,
                'subtotal' => $quote['subtotal'],
                'price_per_night_snapshot' => $quote['base_price'],
            ]);

            // Rebuild night prices
            $booking->nightPrices()->delete();
            $nightPricesData = [];
            foreach ($quote['night_prices'] as $np) {
                $nightPricesData[] = [
                    'booking_id' => $booking->id,
                    'date' => $np['date'],
                    'price' => $np['price'],
                    'label' => $np['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            \App\Models\BookingNightPrice::insert($nightPricesData);

            // Create new payment attempt if there is a positive price difference
            if ($request->price_difference > 0) {
                // Determine attempt number specifically for this change request
                $attemptNo = ($booking->payments()->max('attempt_no') ?? 0) + 1;
                $providerOrderId = "{$booking->booking_code}-CHG-{$request->id}-{$attemptNo}";

                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'provider' => 'midtrans',
                    'provider_order_id' => $providerOrderId,
                    'attempt_no' => $attemptNo,
                    'gross_amount' => $request->price_difference,
                    'status' => PaymentStatus::Unpaid->value,
                ]);

                // The admin or system will generate a snap token later, or we can generate it here
                // via an overloaded MidtransPaymentService method.
            }

            $request->update([
                'status' => 'approved',
                'admin_notes' => $notes,
                'processed_by_admin_id' => $adminId,
                'processed_at' => now(),
            ]);
        });
    }

    /**
     * Admin rejects the request.
     */
    public function rejectRequest(BookingChangeRequest $request, int $adminId, ?string $notes = null): void
    {
        $request->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
        ]);
    }
}
