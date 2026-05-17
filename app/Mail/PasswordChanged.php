<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public string $changedAt;

    public function __construct(public User $user, string $changedAt = '')
    {
        $this->changedAt = $changedAt ?: now()->format('H:i d/m/Y');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mật khẩu StayGo đã được thay đổi thành công',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: [
                'user'      => $this->user,
                'changedAt' => $this->changedAt,
            ],
        );
    }
}
