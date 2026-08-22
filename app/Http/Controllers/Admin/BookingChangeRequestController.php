<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingChangeRequest;
use App\Services\BookingChangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingChangeResultMail;

class BookingChangeRequestController extends Controller
{
    public function __construct(
        private BookingChangeService $bookingChangeService
    ) {}

    public function index()
    {
        $requests = BookingChangeRequest::with(['booking', 'user'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);
            
        return view('admin.booking-changes.index', compact('requests'));
    }

    public function show(BookingChangeRequest $bookingChangeRequest)
    {
        $bookingChangeRequest->load(['booking.room.roomType', 'user', 'processedByAdmin']);
        return view('admin.booking-changes.show', compact('bookingChangeRequest'));
    }

    public function approve(Request $request, BookingChangeRequest $bookingChangeRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $this->bookingChangeService->approveRequest($bookingChangeRequest, auth()->id(), $request->notes);
            
            Mail::to($bookingChangeRequest->user->email)->send(new BookingChangeResultMail($bookingChangeRequest));

            return back()->with('success', 'Pengajuan perubahan berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, BookingChangeRequest $bookingChangeRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $this->bookingChangeService->rejectRequest($bookingChangeRequest, auth()->id(), $request->notes);
            
            Mail::to($bookingChangeRequest->user->email)->send(new BookingChangeResultMail($bookingChangeRequest));

            return back()->with('success', 'Pengajuan perubahan berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }
}
