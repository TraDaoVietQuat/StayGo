<?php

namespace App\Console\Commands;

use App\Mail\PreArrivalReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPreArrivalReminders extends Command
{
    protected $signature   = 'staygo:pre-arrival-reminders';
    protected $description = 'Gửi email nhắc nhở trước ngày check-in (24 giờ)';

    public function handle(): void
    {
        // Lấy booking check-in vào ngày mai, đã confirmed, chưa gửi reminder
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::where('status', 'confirmed')
            ->whereDate('check_in', $tomorrow)
            ->whereNull('reminder_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->email)->send(new PreArrivalReminder($booking));
                $booking->updateQuietly(['reminder_sent_at' => now()]);
                $this->line("Sent reminder for #{$booking->order_code}");
            } catch (\Exception $e) {
                $this->error("Failed #{$booking->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$bookings->count()} pre-arrival reminder(s).");
    }
}
