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

    /**
     * @param  string  $wave  '7day' | '1day' | 'morning'
     */
    public function __construct(public Booking $booking, public string $wave = '1day') {}

    public function envelope(): Envelope
    {
        $hotel = $this->booking->room?->hotel?->name ?? 'khách sạn';
        $checkin = $this->booking->check_in?->format('d/m/Y') ?? '';

        $subject = match ($this->wave) {
            '7day'    => "7 ngày nữa bạn sẽ check-in tại {$hotel}!",
            'morning' => "Chào buổi sáng! Hôm nay là ngày check-in của bạn 🌅",
            default   => "Ngày mai bạn check-in rồi! Checklist nhanh cho {$hotel}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pre-arrival-reminder',
            with: ['booking' => $this->booking, 'wave' => $this->wave],
        );
    }
}
