<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'booking.room.roomType'])->latest();

        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'pending') {
                $query->where('is_published', false);
            }
        }

        $reviews = $query->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function publish(Review $review)
    {
        $review->update([
            'is_published' => !$review->is_published
        ]);

        $status = $review->is_published ? 'dipublikasikan' : 'disembunyikan';
        return back()->with('success', "Ulasan berhasil {$status}.");
    }

    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000'
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply,
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Balasan berhasil disimpan.');
    }
}
