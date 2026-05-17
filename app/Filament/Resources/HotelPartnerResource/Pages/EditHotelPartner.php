<?php

namespace App\Filament\Resources\HotelPartnerResource\Pages;

use App\Filament\Resources\HotelPartnerResource;
use App\Models\Hotel;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHotelPartner extends EditRecord
{
    protected static string $resource = HotelPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Gán khách sạn nếu có chọn
        if (!empty($data['hotel_id_assign'])) {
            Hotel::where('id', $data['hotel_id_assign'])
                ->update(['partner_user_id' => $this->record->user_id]);
        }
        unset($data['hotel_id_assign'], $data['user']);
        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->title('Đã cập nhật thông tin đối tác')->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
