<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Location;
use App\Models\BlogPost;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $locations = Cache::remember('home.locations.v3', 3600, fn() =>
            Location::active()->withCount('hotels')->withAvg('hotels', 'rating')->get()
        );

        $weekendDeals = Cache::remember('home.weekend_deals', 1800, fn() =>
            Hotel::where('is_weekend_deal', true)
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->with('location')
                ->take(6)
                ->get()
        );

        $featuredHotels = Cache::remember('home.featured_hotels', 1800, fn() =>
            Hotel::where('is_active', true)
                ->orderByDesc('rating')
                ->with('location')
                ->take(3)
                ->get()
        );

        $featuredByLocation = Cache::remember('home.featured_by_location.v3', 1800, function () {
            return Location::active()->get()->map(function ($loc) {
                $hotels = Hotel::where('location_id', $loc->id)
                    ->where('is_active', true)
                    ->orderByDesc('rating')
                    ->with('location')
                    ->take(10)
                    ->get();
                return ['id' => $loc->id, 'name' => $loc->name, 'hotels' => $hotels];
            })->filter(fn($l) => $l['hotels']->isNotEmpty())->values();
        });

        $blogPosts = Cache::remember('home.blog_posts', 3600, fn() =>
            BlogPost::where('is_active', true)
                ->latest()
                ->take(4)
                ->get()
        );

        $homeReviews = Cache::remember('home.reviews', 1800, fn() =>
            Review::where('is_active', true)
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->where('rating', '>=', 8.0)
                ->where('created_at', '>=', now()->subMonths(6))
                ->with(['user', 'hotel'])
                ->orderByDesc('rating')
                ->take(4)
                ->get()
        );

        return view('pages.home', compact('locations', 'weekendDeals', 'featuredHotels', 'featuredByLocation', 'blogPosts', 'homeReviews'));
    }
}
