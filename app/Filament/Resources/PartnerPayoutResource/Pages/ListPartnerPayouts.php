<?php

namespace App\Filament\Resources\PartnerPayoutResource\Pages;

use App\Filament\Resources\PartnerPayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerPayouts extends ListRecords
{
    protected static string $resource = PartnerPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('Tạo kỳ chi trả')];
    }
}
