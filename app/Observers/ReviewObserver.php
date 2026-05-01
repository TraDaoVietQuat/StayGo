<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Hotel;
use App\Models\Review;
use App\Traits\ClearsRedisCache;

class ReviewObserver
{
    use ClearsRedisCache;
    public function created(Review $review): void
    {
        $this->recalculateHotelRating($review->hotel_id);
    }

    public function updated(Review $review): void
    {
        if ($review->wasChanged(['rating', 'is_active'])) {
            $this->recalculateHotelRating($review->hotel_id);
        }
    }

    public function deleted(Review $review): void
    {
        $this->recalculateHotelRating($review->hotel_id);

        AuditLog::log(
            action: 'review.deleted',
            subject: $review,
            oldValues: ['rating' => $review->rating, 'comment' => $review->comment]
        );
    }

    private function recalculateHotelRating(int $hotelId): void
    {
        $hotel = Hotel::find($hotelId);
        if (!$hotel) return;

        $avg   = Review::where('hotel_id', $hotelId)->where('is_active', true)->avg('rating') ?? 0;
        $count = Review::where('hotel_id', $hotelId)->where('is_active', true)->count();

        $hotel->updateQuietly([
            'rating'       => round($avg, 1),
            'review_count' => $count,
        ]);

        // Xóa cache frontend vì rating thay đổi ảnh hưởng thứ tự hiển thị
        $this->forgetMany([
            'home.reviews',
            'home.featured_hotels',
            'home.featured_by_location',
            'home.weekend_deals',
            'deals.weekend_deals',
        ]);
    }
}
