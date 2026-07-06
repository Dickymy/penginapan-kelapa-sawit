<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RefundStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function create(Booking $booking): View
    {
        $payment = $booking->payments()->where('status', 'paid')->first();
        if (!$payment) abort(422, 'Booking belum memiliki pembayaran.');
        $existingRefunds = Refund::where('payment_id', $payment->id)->where('status', '!=', 'cancelled')->sum('amount');
        $maxRefundable = $payment->gross_amount - $existingRefunds;
        return view('admin.refunds.create', compact('booking', 'payment', 'maxRefundable'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $payment = $booking->payments()->where('status', 'paid')->firstOrFail();

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $existingRefunds = Refund::where('payment_id', $payment->id)->where('status', '!=', 'cancelled')->sum('amount');
        $maxRefundable = $payment->gross_amount - $existingRefunds;

        if ($validated['amount'] > $maxRefundable) {
            return back()->with('error', 'Jumlah refund melebihi batas maksimal.');
        }

        $refund = Refund::create([
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'requested_by_admin_id' => Auth::guard('admin')->id(),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'status' => RefundStatus::Requested->value,
            'requested_at' => now(),
        ]);

        AuditLog::record('refund_requested', 'admin', Auth::guard('admin')->id(), 'booking', $booking->id, null, ['refund_id' => $refund->id, 'amount' => $refund->amount]);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Refund berhasil dicatat.');
    }
}
