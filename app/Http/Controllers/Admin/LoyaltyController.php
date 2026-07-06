<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function __construct(
        private readonly LoyaltyPointService $loyaltyService,
    ) {}

    public function index(): View
    {
        $users = User::where('loyalty_balance_cache', '>', 0)
            ->orderByDesc('loyalty_balance_cache')
            ->paginate(20);

        return view('admin.loyalty.index', compact('users'));
    }

    public function show(User $user): View
    {
        $transactions = LoyaltyTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        $balance = $this->loyaltyService->getBalance($user);

        return view('admin.loyalty.show', compact('user', 'transactions', 'balance'));
    }

    public function adjust(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        $this->loyaltyService->adjustPoints(
            $user,
            (int) $validated['points'],
            $validated['reason'],
            $request->user('admin'),
        );

        return redirect()
            ->route('admin.loyalty.show', $user)
            ->with('success', 'Poin berhasil disesuaikan.');
    }
}
