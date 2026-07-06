<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PolicyVersion;
use App\Models\Setting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $content = Setting::get('general', 'about_content', '');
        $propertyName = Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit');

        return view('public.about', compact('content', 'propertyName'));
    }

    public function location(): View
    {
        $address = Setting::get('contact', 'address', '');
        $mapUrl = Setting::get('contact', 'map_url', '');
        $whatsapp = Setting::get('contact', 'whatsapp', '');
        $email = Setting::get('contact', 'email', '');

        return view('public.location', compact('address', 'mapUrl', 'whatsapp', 'email'));
    }

    public function policy(): View
    {
        $policy = PolicyVersion::current()->first();

        return view('public.policy', compact('policy'));
    }
}
