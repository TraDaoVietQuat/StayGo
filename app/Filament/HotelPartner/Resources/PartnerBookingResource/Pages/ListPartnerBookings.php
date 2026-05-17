<?php

namespace App\Filament\HotelPartner\Resources\PartnerBookingResource\Pages;

use App\Filament\HotelPartner\Resources\PartnerBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerBookings extends ListRecords
{
    protected static string $resource = PartnerBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
