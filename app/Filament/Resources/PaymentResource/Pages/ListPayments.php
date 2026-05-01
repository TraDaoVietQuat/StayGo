<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Filament\Widgets\PaymentStatsWidget;
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm thanh toán'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(Payment::count()),

            'pending' => Tab::make('Chờ xử lý')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_status', 'pending')->where('qr_scanned', false))
                ->badge(Payment::where('payment_status', 'pending')->where('qr_scanned', false)->count())
                ->badgeColor('warning'),

            'completed' => Tab::make('Đã thanh toán')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_status', 'completed'))
                ->badge(Payment::where('payment_status', 'completed')->count())
                ->badgeColor('success'),

            'failed' => Tab::make('Thất bại')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_status', 'failed'))
                ->badge(Payment::where('payment_status', 'failed')->count())
                ->badgeColor('danger'),

            'refunded' => Tab::make('Hoàn tiền')
                ->icon('heroicon-o-arrow-uturn-left')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_status', 'refunded'))
                ->badge(Payment::where('payment_status', 'refunded')->count())
                ->badgeColor('info'),

            'warned' => Tab::make('Cảnh báo KH')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('qr_scanned', true))
                ->badge(Payment::where('qr_scanned', true)->count())
                ->badgeColor('warning'),

            'conflict' => Tab::make('Xung đột phòng')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('booking', fn(Builder $sub) => $sub->where('refund_requested', true)))
                ->badge(Payment::whereHas('booking', fn(Builder $query) => $query->where('refund_requested', true))->count())
                ->badgeColor('danger'),
        ];
    }
}
