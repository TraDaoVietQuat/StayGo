<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'booking_confirmed',
            'title'      => 'Đặt phòng thành công',
            'message'    => "Đặt phòng #{$this->booking->order_code} đã được ghi nhận. Vui lòng hoàn tất thanh toán.",
            'order_code' => $this->booking->order_code,
            'booking_id' => $this->booking->id,
            'url'        => route('payment.show', $this->booking),
        ];
    }
}
