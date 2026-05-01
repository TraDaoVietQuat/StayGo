<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Traits\ClearsRedisCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    use ClearsRedisCache;
    /**
     * Khi payment được cập nhật → auto-confirm booking nếu completed
     */
    public function updated(Payment $payment): void
    {
        // Chỉ xử lý khi payment_status vừa thay đổi thành 'completed'
        if (!$payment->wasChanged('payment_status')) return;
        if ($payment->payment_status !== 'completed') return;

        $booking = $payment->booking;
        if (!$booking) return;

        // Cập nhật booking status → confirmed
        if (in_array($booking->status, ['pending'])) {
            $booking->update([
                'status'  => 'confirmed',
            ]);

            // Ghi audit log
            AuditLog::log(
                action: 'booking.auto_confirmed',
                subject: $booking,
                newValues: ['status' => 'confirmed', 'triggered_by' => 'payment_completed']
            );

            // Thông báo user
            if ($booking->user) {
                try {
                    $booking->user->notify(new BookingConfirmedNotification($booking));
                } catch (\Exception $e) {
                    Log::error('Notification failed after payment: ' . $e->getMessage());
                }
            }
        }

        // Cập nhật paid_at
        $payment->updateQuietly(['paid_at' => now()]);

        // Xóa cache widget payment
        $this->forgetMany([
            'widget.payment_method.data.all',
            'widget.payment_method.data.completed',
            'widget.payment_method.data.pending',
            'widget.stats_overview.data',
            'widget.occupancy.data',
        ]);
    }
}
