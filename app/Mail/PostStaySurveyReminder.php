<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostStaySurveyReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bạn vẫn còn 50 điểm thưởng đang chờ từ chuyến ở ' . ($this->booking->room?->hotel?->name ?? 'khách sạn') . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.post-stay-survey-reminder',
            with: ['booking' => $this->booking],
        );
    }
}
