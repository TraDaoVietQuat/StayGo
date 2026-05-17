<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminSystemAlert extends Mailable
{
    use Queueable, SerializesModels;

    public string $alertTime;

    /**
     * @param  string $alertType          Tên loại cảnh báo, VD: "Thanh toán thất bại hàng loạt"
     * @param  string $severity           'LOW' | 'MEDIUM' | 'HIGH' | 'CRITICAL'
     * @param  string $description        Mô tả ngắn về sự cố
     * @param  string $technicalDetails   Thông số kỹ thuật (stack trace, query, metrics...)
     * @param  string $recommendedActions Hành động đề xuất
     */
    public function __construct(
        public string $alertType,
        public string $severity,
        public string $description,
        public string $technicalDetails   = '',
        public string $recommendedActions = '',
    ) {
        $this->alertTime = now()->format('H:i:s — d/m/Y');
    }

    public function envelope(): Envelope
    {
        $icon = match ($this->severity) {
            'CRITICAL' => '🚨',
            'HIGH'     => '🔴',
            'MEDIUM'   => '🟡',
            default    => '🔵',
        };

        return new Envelope(
            subject: "{$icon} [ALERT] {$this->alertType} — StayGo cần kiểm tra ngay",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-system-alert');
    }

    public function attachments(): array
    {
        return [];
    }
}
