<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewSupportRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SupportRequest $supportRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo] 🆘 Yêu cầu hỗ trợ mới từ ' . $this->supportRequest->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-new-support-request');
    }
}
