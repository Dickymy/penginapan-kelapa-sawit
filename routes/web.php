<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\PolicyVersionController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\RoomImageController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Public\AvailabilityController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PaymentController;
use App\Http\Controllers\Public\RoomController as PublicRoomController;
use App\Http\Controllers\Webhook\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kamar', [PublicRoomController::class, 'index'])->name('rooms.index');
Route::get('/kamar/{slug}', [PublicRoomController::class, 'show'])->name('rooms.show');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/lokasi', [PageController::class, 'location'])->name('location');
Route::get('/kebijakan', [PageController::class, 'policy'])->name('policy');

// Availability & Booking
Route::get('/ketersediaan', [AvailabilityController::class, 'search'])->name('availability.search');
Route::get('/checkout', [BookingController::class, 'showCheckout'])->name('booking.checkout');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:booking-store');
Route::get('/booking/{bookingCode}/konfirmasi', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/cek-booking', [BookingController::class, 'verifyForm'])->name('booking.verify.form');
Route::post('/cek-booking', [BookingController::class, 'verifyAccess'])->name('booking.verify')->middleware('throttle:booking-verify');

// Payment
Route::get('/booking/{bookingCode}/bayar', [PaymentController::class, 'pay'])->name('booking.pay')->middleware('throttle:payment-initiate');
Route::get('/booking/{bookingCode}/selesai', [PaymentController::class, 'finish'])->name('booking.finish');

// Invoice
Route::get('/booking/{bookingCode}/invoice', [\App\Http\Controllers\Public\InvoiceController::class, 'download'])->name('booking.invoice');

// Google OAuth
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');

// Webhook (no CSRF)
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhook.midtrans')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Member Routes (authenticated + verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [\App\Http\Controllers\Member\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Member\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/profile', [\App\Http\Controllers\Member\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Member\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/whatsapp', [\App\Http\Controllers\Member\ProfileController::class, 'updateWhatsapp'])->name('profile.update-whatsapp');
    Route::get('/claim', [\App\Http\Controllers\Member\ClaimController::class, 'index'])->name('claim.index');
    Route::post('/claim/{booking}', [\App\Http\Controllers\Member\ClaimController::class, 'claim'])->name('claim.claim');
    Route::get('/points', [\App\Http\Controllers\Member\PointController::class, 'index'])->name('points.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest admin routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:admin-login');
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Room Types
        Route::resource('room-types', RoomTypeController::class)->except(['show', 'destroy']);
        Route::patch('room-types/{room_type}/toggle', [RoomTypeController::class, 'toggleActive'])->name('room-types.toggle');

        // Room Images
        Route::post('room-types/{room_type}/images', [RoomImageController::class, 'store'])->name('room-images.store');
        Route::patch('room-images/{image}/cover', [RoomImageController::class, 'setCover'])->name('room-images.cover');
        Route::delete('room-images/{image}', [RoomImageController::class, 'destroy'])->name('room-images.destroy');

        // Rooms
        Route::resource('rooms', AdminRoomController::class)->except(['show', 'destroy']);
        Route::patch('rooms/{room}/toggle', [AdminRoomController::class, 'toggleActive'])->name('rooms.toggle');

        // Facilities
        Route::resource('facilities', FacilityController::class)->except(['show']);

        // Settings
        Route::get('settings/{group}', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings/{group}', [SettingsController::class, 'update'])->name('settings.update');

        // Policy Versions
        Route::resource('policies', PolicyVersionController::class)->except(['edit', 'update', 'destroy']);
        Route::patch('policies/{policy}/publish', [PolicyVersionController::class, 'publish'])->name('policies.publish');

        // Gallery
        Route::get('galleries', [AdminGalleryController::class, 'index'])->name('galleries.index');
        Route::post('galleries', [AdminGalleryController::class, 'store'])->name('galleries.store');
        Route::patch('galleries/{gallery}/toggle', [AdminGalleryController::class, 'toggleActive'])->name('galleries.toggle');
        Route::delete('galleries/{gallery}', [AdminGalleryController::class, 'destroy'])->name('galleries.destroy');

        // Bookings
        Route::get('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/create', [\App\Http\Controllers\Admin\BookingController::class, 'create'])->name('bookings.create');
        Route::post('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'store'])->name('bookings.store');
        Route::get('bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/cancel', [\App\Http\Controllers\Admin\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::patch('bookings/{booking}/check-in', [\App\Http\Controllers\Admin\BookingController::class, 'checkIn'])->name('bookings.check-in');
        Route::patch('bookings/{booking}/check-out', [\App\Http\Controllers\Admin\BookingController::class, 'checkOut'])->name('bookings.check-out');
        Route::patch('bookings/{booking}/complete', [\App\Http\Controllers\Admin\BookingController::class, 'complete'])->name('bookings.complete');
        Route::patch('bookings/{booking}/no-show', [\App\Http\Controllers\Admin\BookingController::class, 'noShow'])->name('bookings.no-show');

        // Room Blocks
        Route::get('room-blocks', [\App\Http\Controllers\Admin\RoomBlockController::class, 'index'])->name('room-blocks.index');
        Route::get('room-blocks/create', [\App\Http\Controllers\Admin\RoomBlockController::class, 'create'])->name('room-blocks.create');
        Route::post('room-blocks', [\App\Http\Controllers\Admin\RoomBlockController::class, 'store'])->name('room-blocks.store');
        Route::delete('room-blocks/{roomBlock}', [\App\Http\Controllers\Admin\RoomBlockController::class, 'destroy'])->name('room-blocks.destroy');

        // Loyalty
        Route::get('loyalty', [\App\Http\Controllers\Admin\LoyaltyController::class, 'index'])->name('loyalty.index');
        Route::get('loyalty/{user}', [\App\Http\Controllers\Admin\LoyaltyController::class, 'show'])->name('loyalty.show');
        Route::post('loyalty/{user}/adjust', [\App\Http\Controllers\Admin\LoyaltyController::class, 'adjust'])->name('loyalty.adjust');

        // Promotions
        Route::resource('promotions', \App\Http\Controllers\Admin\PromotionController::class)->except(['show']);

        // Refunds
        Route::get('bookings/{booking}/refund', [\App\Http\Controllers\Admin\RefundController::class, 'create'])->name('refunds.create');
        Route::post('bookings/{booking}/refund', [\App\Http\Controllers\Admin\RefundController::class, 'store'])->name('refunds.store');

        // Expenses
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class)->except(['show']);

        // Reports
        Route::get('reports/revenue', [\App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/occupancy', [\App\Http\Controllers\Admin\ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('reports/profit', [\App\Http\Controllers\Admin\ReportController::class, 'profit'])->name('reports.profit');
        Route::get('reports/sources', [\App\Http\Controllers\Admin\ReportController::class, 'sources'])->name('reports.sources');
    });
});
