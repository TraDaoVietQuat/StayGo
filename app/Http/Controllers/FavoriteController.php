<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** Toggle yêu thích (AJAX hoặc redirect) */
    public function toggle(Hotel $hotel)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $exists = $user->favorites()->where('hotel_id', $hotel->id)->exists();

        if ($exists) {
            $user->favorites()->where('hotel_id', $hotel->id)->delete();
            $favorited = false;
        } else {
            $user->favorites()->create(['hotel_id' => $hotel->id]);
            $favorited = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('success', $favorited ? 'Đã thêm vào yêu thích.' : 'Đã xóa khỏi yêu thích.');
    }

    /** Danh sách khách sạn yêu thích */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $hotels = $user->favoriteHotels()
            ->with('location')
            ->where('is_active', true)
            ->paginate(12);

        return view('pages.my-favorites', compact('hotels'));
    }
}
