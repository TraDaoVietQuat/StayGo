<?php

namespace App\Mail;

use App\Models\HotelPartnerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HotelPartnerProfile $profile,
        public string $tempPassword,
    ) {}

    public function envelope(): Envelope
    {
        $hotelName = $this->profile->hotel?->name ?? $this->profile->business_name ?? 'Khách sạn';

        return new Envelope(
            subject: "Chúc mừng! {$hotelName} đã được duyệt làm đối tác StayGo",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-approved');
    }

    public function attachments(): array
    {
        return [];
    }
}
