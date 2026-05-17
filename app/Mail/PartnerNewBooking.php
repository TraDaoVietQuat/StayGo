<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerNewBooking extends Mailable
{
    use Queueable, SerializesModels;

    public bool  $autoConfirm;
    public float $commissionRate;
    public float $commissionAmount;
    public float $netAmount;

    public function __construct(
        public Booking $booking,
        bool  $autoConfirm    = false,
        float $commissionRate = 15.0,
    ) {
        $this->autoConfirm      = $autoConfirm;
        $this->commissionRate   = $commissionRate;
        $this->commissionAmount = round($booking->total_price * $commissionRate / 100, 0);
        $this->netAmount        = $booking->total_price - $this->commissionAmount;
    }

    public function envelope(): Envelope
    {
        $checkIn = $this->booking->check_in?->format('d/m/Y') ?? '?';
        $room    = $this->booking->room?->name ?? 'Phòng';

        $subject = $this->autoConfirm
            ? "[Booking mới] #{$this->booking->order_code} — {$checkIn} | {$room}"
            : "[Cần xác nhận] Booking #{$this->booking->order_code} — Còn 2 giờ để phản hồi";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-new-booking');
    }

    public function attachments(): array
    {
        return [];
    }
}
