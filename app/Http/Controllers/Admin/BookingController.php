<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $bookings = $query->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('sort_order')->get();
        $sources = BookingSource::cases();
        return view('admin.bookings.create', compact('rooms', 'sources'));
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
            'source' => ['required', 'string'],
            'price_per_night' => ['required', 'integer', 'min:0'],
            'payment_status' => ['required', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $admin = Auth::guard('admin')->user();
        $booking = $this->bookingService->createManualBooking($validated, $admin);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking berhasil dibuat.');
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.roomType', 'payments', 'statusHistories']);
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
            $booking->update([
                'status' => BookingStatus::Cancelled->value,
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
                'cancelled_by_admin_id' => Auth::guard('admin')->id(),
            ]);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => $booking->getOriginal('status'),
                'to_status' => BookingStatus::Cancelled->value,
                'reason' => $request->reason,
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'created_at' => now(),
            ]);
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
        });
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
        });
        return back()->with('success', 'Booking ditandai no-show.');
    }
}
