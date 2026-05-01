<?php

namespace App\Filament\Resources\SupportRequestResource\Pages;

use App\Filament\Resources\SupportRequestResource;
use App\Models\SupportRequest;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSupportRequests extends ListRecords
{
    protected static string $resource = SupportRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm yêu cầu'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(SupportRequest::count()),

            'pending' => Tab::make('Chờ xử lý')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(SupportRequest::where('status', 'pending')->count())
                ->badgeColor('danger'),

            'processing' => Tab::make('Đang xử lý')
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'processing'))
                ->badge(SupportRequest::where('status', 'processing')->count())
                ->badgeColor('warning'),

            'resolved' => Tab::make('Đã giải quyết')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'resolved'))
                ->badge(SupportRequest::where('status', 'resolved')->count())
                ->badgeColor('success'),

            'closed' => Tab::make('Đã đóng')
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'closed'))
                ->badge(SupportRequest::where('status', 'closed')->count())
                ->badgeColor('gray'),
        ];
    }
}
