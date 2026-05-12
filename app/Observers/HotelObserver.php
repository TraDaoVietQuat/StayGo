<?php

namespace App\Observers;

use App\Models\Hotel;
use App\Traits\ClearsRedisCache;

class HotelObserver
{
    use ClearsRedisCache;

    public function created(Hotel $hotel): void  { $this->clearAll($hotel); }
    public function updated(Hotel $hotel): void  { $this->clearAll($hotel); }
    public function deleted(Hotel $hotel): void  { $this->clearAll($hotel); }

    private function clearAll(Hotel $hotel): void
    {
        // Frontend — trang chủ & danh sách (v2 + v3 keys)
        $this->forgetMany([
            'home.locations.v2',
            'home.locations.v3',
            'home.weekend_deals',
            'home.featured_hotels',
            'home.featured_by_location',
            'home.featured_by_location.v3',
            'home.blog_posts',
            'home.reviews',
            'deals.weekend_deals',
            'all.locations.with_count.v2',
            // widget admin
            'widget.revenue_ranking.data',
            'widget.top_hotels.data.bookings',
            'widget.top_hotels.data.revenue',
            'widget.stats_overview.data',
        ]);

        // Xóa cache related hotels theo pattern (hotel.related.{id})
        $this->forgetByPattern('hotel.related.*');
    }
}
