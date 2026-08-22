<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Enums\BookingStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Mail\NewReviewNotificationMail;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function create(Booking $booking)
    {
        // Pastikan booking milik user dan statusnya valid
        if ($booking->user_id !== auth()->id() || !in_array($booking->status, [BookingStatus::CheckedOut, BookingStatus::Completed])) {
            abort(403, 'Anda tidak diizinkan mereview pemesanan ini.');
        }

        // Cek apakah sudah pernah direview
        if ($booking->review()->exists()) {
            return redirect()->route('member.dashboard')->with('error', 'Pemesanan ini sudah pernah diulas.');
        }

        return view('member.reviews.create', compact('booking'));
    }

    public function store(StoreReviewRequest $request)
    {
        $review = Review::create([
            'user_id' => auth()->id(),
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_published' => false,
        ]);

        // Kirim notifikasi ke admin
        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new NewReviewNotificationMail($review));
        }

        return redirect()->route('member.dashboard')->with('success', 'Terima kasih! Ulasan Anda telah berhasil dikirim dan menunggu moderasi.');
    }
}
