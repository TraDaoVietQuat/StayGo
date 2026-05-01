<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    // Hidden from main dashboard — only rendered inside ListPayments
    public static function canView(): bool
    {
        return false;
    }

    protected function getStats(): array
    {
        $totalCollected = Payment::where('payment_status', 'completed')->sum('amount');
        $paid           = Payment::where('payment_status', 'completed')->count();
        $pending        = Payment::where('payment_status', 'pending')->count();
        $failed         = Payment::where('payment_status', 'failed')->count();

        return [
            Stat::make('Tổng đã thu', number_format((int) $totalCollected, 0, ',', '.') . 'đ')
                ->description('Doanh thu đã xác nhận')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Đã thanh toán', $paid)
                ->description('Giao dịch thành công')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Chờ xử lý', $pending)
                ->description('Cần xác nhận')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Thất bại', $failed)
                ->description('Giao dịch thất bại')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
