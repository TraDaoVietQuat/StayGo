<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'booking_cancelled',
            'title'      => 'Đặt phòng đã hủy',
            'message'    => "Đặt phòng #{$this->booking->order_code} đã được hủy thành công.",
            'order_code' => $this->booking->order_code,
            'booking_id' => $this->booking->id,
            'url'        => route('booking.my'),
        ];
    }
}
