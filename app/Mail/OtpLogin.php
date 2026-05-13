<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpLogin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo] 🔐 Mã OTP đăng nhập',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-login',
            with: ['otp' => $this->otp, 'user' => $this->user],
        );
    }
}
