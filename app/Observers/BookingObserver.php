<?php

namespace App\Observers;

use App\Models\Booking;
use App\Traits\ClearsRedisCache;

class BookingObserver
{
    use ClearsRedisCache;

    public function created(Booking $booking): void { $this->clearAll(); }
    public function updated(Booking $booking): void { $this->clearAll(); }
    public function deleted(Booking $booking): void { $this->clearAll(); }

    private function clearAll(): void
    {
        $this->forgetMany([
            // Admin widgets
            'widget.stats_overview.data',
            'widget.occupancy.data',
            'widget.revenue_chart.data.3',
            'widget.revenue_chart.data.6',
            'widget.revenue_chart.data.12',
            'widget.booking_status.data.all',
            'widget.booking_status.data.month',
            'widget.booking_status.data.year',
            'widget.recent_bookings.data',
            'widget.top_hotels.data.bookings',
            'widget.top_hotels.data.revenue',
            'widget.revenue_ranking.data',
            // Admin nav badges
            'badge.bookings.pending',
        ]);
        // Partner badge cache keyed by hotel — clear via pattern
        try {
            $this->forgetByPattern('badge.partner.bookings.*');
            $this->forgetByPattern('partner.stats.*');
        } catch (\Throwable) {
            // Redis không available — file/database cache sẽ expire tự nhiên
        }
    }
}
