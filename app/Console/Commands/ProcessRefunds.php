<?php

namespace App\Console\Commands;

use App\Mail\AdminRefundNotice;
use App\Mail\BookingRefunded;
use App\Models\AuditLog;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessRefunds extends Command
{
    protected $signature   = 'staygo:process-refunds';
    protected $description = 'Tự động xử lý các yêu cầu hoàn tiền quá 3 ngày chưa được duyệt';

    public function handle(): void
    {
        $bookings = Booking::where('refund_requested', true)
            ->where('status', '!=', 'refunded')
            ->whereNotNull('refund_requested_at')
            ->where('refund_requested_at', '<=', now()->subDays(3))
            ->get();

        foreach ($bookings as $booking) {
            $booking->update([
                'status'         => 'refunded',
                'payment_status' => 'refunded',
            ]);

            // Ghi audit log
            AuditLog::log(
                action: 'booking.auto_refunded',
                subject: $booking,
                newValues: ['status' => 'refunded', 'triggered_by' => 'scheduler']
            );

            // Thông báo user + admin
            try {
                $booking->load('room.hotel');
                if ($booking->user) {
                    $booking->user->notify(new \App\Notifications\BookingRefundedNotification($booking));
                }
                if ($booking->email) {
                    Mail::to($booking->email)->send(new BookingRefunded($booking));
                }
                Mail::to(config('mail.from.address'))->send(new AdminRefundNotice($booking));
            } catch (\Exception) {}

            $this->line("Refunded booking #{$booking->order_code}");
        }

        $this->info("Processed {$bookings->count()} refund(s).");
    }
}
