<?php

namespace App\Mail;

use App\Models\PartnerPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerPayoutStatement extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerPayout $payout) {}

    public function envelope(): Envelope
    {
        $period = $this->payout->period_start?->format('d/m/Y')
            . ' – '
            . $this->payout->period_end?->format('d/m/Y');

        return new Envelope(
            subject: "[StayGo] Bảng kê thanh toán kỳ {$period} — {$this->payout->hotel?->name}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-payout-statement');
    }

    public function attachments(): array
    {
        return [];
    }
}
