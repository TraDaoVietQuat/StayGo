<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ưu tiên tên tuỳ chỉnh nếu có
        if (!empty($data['room_name_custom'])) {
            $data['room_name'] = $data['room_name_custom'];
        }
        unset($data['room_name_custom']);
        return $data;
    }
}
