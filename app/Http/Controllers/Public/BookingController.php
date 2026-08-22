<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\BookingAccessService;
use App\Services\BookingService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availability,
        private PricingService $pricing,
        private BookingAccessService $accessService,
    ) {}

    /**
     * Show checkout form with recheck and quote.
     */
    public function showCheckout(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guest_count' => ['required', 'integer', 'min:1'],
        ]);

        $roomType = RoomType::findOrFail($validated['room_type_id']);
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $guestCount = (int) $validated['guest_count'];

        // Recheck availability
        $availableRooms = $this->availability->findAvailableRooms($roomType->id, $checkIn, $checkOut);

        if ($availableRooms->isEmpty()) {
            return redirect()->route('availability.search', $validated)
                ->with('error', 'Maaf, kamar tidak tersedia lagi untuk tanggal tersebut.');
        }

        $quote = $this->pricing->calculateQuote($roomType, $checkIn, $checkOut);

        // Generate idempotency key for this session
        $idempotencyKey = $request->session()->get('booking_idempotency_key')
            ?: Str::uuid()->toString();
        $request->session()->put('booking_idempotency_key', $idempotencyKey);

        $user = auth()->user();
        $addons = \App\Models\Addon::active()->ordered()->get();

        return view('public.booking.checkout', compact(
            'roomType', 'checkIn', 'checkOut', 'guestCount', 'quote', 'idempotencyKey', 'user', 'addons'
        ));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'guest_name' => ['required', 'string', 'max:150'],
            'guest_email' => ['required', 'email', 'max:191'],
            'guest_whatsapp' => ['required', 'string', 'max:32'],
            'arrival_estimate' => ['nullable', 'string', \Illuminate\Validation\Rule::in(\App\Support\ArrivalTimeSlots::validValues())],
            'special_request' => ['nullable', 'string', 'max:1000'],
            'policy_accepted' => ['accepted'],
            'idempotency_key' => ['required', 'string'],
            'addons' => ['nullable', 'array'],
            'addons.*.addon_id' => ['required_with:addons', 'exists:addons,id'],
            'addons.*.quantity' => ['required_with:addons', 'integer', 'min:1'],
            'use_points' => ['nullable', 'boolean'],
        ]);

        try {
            $user = auth()->user();

            if ($user) {
                $result = $this->bookingService->createMemberBooking($validated, $user);
            } else {
                $result = $this->bookingService->createGuestBooking($validated);
            }

            $booking = $result['booking'];
            $rawToken = $result['raw_token'];

            // Clear idempotency key from session
            $request->session()->forget('booking_idempotency_key');

            // Grant session access to the booking for confirmation/payment
            $this->accessService->grantCreationAccess($request, $booking);

            // Store token temporarily in session for confirmation page
            if ($rawToken) {
                $request->session()->flash('booking_raw_token', $rawToken);
            }

            return redirect()->route('booking.confirmation', $booking->booking_code);

        } catch (RoomNotAvailableException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show booking confirmation page.
     * After booking is created, guest is shown a success page with booking details.
     */
    public function confirmation(Request $request, string $bookingCode): View
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        // Verify access (session grant from just-created or verified)
        if (!$this->accessService->hasAccess($request, $booking)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $rawToken = $request->session()->get('booking_raw_token', '');
        $booking->load(['room.roomType', 'statusHistories', 'nightPrices', 'addons.addon']);

        return view('public.booking.confirmation', compact('booking', 'rawToken'));
    }

    /**
     * Smart "Booking Saya" route.
     * - Guest: show search form
     * - Member: redirect to member bookings
     */
    public function myBooking(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('member.bookings.index');
        }

        return view('public.booking.my-booking');
    }

    /**
     * Show form to verify booking access (legacy route kept for compatibility).
     */
    public function verifyForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('member.bookings.index');
        }

        return view('public.booking.my-booking');
    }

    /**
     * Verify and show booking status.
     * Simplified: only booking_code + guest_whatsapp required for guest.
     * Also supports token-based access via query param.
     */
    public function verifyAccess(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'booking_code' => ['required', 'string', 'max:50'],
            'guest_whatsapp' => ['required', 'string', 'max:32'],
        ]);

        $booking = Booking::where('booking_code', trim($validated['booking_code']))->first();

        if (! $booking) {
            return back()
                ->withInput()
                ->with('error', 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp Anda.');
        }

        // Verify via WhatsApp (with normalization)
        $verified = $this->accessService->verifyByWhatsApp($booking, $validated['guest_whatsapp']);

        if ($verified) {
            // Grant session access for subsequent pages (payment, invoice)
            $this->accessService->grantAccess($request, $booking);
            return redirect()->route('booking.guest.detail', $booking->booking_code);
        }

        // Generic error - don't reveal which field was wrong
        return back()
            ->withInput()
            ->with('error', 'Booking tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp Anda.');
    }

    /**
     * Guest booking detail page (requires session access or token).
     */
    public function guestDetail(Request $request, string $bookingCode): View|RedirectResponse
    {
        $booking = Booking::where('booking_code', $bookingCode)->first();

        if (! $booking) {
            return redirect()->route('booking.my')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Check token-based access from URL query param
        if ($request->has('access') && !$this->accessService->hasAccess($request, $booking)) {
            $tokenVerified = $this->accessService->verifyByToken($booking, $request->query('access'));
            if ($tokenVerified) {
                $this->accessService->grantAccess($request, $booking);
            }
        }

        // Verify access (session grant from verification/creation or token)
        if (!$this->accessService->hasAccess($request, $booking)) {
            return redirect()->route('booking.my')
                ->with('error', 'Anda tidak memiliki akses ke booking ini. Silakan verifikasi terlebih dahulu.');
        }

        $booking->load(['room.roomType', 'statusHistories', 'nightPrices', 'addons.addon']);

        return view('public.booking.detail', compact('booking'));
    }
}
