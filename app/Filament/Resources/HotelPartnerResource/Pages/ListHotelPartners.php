<?php

namespace App\Filament\Resources\HotelPartnerResource\Pages;

use App\Filament\Resources\HotelPartnerResource;
use Filament\Resources\Pages\ListRecords;

class ListHotelPartners extends ListRecords
{
    protected static string $resource = HotelPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
