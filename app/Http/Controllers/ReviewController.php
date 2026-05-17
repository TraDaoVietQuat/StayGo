<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'hotel_id'       => ['required', 'exists:hotels,id'],
            'booking_id'     => ['required', 'exists:bookings,id'],
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'comment'        => ['required', 'string', 'min:10', 'max:1000'],
            'cleanliness'    => ['nullable', 'integer', 'min:1', 'max:5'],
            'service_score'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'location_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'value_score'    => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->status !== 'completed', 400, 'Chỉ có thể đánh giá sau khi hoàn thành lưu trú.');

        $existing = Review::where('booking_id', $request->booking_id)->exists();
        abort_if($existing, 400, 'Bạn đã đánh giá đặt phòng này rồi.');

        DB::transaction(function () use ($request) {
            Review::create([
                'hotel_id'       => $request->hotel_id,
                'user_id'        => Auth::id(),
                'booking_id'     => $request->booking_id,
                'rating'         => $request->rating,
                'cleanliness'    => $request->cleanliness    ?: null,
                'service_score'  => $request->service_score  ?: null,
                'location_score' => $request->location_score ?: null,
                'value_score'    => $request->value_score    ?: null,
                'comment'        => $request->comment,
            ]);

            $hotel = Hotel::find($request->hotel_id);
            $avg   = Review::where('hotel_id', $hotel->id)->where('is_active', true)->avg('rating');
            $count = Review::where('hotel_id', $hotel->id)->where('is_active', true)->count();
            $hotel->update(['rating' => round($avg, 1), 'review_count' => $count]);
        });

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }

    public function update(Request $request, Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);

        $request->validate([
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'comment'        => ['required', 'string', 'min:10', 'max:1000'],
            'cleanliness'    => ['nullable', 'integer', 'min:1', 'max:5'],
            'service_score'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'location_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'value_score'    => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $review->update([
            'rating'         => $request->rating,
            'cleanliness'    => $request->cleanliness    ?: null,
            'service_score'  => $request->service_score  ?: null,
            'location_score' => $request->location_score ?: null,
            'value_score'    => $request->value_score    ?: null,
            'comment'        => $request->comment,
        ]);

        return back()->with('success', 'Đánh giá đã được cập nhật.');
    }

    public function destroy(Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);

        DB::transaction(function () use ($review) {
            $hotelId = $review->hotel_id;
            $review->delete();

            $hotel = Hotel::find($hotelId);
            $avg   = Review::where('hotel_id', $hotelId)->where('is_active', true)->avg('rating');
            $count = Review::where('hotel_id', $hotelId)->where('is_active', true)->count();
            $hotel->update(['rating' => round($avg ?? 0, 1), 'review_count' => $count]);
        });

        return back()->with('success', 'Đã xóa đánh giá.');
    }
}
