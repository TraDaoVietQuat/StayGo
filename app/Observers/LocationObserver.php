<?php

namespace App\Observers;

use App\Models\Location;
use App\Traits\ClearsRedisCache;

class LocationObserver
{
    use ClearsRedisCache;

    public function created(Location $location): void { $this->clearAll(); }
    public function updated(Location $location): void { $this->clearAll(); }
    public function deleted(Location $location): void { $this->clearAll(); }

    private function clearAll(): void
    {
        $this->forgetMany([
            'home.locations.v2',
            'all.locations.with_count.v2',
            'home.featured_by_location',
            'home.featured_hotels',
            'deals.weekend_deals',
            'widget.stats_overview.data',
        ]);

        $this->forgetByPattern('hotel.related.*');
    }
}
