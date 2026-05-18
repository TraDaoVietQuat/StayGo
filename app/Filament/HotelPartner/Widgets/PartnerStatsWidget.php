<?php

namespace App\Filament\HotelPartner\Widgets;

use App\Models\Booking;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class PartnerStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if (!$hotel) {
            return [Stat::make('Thông báo', 'Chưa có khách sạn được gán')->color('warning')];
        }

        $hotelId   = $hotel->id;
        $thisMonth = now()->format('Y-m');

        $d = Cache::remember("partner.stats.{$hotelId}.{$thisMonth}", 120, function () use ($hotelId, $thisMonth) {
            $roomIds = \App\Models\Room::where('hotel_id', $hotelId)->pluck('id');
            $today   = now()->toDateString();
            return [
                'bookings_today' => Booking::whereIn('room_id', $roomIds)->whereDate('created_at', $today)->count(),
                'bookings_month' => Booking::whereIn('room_id', $roomIds)->where('created_at', 'like', "$thisMonth%")->count(),
                'pending_count'  => Booking::whereIn('room_id', $roomIds)->where('status', 'pending')->count(),
                'revenue_month'  => Booking::whereIn('room_id', $roomIds)
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->where('created_at', 'like', "$thisMonth%")
                    ->sum('total_price'),
                'avg_rating'     => Review::where('hotel_id', $hotelId)->where('is_active', true)->avg('rating'),
                'review_count'   => $hotel->review_count,
            ];
        });

        $bookingsToday = $d['bookings_today'];
        $bookingsMonth = $d['bookings_month'];
        $pendingCount  = $d['pending_count'];
        $revenueMonth  = $d['revenue_month'];
        $avgRating     = $d['avg_rating'];

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
