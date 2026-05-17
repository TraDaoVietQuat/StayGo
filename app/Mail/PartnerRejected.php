<?php

namespace App\Mail;

use App\Models\HotelPartnerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HotelPartnerProfile $profile,
        public string $rejectionReason,
    ) {}

    public function envelope(): Envelope
    {
        $hotelName = $this->profile->hotel?->name ?? $this->profile->business_name ?? 'Khách sạn';

        return new Envelope(
            subject: "Cập nhật hồ sơ đăng ký đối tác {$hotelName} — Cần bổ sung",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-rejected');
    }

    public function attachments(): array
    {
        return [];
    }
}
