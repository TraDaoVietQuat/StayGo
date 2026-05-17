<?php

namespace App\Filament\HotelPartner\Pages;

use App\Filament\HotelPartner\Widgets\PartnerRevenueWidget;
use App\Filament\HotelPartner\Widgets\PartnerStatsWidget;
use App\Filament\HotelPartner\Widgets\UpcomingCheckInsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Tổng quan';
    protected static ?string $title           = 'Bảng điều khiển';
    protected static ?int    $navigationSort  = 1;

    public function getWidgets(): array
    {
        return [
            PartnerStatsWidget::class,
            UpcomingCheckInsWidget::class,
            PartnerRevenueWidget::class,
        ];
    }
}
