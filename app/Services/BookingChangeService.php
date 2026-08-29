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
        private MidtransPaymentService $paymentService,
        private LoyaltyPointService $loyaltyPointService
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

        // Hitung harga baru kamar (subtotal)
        $roomType = \App\Models\RoomType::find($roomTypeId);
        $quote = $this->pricingService->calculateQuote($roomType, $checkIn, $checkOut);
        
        // Bawa (carry over) layanan tambahan yang lama ke perhitungan baru
        $existingAddons = $booking->addons->map(function($addon) {
            return [
                'addon_id' => $addon->addon_id,
                'quantity' => $addon->quantity,
            ];
        })->toArray();

        if (!empty($existingAddons)) {
            $quote = $this->pricingService->calculateQuoteWithAddons($quote, $existingAddons);
        } else {
            $quote['addon_total'] = 0;
            $quote['addon_details'] = [];
        }

        $newTotal = $quote['total_amount']; // Ini berisi subtotal kamar + total addons

        // Jika booking pakai poin/promo, kita pertahankan diskon tersebut.
        $newTotalWithOldDiscounts = max(0, $newTotal - $booking->promotion_discount - $booking->points_discount);
        
        $priceDifference = $newTotalWithOldDiscounts - $booking->total_amount;

        return [
            'is_available' => $isAvailable,
            'available_room_id' => $isAvailable ? $availableRooms->first()->id : null,
            'room_subtotal' => $quote['subtotal'],
            'addon_total' => $quote['addon_total'],
            'addon_details' => $quote['addon_details'],
            'promotion_discount' => $booking->promotion_discount,
            'points_discount' => $booking->points_discount,
            'new_total' => $newTotalWithOldDiscounts,
            'old_total' => $booking->total_amount,
            'price_difference' => $priceDifference,
            'nights' => $quote['nights'],
            'message' => $isAvailable ? 'Kamar tersedia.' : 'Kamar tidak tersedia untuk tipe dan tanggal tersebut.',
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

        if ($booking->changeRequests()->where('status', 'pending')->exists()) {
            throw new \RuntimeException('Anda sudah memiliki pengajuan perubahan yang sedang diproses (Pending). Silakan tunggu admin memproses pengajuan tersebut sebelum membuat yang baru.');
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
        $requestedData['addon_details'] = $preview['addon_details'];
        $requestedData['new_total'] = $preview['new_total'];

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
     * Preview cancellation request.
     */
    public function previewCancellation(Booking $booking): array
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw new \RuntimeException('Hanya booking yang sudah dikonfirmasi yang dapat dibatalkan.');
        }

        // Aturan Refund Bertingkat:
        // H-3 atau lebih = 100% refund
        // H-1 atau H-2 = 50% refund
        // H-0 atau sudah lewat = Tidak bisa batal
        $daysUntilCheckIn = now()->startOfDay()->diffInDays($booking->check_in->startOfDay(), false);
        
        if ($daysUntilCheckIn < 1) {
            throw new \Exception('Pembatalan hanya dapat dilakukan maksimal H-1 sebelum tanggal check-in.');
        }

        $penaltyApplied = false;
        if ($daysUntilCheckIn >= 3) {
            $refundableAmount = $booking->total_amount;
        } else {
            // H-1 atau H-2
            $refundableAmount = $booking->total_amount * 0.5;
            $penaltyApplied = true;
        }

        return [
            'is_eligible' => true,
            'refundable_amount' => $refundableAmount,
            'penalty_applied' => $penaltyApplied,
            'points_to_return' => $booking->points_discount > 0 ? true : false,
        ];
    }

    /**
     * Submit cancellation request.
     */
    public function submitCancellation(Booking $booking, array $requestedData, int $userId): BookingChangeRequest
    {
        if ($booking->changeRequests()->where('status', 'pending')->exists()) {
            throw new \RuntimeException('Anda sudah memiliki pengajuan yang sedang diproses (Pending).');
        }

        $preview = $this->previewCancellation($booking);

        $originalData = [
            'check_in' => $booking->check_in->format('Y-m-d'),
            'check_out' => $booking->check_out->format('Y-m-d'),
            'room_type_id' => $booking->room->room_type_id,
            'room_id' => $booking->room_id,
            'guest_count' => $booking->guest_count,
            'total_amount' => $booking->total_amount,
            'points_discount' => $booking->points_discount,
        ];

        $requestedData['penalty_applied'] = $preview['penalty_applied'];

        return BookingChangeRequest::create([
            'booking_id' => $booking->id,
            'user_id' => $userId,
            'type' => 'cancellation',
            'original_data' => $originalData,
            'requested_data' => $requestedData, // Berisi bank_name, account_number, account_name, reason, penalty_applied
            'price_difference' => -$preview['refundable_amount'],
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

            if ($request->type === 'cancellation') {
                $booking->update([
                    'status' => BookingStatus::Cancelled->value,
                ]);

                // Lepaskan kamar dari BookingNightPrice
                $booking->nightPrices()->delete();

                // Kembalikan poin loyalitas jika ada
                if ($booking->points_discount > 0) {
                    $this->loyaltyPointService->reverseRedemptionForBooking($booking);
                }
                
                $request->update([
                    'status' => 'approved',
                    'admin_notes' => $notes,
                    'processed_by_admin_id' => $adminId,
                    'processed_at' => now(),
                ]);
                return;
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
                'eligible_loyalty_amount' => $booking->total_amount + $request->price_difference,
                'subtotal' => $quote['subtotal'],
                'price_per_night_snapshot' => $quote['price_per_night'],
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

                // Generate snap token and update booking status to pending payment
                $this->paymentService->generateSnapTokenForPayment($payment, $booking, 'Selisih Perubahan Booking');

                $booking->update([
                    'status' => BookingStatus::PendingPayment->value,
                    'payment_status' => PaymentStatus::Pending->value,
                    'payment_expires_at' => now()->addHours(24),
                ]);
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
