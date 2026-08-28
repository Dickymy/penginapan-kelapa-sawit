<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['room.roomType'])->latest();

        $this->applyFilters($query, $request);

        $bookings = $query->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function export(Request $request)
    {
        $query = Booking::with(['room.roomType'])->latest();

        $this->applyFilters($query, $request);

        $bookings = $query->get();

        $filename = "Daftar_Booking_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($bookings) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Kode Booking', 'Tamu', 'Kamar', 'Check-In', 'Check-Out', 'Malam', 'Status', 'Pembayaran', 'Sumber', 'Total']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_code,
                    $booking->guest_name,
                    $booking->room_type_name_snapshot . ' - ' . $booking->room_name_snapshot,
                    $booking->check_in->format('d/m/Y'),
                    $booking->check_out->format('d/m/Y'),
                    $booking->nights,
                    $booking->status->label(),
                    $booking->payment_status->label(),
                    $booking->source->label(),
                    $booking->total_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(): View
    {
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('sort_order')->get();
        $sources = BookingSource::cases();
        $roomsJson = $rooms->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'type_name' => $r->roomType->name,
            'base_price' => $r->roomType->base_price,
            'capacity' => $r->roomType->capacity,
        ])->values();
        return view('admin.bookings.create', compact('rooms', 'sources', 'roomsJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'guest_name' => ['required', 'string', 'max:150'],
            'guest_email' => ['nullable', 'email'],
            'guest_whatsapp' => ['required', 'string', 'max:32'],
            'source' => ['required', 'string', \Illuminate\Validation\Rule::in(array_column(BookingSource::cases(), 'value'))],
            'price_per_night' => ['required', 'integer', 'min:0'],
            'payment_status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'internal_notes' => ['nullable', 'string'],
            'hold_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ]);

        $admin = Auth::guard('admin')->user();
        $booking = $this->bookingService->createManualBooking($validated, $admin);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking berhasil dibuat.');
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.roomType', 'payments', 'statusHistories', 'nightPrices', 'addons.addon']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:255']);

        DB::transaction(function () use ($booking, $request) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            if (!$booking->status->canTransitionTo(BookingStatus::Cancelled)) {
                abort(422, 'Booking tidak dapat dibatalkan dari status saat ini.');
            }

            $fromStatus = $booking->status->value;

            $booking->update([
                'status' => BookingStatus::Cancelled->value,
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
                'cancelled_by_admin_id' => Auth::guard('admin')->id(),
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => $fromStatus,
                'to_status' => BookingStatus::Cancelled->value,
                'reason' => $request->reason,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);

            AuditLog::record(
                action: 'booking_cancelled',
                actorType: 'admin',
                actorId: Auth::guard('admin')->id(),
                subjectType: 'booking',
                subjectId: $booking->id,
                before: ['status' => $fromStatus],
                after: ['status' => BookingStatus::Cancelled->value],
                metadata: ['reason' => $request->reason],
            );

            // Release promotion and reverse loyalty inside the same transaction
            app(\App\Services\PromotionService::class)->releaseForBooking($booking);
            app(\App\Services\LoyaltyPointService::class)->reverseRedemptionForBooking($booking);
        });

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            if ($booking->status !== BookingStatus::Confirmed) {
                abort(422, 'Booking harus dikonfirmasi sebelum check-in.');
            }
            $booking->update(['status' => BookingStatus::CheckedIn->value, 'checked_in_at' => now()]);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => BookingStatus::Confirmed->value,
                'to_status' => BookingStatus::CheckedIn->value,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);
            AuditLog::record(
                action: 'booking_checked_in',
                actorType: 'admin',
                actorId: Auth::guard('admin')->id(),
                subjectType: 'booking',
                subjectId: $booking->id,
                before: ['status' => BookingStatus::Confirmed->value],
                after: ['status' => BookingStatus::CheckedIn->value],
            );
        });
        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            if ($booking->status !== BookingStatus::CheckedIn) {
                abort(422, 'Tamu harus check-in terlebih dahulu.');
            }
            $booking->update(['status' => BookingStatus::CheckedOut->value, 'checked_out_at' => now()]);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => BookingStatus::CheckedIn->value,
                'to_status' => BookingStatus::CheckedOut->value,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);
            AuditLog::record(
                action: 'booking_checked_out',
                actorType: 'admin',
                actorId: Auth::guard('admin')->id(),
                subjectType: 'booking',
                subjectId: $booking->id,
                before: ['status' => BookingStatus::CheckedIn->value],
                after: ['status' => BookingStatus::CheckedOut->value],
            );
        });
        return back()->with('success', 'Check-out berhasil.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            if ($booking->status !== BookingStatus::CheckedOut) {
                abort(422, 'Booking harus check-out terlebih dahulu.');
            }
            $booking->update(['status' => BookingStatus::Completed->value, 'completed_at' => now()]);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => BookingStatus::CheckedOut->value,
                'to_status' => BookingStatus::Completed->value,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);
            AuditLog::record(
                action: 'booking_completed',
                actorType: 'admin',
                actorId: Auth::guard('admin')->id(),
                subjectType: 'booking',
                subjectId: $booking->id,
                before: ['status' => BookingStatus::CheckedOut->value],
                after: ['status' => BookingStatus::Completed->value],
            );
        });

        $booking->refresh();
        app(\App\Services\LoyaltyPointService::class)->awardForCompletedBooking($booking);

        return back()->with('success', 'Booking selesai.');
    }

    public function noShow(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            if ($booking->status !== BookingStatus::Confirmed) {
                abort(422, 'Hanya booking confirmed yang dapat ditandai no-show.');
            }
            $booking->update(['status' => BookingStatus::NoShow->value]);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => BookingStatus::Confirmed->value,
                'to_status' => BookingStatus::NoShow->value,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);
            AuditLog::record(
                action: 'booking_no_show',
                actorType: 'admin',
                actorId: Auth::guard('admin')->id(),
                subjectType: 'booking',
                subjectId: $booking->id,
                before: ['status' => BookingStatus::Confirmed->value],
                after: ['status' => BookingStatus::NoShow->value],
            );
        });
        return back()->with('success', 'Booking ditandai no-show.');
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('check_in')) {
            $query->where('check_in', $request->check_in);
        }
        if ($request->filled('search')) {
            // Escape special characters for LIKE clause
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_whatsapp', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }
    }
}
