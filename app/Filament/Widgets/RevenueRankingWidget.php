<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueRankingWidget extends Widget
{
    protected static string $view = 'filament.widgets.revenue-ranking';
    protected static ?int $sort = 5;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = '🥇 Bảng xếp hạng doanh thu';

    public function getViewData(): array
    {
        $hotels = Cache::get('widget.revenue_ranking.data') ?? $this->computeLive();

        return ['hotels' => $hotels];
    }

    private function computeLive()
    {
        return Booking::whereNotIn('status', ['cancelled'])
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->select(
                'hotels.id',
                'hotels.name',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_price) as total_revenue')
            )
            ->groupBy('hotels.id', 'hotels.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }
}
