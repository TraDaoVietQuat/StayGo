<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDailyDigest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array $stats {
     *   gmvYesterday: float,
     *   gmvChange: float,          // % change vs day before
     *   bookingsCount: int,
     *   bookingsChange: float,
     *   cancelRate: float,
     *   pendingCount: int,
     *   complaintsNew: int,
     *   pendingPartners: int,
     *   urgentAlerts: array,       // [['text' => '', 'url' => '']]
     *   watchItems: array,         // ['text']
     *   topHotels: array,          // [['name' => '', 'revenue' => float]]
     * }
     */
    public function __construct(public array $stats) {}

    public function envelope(): Envelope
    {
        $date = now()->subDay()->format('d/m/Y');
        $gmv  = number_format($this->stats['gmvYesterday'] ?? 0, 0, ',', '.');
        $bk   = $this->stats['bookingsCount'] ?? 0;
        $al   = count($this->stats['urgentAlerts'] ?? []);

        return new Envelope(
            subject: "[Daily] Tóm tắt StayGo {$date} | GMV: {$gmv}đ | Booking: {$bk} | Alert: {$al}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-daily-digest');
    }

    public function attachments(): array
    {
        return [];
    }
}
