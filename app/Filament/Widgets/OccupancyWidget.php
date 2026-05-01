<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class OccupancyWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $d = Cache::get('widget.occupancy.data') ?? $this->computeLive();

        $totalRooms    = $d['totalRooms'];
        $occupiedRooms = $d['occupiedRooms'];
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        $topHotel      = $d['topHotel'];

        return [
            Stat::make('Tỉ lệ lấp đầy hôm nay', $occupancyRate . '%')
                ->description($occupiedRooms . '/' . $totalRooms . ' phòng đang có khách')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color($occupancyRate >= 70 ? 'success' : ($occupancyRate >= 40 ? 'warning' : 'danger'))
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Check-in hôm nay', $d['checkInsToday'])
                ->description('Khách nhận phòng ngày ' . now()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info')
                ->icon('heroicon-o-arrow-right-on-rectangle'),

            Stat::make('Check-out hôm nay', $d['checkOutsToday'])
                ->description('Khách trả phòng ngày ' . now()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-arrow-left-circle')
                ->color('gray')
                ->icon('heroicon-o-arrow-left-on-rectangle'),

            Stat::make('Doanh thu tháng ' . now()->month, number_format($d['revenueMonth'], 0, ',', '.') . 'đ')
                ->description('Tháng ' . now()->format('m/Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('KS lấp đầy nhất', $topHotel ? $topHotel['name'] : '—')
                ->description($topHotel ? 'Tỉ lệ: ' . $topHotel['rate'] . '%' : 'Chưa có dữ liệu')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning')
                ->icon('heroicon-o-star'),
        ];
    }

    private function computeLive(): array
    {
        $today = now()->toDateString();
        $occupiedRooms = Booking::whereIn('status', ['confirmed', 'pending'])
            ->whereDate('check_in', '<=', $today)->whereDate('check_out', '>', $today)
            ->distinct('room_id')->count('room_id');
        $totalRooms = Room::count();
        $topHotel = Hotel::where('is_active', true)
            ->withCount(['rooms as occupied' => fn($q) => $q
                ->whereHas('bookings', fn($b) => $b
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->whereDate('check_in', '<=', $today)->whereDate('check_out', '>', $today)
                )])
            ->withCount('rooms')->get()
            ->map(fn($h) => ['name' => $h->name, 'rate' => $h->rooms_count > 0 ? round(($h->occupied / $h->rooms_count) * 100, 1) : 0])
            ->sortByDesc('rate')->first();
        return [
            'occupiedRooms'  => $occupiedRooms,
            'totalRooms'     => $totalRooms,
            'topHotel'       => $topHotel,
            'checkInsToday'  => Booking::whereIn('status', ['confirmed', 'pending'])->whereDate('check_in', $today)->count(),
            'checkOutsToday' => Booking::whereIn('status', ['confirmed', 'completed'])->whereDate('check_out', $today)->count(),
            'revenueMonth'   => Booking::whereNotIn('status', ['cancelled'])->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_price'),
        ];
    }
}
