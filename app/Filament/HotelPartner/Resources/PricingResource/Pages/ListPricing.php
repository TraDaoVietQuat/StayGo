<?php

namespace App\Filament\HotelPartner\Resources\PricingResource\Pages;

use App\Filament\HotelPartner\Resources\PricingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPricing extends ListRecords
{
    protected static string $resource = PricingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Thêm giá đặc biệt')];
    }
}
