<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingRefundedNotification extends Notification
{
    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'booking_refunded',
            'title'      => 'Hoàn tiền thành công',
            'message'    => "Đặt phòng #{$this->booking->order_code} đã được hoàn tiền " . number_format($this->booking->refund_amount, 0, ',', '.') . 'đ.',
            'order_code' => $this->booking->order_code,
            'booking_id' => $this->booking->id,
            'url'        => route('booking.my'),
        ];
    }
}
