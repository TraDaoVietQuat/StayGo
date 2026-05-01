<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\SupportRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $d = Cache::remember('widget.stats_overview.data', 2100, fn () => [
            'total_revenue'    => Booking::whereNotIn('status', ['cancelled'])->sum('total_price'),
            'total_users'      => User::where('role', '!=', 'admin')->count(),
            'total_hotels'     => Hotel::where('is_active', true)->count(),
            'total_rooms'      => Room::count(),
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'pending_support'  => SupportRequest::where('status', 'pending')->count(),
        ]);

        return [
            Stat::make('Tổng doanh thu', number_format($d['total_revenue'], 0, ',', '.') . 'đ')
                ->description('Từ các booking không bị hủy')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Người dùng', $d['total_users'])
                ->description('Tổng tài khoản')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('Khách sạn', $d['total_hotels'])
                ->description('Đang hoạt động')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning')
                ->icon('heroicon-o-building-office'),

            Stat::make('Phòng', $d['total_rooms'])
                ->description('Tổng loại phòng')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->icon('heroicon-o-home'),

            Stat::make('Đặt phòng', $d['total_bookings'])
                ->description($d['pending_bookings'] . ' đơn đang chờ xử lý')
                ->descriptionIcon('heroicon-m-clock')
                ->color($d['pending_bookings'] > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Hỗ trợ chờ xử lý', $d['pending_support'])
                ->description('Yêu cầu cần xử lý')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->url(route('filament.admin.resources.support-requests.index'))
                ->color($d['pending_support'] > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
