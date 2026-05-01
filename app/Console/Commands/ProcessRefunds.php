<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Booking;
use Illuminate\Console\Command;

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

            // Thông báo user
            if ($booking->user) {
                try {
                    $booking->user->notify(new \App\Notifications\BookingRefundedNotification($booking));
                } catch (\Exception) {}
            }

            $this->line("Refunded booking #{$booking->order_code}");
        }

        $this->info("Processed {$bookings->count()} refund(s).");
    }
}
