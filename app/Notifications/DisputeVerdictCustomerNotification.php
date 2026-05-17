<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeVerdictCustomerNotification extends Notification
{
    use Queueable;

    public function __construct(private Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verdictMap = [
            'refund_full'      => 'Hoàn tiền 100%',
            'refund_partial'   => 'Hoàn tiền một phần',
            'voucher'          => 'Cấp voucher bù đắp',
            'rejected'         => 'Không chấp nhận khiếu nại',
            'hotel_compensate' => 'Yêu cầu khách sạn bồi thường',
        ];

        $verdictLabel = $verdictMap[$this->dispute->verdict] ?? $this->dispute->verdict;
        $subject      = 'Kết quả xử lý khiếu nại #' . $this->dispute->id . ' — StayGo';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.dispute-verdict-customer', [
                'dispute'      => $this->dispute,
                'verdictLabel' => $verdictLabel,
                'notifiable'   => $notifiable,
            ]);
    }
}
