<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NearbyPlace;

class NearbyPlaceController extends Controller
{
    public function index()
    {
        $placesByCategory = NearbyPlace::active()->ordered()->get()->groupBy('category');
        return view('public.nearby-places', compact('placesByCategory'));
    }
}
