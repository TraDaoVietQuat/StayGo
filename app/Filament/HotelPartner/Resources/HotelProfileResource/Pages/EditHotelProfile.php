<?php

namespace App\Filament\HotelPartner\Resources\HotelProfileResource\Pages;

use App\Filament\HotelPartner\Resources\HotelProfileResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHotelProfile extends EditRecord
{
    protected static string $resource = HotelProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->title('Đã lưu hồ sơ khách sạn')->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
