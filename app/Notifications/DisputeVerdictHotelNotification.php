<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeVerdictHotelNotification extends Notification
{
    use Queueable;

    public function __construct(private Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Thông báo kết quả xử lý tranh chấp #' . $this->dispute->id . ' — StayGo';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.dispute-verdict-hotel', [
                'dispute'    => $this->dispute,
                'notifiable' => $notifiable,
            ]);
    }
}
