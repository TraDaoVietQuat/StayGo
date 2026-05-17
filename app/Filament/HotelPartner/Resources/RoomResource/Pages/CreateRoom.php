<?php

namespace App\Filament\HotelPartner\Resources\RoomResource\Pages;

use App\Filament\HotelPartner\Resources\RoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        $data['hotel_id'] = $hotel?->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
