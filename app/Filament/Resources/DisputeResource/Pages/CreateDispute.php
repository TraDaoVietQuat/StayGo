<?php

namespace App\Filament\Resources\DisputeResource\Pages;

use App\Filament\Resources\DisputeResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateDispute extends CreateRecord
{
    protected static string $resource = DisputeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-populate user_id and hotel_id from booking
        if (!empty($data['booking_id'])) {
            $booking = Booking::with('room.hotel')->find($data['booking_id']);
            if ($booking) {
                $data['user_id']  = $data['user_id']  ?? $booking->user_id;
                $data['hotel_id'] = $data['hotel_id'] ?? $booking->room?->hotel?->id;
            }
        }

        // Set deadline: 4h for urgent, 24h for normal
        $hours = ($data['priority'] ?? 'normal') === 'urgent' ? 4 : 24;
        $data['deadline_at'] = Carbon::now()->addHours($hours);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
