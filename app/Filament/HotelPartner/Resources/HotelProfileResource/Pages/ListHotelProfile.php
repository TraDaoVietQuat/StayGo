<?php

namespace App\Filament\HotelPartner\Resources\HotelProfileResource\Pages;

use App\Filament\HotelPartner\Resources\HotelProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListHotelProfile extends ListRecords
{
    protected static string $resource = HotelProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
