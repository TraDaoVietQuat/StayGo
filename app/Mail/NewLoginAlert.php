<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLoginAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $ip,
        public string $device,
        public string $browser,
        public string $city    = '',
        public string $country = '',
        public string $loginAt = '',
    ) {
        $this->loginAt = $loginAt ?: now()->format('H:i d/m/Y');
    }

    public function envelope(): Envelope
    {
        $location = trim("{$this->city}, {$this->country}", ', ');
        return new Envelope(
            subject: "Cảnh báo: Đăng nhập mới vào tài khoản StayGo của bạn" . ($location ? " từ {$location}" : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-login-alert',
            with: [
                'user'    => $this->user,
                'ip'      => $this->ip,
                'device'  => $this->device,
                'browser' => $this->browser,
                'city'    => $this->city,
                'country' => $this->country,
                'loginAt' => $this->loginAt,
            ],
        );
    }
}
