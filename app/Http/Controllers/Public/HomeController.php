<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\PolicyVersion;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $roomTypes = RoomType::active()
            ->ordered()
            ->with(['images' => fn ($q) => $q->where('is_cover', true)->orWhere('sort_order', 0), 'facilities'])
            ->take(3)
            ->get();

        $reviews = \App\Models\Review::published()
            ->with(['user', 'booking.room.roomType'])
            ->where('rating', '>=', 4)
            ->inRandomOrder()
            ->take(3)
            ->get();

        $faqs = \App\Models\Faq::active()
            ->ordered()
            ->take(6)
            ->get();

        $cheapestPrice = RoomType::active()
            ->where('base_price', '>', 0)
            ->min('base_price');

        $activeRoomCount = Room::where('is_active', true)->count();

        $whatsapp = Setting::get('contact', 'whatsapp', '');
        $propertyName = Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit');
        $shortDescription = Setting::get('general', 'short_description', '');
        $address = Setting::get('contact', 'address', '');
        $mapLink = Setting::get('contact', 'map_link', '');
        $mapUrl = Setting::get('contact', 'map_url', '');
        $email = Setting::get('contact', 'email', '');

        // Policy summary for quick display
        $policy = PolicyVersion::current()->first();

        // Gallery preview (first 8 active photos)
        $galleryPhotos = Gallery::active()->ordered()->take(8)->get();

        $nearbyPlaces = \App\Models\NearbyPlace::active()->ordered()->take(4)->get();

        return view('public.home', compact(
            'roomTypes',
            'cheapestPrice',
            'activeRoomCount',
            'whatsapp',
            'propertyName',
            'shortDescription',
            'address',
            'mapLink',
            'mapUrl',
            'email',
            'policy',
            'galleryPhotos',
            'reviews',
            'faqs',
            'nearbyPlaces'
        ));
    }
}
