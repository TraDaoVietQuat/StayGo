<?php

namespace App\Filament\HotelPartner\Widgets;

use App\Models\Booking;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PartnerStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if (!$hotel) {
            return [Stat::make('Thông báo', 'Chưa có khách sạn được gán')->color('warning')];
        }

        $roomIds   = $hotel->rooms()->pluck('id');
        $today     = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        $bookingsToday = Booking::whereIn('room_id', $roomIds)->whereDate('created_at', $today)->count();
        $bookingsMonth = Booking::whereIn('room_id', $roomIds)->where('created_at', 'like', "$thisMonth%")->count();
        $pendingCount  = Booking::whereIn('room_id', $roomIds)->where('status', 'pending')->count();

        $revenueMonth = Booking::whereIn('room_id', $roomIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('created_at', 'like', "$thisMonth%")
            ->sum('total_price');

        $avgRating = Review::where('hotel_id', $hotel->id)->where('is_active', true)->avg('rating');

        return [
            Stat::make('Đặt phòng hôm nay', $bookingsToday)
                ->description('Tổng tháng: ' . $bookingsMonth . ' đơn')
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Đơn chờ xác nhận', $pendingCount)
                ->description('Cần xử lý ngay')
                ->color($pendingCount > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Doanh thu tháng', number_format($revenueMonth, 0, ',', '.') . ' ₫')
                ->description(now()->format('m/Y'))
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Điểm đánh giá', number_format((float) $avgRating, 1) . ' / 5')
                ->description($hotel->review_count . ' đánh giá')
                ->color('info')
                ->icon('heroicon-o-star'),
        ];
    }
}
