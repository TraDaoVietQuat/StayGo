<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        // Sanitize & whitelist filter inputs
        $allowedTypes = ['hotel', 'resort', 'homestay-resort'];
        $allowedSorts = ['rating', 'price_asc', 'price_desc', 'popular'];

        $type     = in_array($request->input('type'), $allowedTypes) ? $request->input('type') : null;
        $sort     = in_array($request->input('sort'), $allowedSorts) ? $request->input('sort') : 'rating';
        $minPrice = is_numeric($request->input('min_price')) ? (int) $request->input('min_price') : null;
        $maxPrice = is_numeric($request->input('max_price')) ? (int) $request->input('max_price') : null;
        $rating   = is_numeric($request->input('rating')) && $request->input('rating') >= 1 && $request->input('rating') <= 5
                    ? (float) $request->input('rating') : null;
        $location = is_numeric($request->input('location')) ? (int) $request->input('location') : null;
        $keyword  = $request->filled('keyword') ? substr(strip_tags($request->input('keyword')), 0, 100) : null;
        $checkin  = $request->filled('checkin') && strtotime($request->input('checkin')) ? $request->input('checkin') : null;
        $checkout = $request->filled('checkout') && strtotime($request->input('checkout')) ? $request->input('checkout') : null;

        $query = Hotel::where('is_active', true)->with('location');

        // Lọc theo loại hình (hotel / homestay / resort)
        if ($type) {
            if ($type === 'homestay-resort') {
                $query->whereIn('type', ['homestay', 'resort']);
            } else {
                $query->where('type', $type);
            }
        }

        if ($keyword) {
            $kw = $keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('address', 'like', "%{$kw}%")
                  ->orWhereHas('location', fn($l) => $l->where('name', 'like', "%{$kw}%"));
            });
        }

        if ($location) {
            $query->where('location_id', $location);
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($rating !== null) {
            $query->where('rating', '>=', $rating);
        }

        // Lọc theo ngày trống
        if ($checkin && $checkout && $checkin < $checkout) {
            $ci = $checkin;
            $co = $checkout;
            $query->whereHas('rooms', function ($q) use ($ci, $co) {
                $q->whereRaw(
                    'rooms.quantity > (
                        SELECT COUNT(*) FROM bookings
                        WHERE bookings.room_id = rooms.id
                          AND bookings.status IN (\'pending\',\'confirmed\')
                          AND bookings.check_in  < ?
                          AND bookings.check_out > ?
                    )',
                    [$co, $ci]
                );
            });
        }

        // Lọc theo tiện ích
        if ($request->filled('amenity')) {
            $allowed = ['wifi','parking','pool','gym','restaurant','spa','bar','ac','breakfast'];
            foreach (array_intersect((array) $request->amenity, $allowed) as $am) {
                $query->whereJsonContains('amenities', $am);
            }
        }

        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular'    => $query->orderByDesc('review_count'),
            default      => $query->orderByDesc('rating'),
        };

        $hotels    = $query->paginate(12)->withQueryString();
        $locations = Cache::remember('all.locations.with_count.v2', 3600, fn() => Location::withCount('hotels')->withAvg('hotels', 'rating')->get());

        // Truyền search params để view pre-fill
        $guests   = is_numeric($request->input('guests')) ? max(1, min(20, (int) $request->input('guests'))) : 1;
        $stayType = in_array($request->input('stay_type'), ['night', 'oneday']) ? $request->input('stay_type') : 'night';

        // IDs khách sạn yêu thích của user (để hiển thị nút tim)
        $favHotelIds = Auth::check()
            ? Auth::user()->favorites()->pluck('hotel_id')->toArray()
            : [];

        return view('pages.hotels', compact('hotels', 'locations', 'checkin', 'checkout', 'guests', 'stayType', 'favHotelIds'));
    }

    public function show(Hotel $hotel, Request $request)
    {
        abort_if(!$hotel->is_active, 404);

        $hotel->load(['location', 'rooms', 'images', 'reviews.user']);

        $checkin   = $request->input('checkin');
        $checkout  = $request->input('checkout');
        $guests    = (int) $request->input('guests', 1);
        $stayType  = $request->input('stay_type', 'night');

        // Tính phòng trống — batch 1 query; dùng hôm nay làm mặc định khi không có ngày
        $ciForAvail = ($checkin && $checkout && $checkin < $checkout) ? $checkin : now()->toDateString();
        $coForAvail = ($checkin && $checkout && $checkin < $checkout) ? $checkout : now()->addDay()->toDateString();

        $bookedCounts = Booking::whereIn('room_id', $hotel->rooms->pluck('id'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $coForAvail)
            ->where('check_out', '>', $ciForAvail)
            ->selectRaw('room_id, COUNT(*) as booked')
            ->groupBy('room_id')
            ->pluck('booked', 'room_id');

        $rooms = $hotel->rooms->map(function ($room) use ($bookedCounts) {
            $room->available_count = max(0, ($room->quantity ?? 1) - $bookedCounts->get($room->id, 0));
            return $room;
        });

        $relatedHotels = Cache::remember("hotel.related.{$hotel->id}", 1800, fn() =>
            Hotel::where('location_id', $hotel->location_id)
                ->where('id', '!=', $hotel->id)
                ->where('is_active', true)
                ->take(3)
                ->get()
        );

        return view('pages.hotel-detail', compact(
            'hotel', 'rooms', 'relatedHotels', 'checkin', 'checkout', 'guests', 'stayType'
        ));
    }

    public function availability(Hotel $hotel, Request $request)
    {
        $checkin  = $request->query('checkin');
        $checkout = $request->query('checkout');

        $ciForAvail = ($checkin && $checkout && $checkin < $checkout) ? $checkin : now()->toDateString();
        $coForAvail = ($checkin && $checkout && $checkin < $checkout) ? $checkout : now()->addDay()->toDateString();

        $bookedCounts = Booking::whereIn('room_id', $hotel->rooms->pluck('id'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $coForAvail)
            ->where('check_out', '>', $ciForAvail)
            ->selectRaw('room_id, COUNT(*) as booked')
            ->groupBy('room_id')
            ->pluck('booked', 'room_id');

        $data = $hotel->rooms->mapWithKeys(fn($room) => [
            $room->id => max(0, ($room->quantity ?? 1) - $bookedCounts->get($room->id, 0)),
        ]);

        return response()->json($data);
    }
}
