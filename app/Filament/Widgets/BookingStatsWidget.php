<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return false; // Only shown in ListBookings
    }

    protected function getStats(): array
    {
        $totalRevenue  = Booking::where('status', 'confirmed')->sum('total_price');
        $pending       = Booking::where('status', 'pending')->count();
        $confirmed     = Booking::where('status', 'confirmed')->count();
        $cancelled     = Booking::where('status', 'cancelled')->count();
        $refundPending = Booking::where('refund_requested', true)
            ->whereNotIn('status', ['refunded'])->count();

        return [
            Stat::make('Tổng doanh thu', number_format((int) $totalRevenue, 0, ',', '.') . 'đ')
                ->description('Đơn đã xác nhận')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Chờ xác nhận', $pending)
                ->description('Cần xử lý')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Đã xác nhận', $confirmed)
                ->description('Đang lưu trú / hoàn thành')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Đã hủy', $cancelled)
                ->description('Đơn bị hủy')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Yêu cầu hoàn tiền', $refundPending)
                ->description('Cần duyệt')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning'),
        ];
    }
}
