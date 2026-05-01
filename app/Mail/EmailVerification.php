<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $verifyUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[StayGo] Xác minh địa chỉ email của bạn');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.email-verification');
    }
}
