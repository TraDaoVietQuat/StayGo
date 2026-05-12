<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminRefundNotice extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo Admin] 💰 Hoàn tiền #' . $this->booking->order_code . ' — ' . number_format($this->booking->refund_amount ?? $this->booking->total_price * 0.8, 0, ',', '.') . 'đ',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-refund-notice');
    }
}
