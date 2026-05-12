<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckoutReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo] ⏰ Nhắc nhở trả phòng hôm nay - ' . ($this->booking->room?->hotel?->name ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.checkout-reminder');
    }
}
