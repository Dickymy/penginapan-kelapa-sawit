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
use App\Http\Controllers\Public\RoomController as PublicRoomController;
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
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{bookingCode}/konfirmasi', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/cek-booking', [BookingController::class, 'verifyForm'])->name('booking.verify.form');
Route::post('/cek-booking', [BookingController::class, 'verifyAccess'])->name('booking.verify');

/*
|--------------------------------------------------------------------------
| Member Routes (authenticated + verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
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
        Route::post('/login', [AdminLoginController::class, 'login']);
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
    });
});
