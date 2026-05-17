<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerKpiAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Hotel  $hotel
     * @param  string $level   'yellow' | 'red'
     * @param  array  $issues  [['metric' => '...', 'value' => '...', 'threshold' => '...', 'tip' => '...']]
     * @param  array  $kpis    ['cancellation_rate' => float, 'rating' => float, 'open_disputes' => int]
     */
    public function __construct(
        public Hotel  $hotel,
        public string $level,
        public array  $issues,
        public array  $kpis = [],
    ) {}

    public function envelope(): Envelope
    {
        $prefix = $this->level === 'red' ? '🚨 [Cảnh báo nghiêm trọng]' : '⚠️ [Cảnh báo]';

        return new Envelope(
            subject: "{$prefix} KPI khách sạn cần cải thiện — {$this->hotel->name}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-kpi-alert');
    }

    public function attachments(): array
    {
        return [];
    }
}
