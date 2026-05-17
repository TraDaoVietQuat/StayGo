<?php

namespace App\Filament\HotelPartner\Resources\PayoutResource\Pages;

use App\Filament\HotelPartner\Resources\PayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListPayouts extends ListRecords
{
    protected static string $resource = PayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
