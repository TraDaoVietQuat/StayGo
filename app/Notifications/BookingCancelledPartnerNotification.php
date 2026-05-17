<?php

namespace App\Notifications;

use App\Models\Booking;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledPartnerNotification extends Notification
{
    use Queueable;

    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $checkIn = $this->booking->check_in?->format('d/m/Y') ?? '?';

        return FilamentNotification::make()
            ->title('❌ Đặt phòng bị hủy: ' . $this->booking->order_code)
            ->body($this->booking->full_name
                . ' | Check-in: ' . $checkIn
                . ' | ' . number_format($this->booking->total_price) . 'đ')
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label('Xem chi tiết')
                    ->url(url('/partner/bookings/' . $this->booking->id . '/edit'))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
