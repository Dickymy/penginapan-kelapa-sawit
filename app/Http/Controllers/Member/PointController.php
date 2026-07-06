<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyPointService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointController extends Controller
{
    public function __construct(
        private readonly LoyaltyPointService $loyaltyService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $balance = $this->loyaltyService->getBalance($user);
        $pointValue = config('loyalty.point_value', 50);

        $transactions = LoyaltyTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('member.points.index', compact('balance', 'pointValue', 'transactions'));
    }
}
