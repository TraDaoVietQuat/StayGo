<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportRequestReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportRequest $supportRequest,
        public string $adminReply = '',
        public bool $isResolved = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isResolved
            ? '[StayGo] ✅ Yêu cầu hỗ trợ của bạn đã được giải quyết'
            : '[StayGo] 💬 Admin đã phản hồi yêu cầu hỗ trợ của bạn';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.support-request-replied');
    }
}
