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
        if ($booking->user_id !== auth()->id() || $booking->status !== BookingStatus::Confirmed->value) {
            abort(403, 'Anda tidak diizinkan mengubah booking ini.');
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
}
