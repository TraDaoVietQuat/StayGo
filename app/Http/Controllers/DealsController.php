<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class DealsController extends Controller
{
    public function index()
    {
        $weekendDeals = Cache::remember('deals.weekend_deals', 1800, fn() =>
            Hotel::where('is_weekend_deal', true)
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->with('location')
                ->take(6)
                ->get()
        );

        $locations = Cache::remember('all.locations.with_count.v2', 3600, fn() =>
            Location::withCount('hotels')->get()
        );

        return view('pages.deals', compact('weekendDeals', 'locations'));
    }
}
