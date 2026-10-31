<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromotionRequest;
use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::orderByDesc('created_at')->paginate(20);
        $showPromoListing = (bool) Setting::get('booking', 'show_promo_listing', true);

        return view('admin.promotions.index', compact('promotions', 'showPromoListing'));
    }

    public function toggleCheckoutListing(Request $request): RedirectResponse
    {
        $enabled = $request->boolean('show_promo_listing');
        Setting::set('booking', 'show_promo_listing', $enabled, auth('admin')->id(), 'boolean', true);

        $message = $enabled
            ? 'Daftar "Promo Menarik Tersedia" pada checkout berhasil diaktifkan.'
            : 'Daftar "Promo Menarik Tersedia" pada checkout berhasil disembunyikan. Tamu tetap dapat menggunakan promo dengan mengetik kode promo yang benar secara manual.';

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', $message);
    }

    public function create(): View
    {
        return view('admin.promotions.create');
    }

    public function store(StorePromotionRequest $request): RedirectResponse
    {
        Promotion::create($request->validated());

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promo berhasil dibuat.');
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(StorePromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($request->validated());

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->update(['is_active' => false]);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promo berhasil dinonaktifkan.');
    }
}
