<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public string $variant; // 'a' hoặc 'b' cho A/B test

    public function __construct(public Booking $booking, string $variant = '')
    {
        // Nếu không truyền variant, tự chọn ngẫu nhiên 50/50
        $this->variant = $variant ?: (rand(0, 1) ? 'b' : 'a');
    }

    public function envelope(): Envelope
    {
        $hotel = $this->booking->room?->hotel?->name ?? 'khách sạn';
        return new Envelope(
            subject: "Đặt phòng xác nhận! Mã #{$this->booking->order_code} tại {$hotel} ✓",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'variant' => $this->variant,
            ],
        );
    }
}
