<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreArrivalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo] Nhắc nhở: Bạn check-in ngày mai – ' . $this->booking->order_code,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pre-arrival-reminder');
    }
}
