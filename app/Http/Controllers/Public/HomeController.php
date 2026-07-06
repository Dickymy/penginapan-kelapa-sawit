<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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

        $whatsapp = Setting::get('contact', 'whatsapp', '');
        $propertyName = Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit');
        $shortDescription = Setting::get('general', 'short_description', '');

        return view('public.home', compact(
            'roomTypes',
            'cheapestPrice',
            'whatsapp',
            'propertyName',
            'shortDescription'
        ));
    }
}
