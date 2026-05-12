<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCheckoutReminders extends Command
{
    protected $signature   = 'staygo:checkout-reminders';
    protected $description = 'Gửi email nhắc nhở trả phòng (check-out ngày hôm nay)';

    public function handle(): void
    {
        $today = now()->toDateString();

        $bookings = Booking::where('status', 'confirmed')
            ->whereDate('check_out', $today)
            ->whereNull('checkout_reminder_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookings as $booking) {
            if (!$booking->email) continue;
            try {
                Mail::to($booking->email)->send(new \App\Mail\CheckoutReminder($booking));
                $booking->updateQuietly(['checkout_reminder_sent_at' => now()]);
                $this->line("Sent checkout reminder for #{$booking->order_code}");
            } catch (\Exception $e) {
                $this->error("Failed #{$booking->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$bookings->count()} checkout reminder(s).");
    }
}
