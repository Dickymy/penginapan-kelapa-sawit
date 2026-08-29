<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreBookingChangeRequest;
use App\Models\Booking;
use App\Models\RoomType;
use App\Services\BookingChangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingChangeRequestMail;
use App\Enums\BookingStatus;

class BookingChangeRequestController extends Controller
{
    public function __construct(
        private BookingChangeService $bookingChangeService
    ) {}

    public function create(Booking $booking, Request $request)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== BookingStatus::Confirmed) {
            abort(403, 'Anda tidak diizinkan mengubah booking ini.');
        }

        if ($booking->changeRequests()->where('status', 'pending')->exists()) {
            return redirect()->route('member.bookings.show', $booking)
                ->with('error', 'Anda sudah memiliki pengajuan perubahan yang sedang diproses. Harap tunggu konfirmasi admin terlebih dahulu.');
        }

        // Jika form di-submit untuk "Preview" menggunakan GET (opsional),
        // atau kita pakai JavaScript. Untuk sederhana, kita kirim data RoomTypes saja.
        $roomTypes = RoomType::active()->get();
        
        $preview = null;
        if ($request->has('check_in') && $request->has('check_out')) {
            $preview = $this->bookingChangeService->previewChange($booking, $request->all());
        }

        return view('member.bookings.change', compact('booking', 'roomTypes', 'preview'));
    }

    public function store(StoreBookingChangeRequest $request, Booking $booking)
    {
        try {
            $changeRequest = $this->bookingChangeService->submitRequest(
                $booking,
                $request->validated(),
                $request->type,
                auth()->id()
            );

            // Kirim email notifikasi ke admin
            // Kita kirim ke config mailto
            Mail::to(config('mail.from.address'))->send(new BookingChangeRequestMail($changeRequest));

            return redirect()->route('member.bookings.show', $booking)
                ->with('success', 'Pengajuan perubahan berhasil dikirim. Silakan tunggu konfirmasi dari Admin.');
        } catch (\App\Exceptions\RoomNotAvailableException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function calculateChange(Request $request, Booking $booking)
    {
        try {
            $preview = $this->bookingChangeService->previewChange($booking, $request->all());
            return response()->json($preview);
        } catch (\Exception $e) {
            return response()->json([
                'is_available' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function createCancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== BookingStatus::Confirmed) {
            abort(403, 'Anda tidak diizinkan membatalkan booking ini.');
        }

        if ($booking->changeRequests()->where('status', 'pending')->exists()) {
            return redirect()->route('member.bookings.show', $booking)
                ->with('error', 'Anda sudah memiliki pengajuan yang sedang diproses. Harap tunggu konfirmasi admin terlebih dahulu.');
        }

        try {
            $preview = $this->bookingChangeService->previewCancellation($booking);
        } catch (\Exception $e) {
            return redirect()->route('member.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }

        return view('member.bookings.cancel', compact('booking', 'preview'));
    }

    public function storeCancel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        try {
            $changeRequest = $this->bookingChangeService->submitCancellation(
                $booking,
                $validated,
                auth()->id()
            );

            // Kirim email notifikasi ke admin
            Mail::to(config('mail.from.address'))->send(new BookingChangeRequestMail($changeRequest));

            return redirect()->route('member.bookings.show', $booking)
                ->with('success', 'Pengajuan pembatalan berhasil dikirim. Admin akan segera memproses pengembalian dana Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
