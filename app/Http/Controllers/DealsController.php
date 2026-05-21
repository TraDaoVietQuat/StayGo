<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DealsController extends Controller
{
    public function subscribeNewsletter(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $email = strtolower(trim($request->email));

        $exists = DB::table('newsletter_subscriptions')->where('email', $email)->exists();
        if ($exists) {
            return response()->json(['message' => 'already_subscribed'], 200);
        }

        DB::table('newsletter_subscriptions')->insert([
            'email'         => $email,
            'subscribed_at' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

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
