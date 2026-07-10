<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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
            ->with('images')
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
            'policy'
        ));
    }
}
