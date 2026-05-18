<?php

namespace App\Filament\HotelPartner\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class PartnerRevenueWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static bool $isLazy = true;
    protected static ?string $heading = 'Doanh thu 6 tháng gần đây';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $hotel   = auth('hotel_partner')->user()?->managedHotel;
        $roomIds = $hotel ? $hotel->rooms()->pluck('id') : collect();

        $labels = [];
        $data   = [];

        for ($i = 5; $i >= 0; $i--) {
            $month     = now()->subMonths($i);
            $labels[]  = $month->format('m/Y');
            $revenue   = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
            $data[] = (int) $revenue;
        }

        return [
            'datasets' => [[
                'label'           => 'Doanh thu (₫)',
                'data'            => $data,
                'backgroundColor' => 'rgba(59,130,246,0.15)',
                'borderColor'     => '#3b82f6',
                'borderWidth'     => 2,
                'fill'            => true,
                'tension'         => 0.4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
