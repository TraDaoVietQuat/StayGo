<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class RecentBookingsWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-bookings';
    protected static ?int $sort = 6;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $bookings = Cache::get('widget.recent_bookings.data') ?? $this->computeLive();

        return ['bookings' => $bookings];
    }

    private function computeLive()
    {
        return Booking::with(['room.hotel'])->latest()->limit(8)->get();
    }
}
