<?php

namespace App\Console\Commands;

use App\Mail\PreArrivalReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPreArrivalReminders extends Command
{
    protected $signature   = 'staygo:pre-arrival-reminders';
    protected $description = 'Gửi chuỗi email nhắc nhở check-in: 7 ngày trước, 1 ngày trước, sáng ngày check-in';

    public function handle(): void
    {
        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $in7days  = now()->addDays(7)->toDateString();

        $sent = 0;

        // ── Wave 1: 7 ngày trước (chỉ gửi khi reminder_7day_sent_at là null) ──
        $bookings7day = Booking::where('status', 'confirmed')
            ->whereDate('check_in', $in7days)
            ->whereNull('reminder_7day_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookings7day as $booking) {
            if (!$booking->email) continue;
            try {
                Mail::to($booking->email)->send(new PreArrivalReminder($booking, '7day'));
                $booking->updateQuietly(['reminder_7day_sent_at' => now()]);
                $this->line("[7day] #{$booking->order_code}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("[7day] #{$booking->order_code}: " . $e->getMessage());
            }
        }

        // ── Wave 2: 1 ngày trước (dùng lại reminder_sent_at hiện có) ──
        $bookings1day = Booking::where('status', 'confirmed')
            ->whereDate('check_in', $tomorrow)
            ->whereNull('reminder_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookings1day as $booking) {
            if (!$booking->email) continue;
            try {
                Mail::to($booking->email)->send(new PreArrivalReminder($booking, '1day'));
                $booking->updateQuietly(['reminder_sent_at' => now()]);
                $this->line("[1day] #{$booking->order_code}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("[1day] #{$booking->order_code}: " . $e->getMessage());
            }
        }

        // ── Wave 3: Sáng ngày check-in (chỉ gửi khi morning_reminder_sent_at là null) ──
        $bookingsMorning = Booking::where('status', 'confirmed')
            ->whereDate('check_in', $today)
            ->whereNull('morning_reminder_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookingsMorning as $booking) {
            if (!$booking->email) continue;
            try {
                Mail::to($booking->email)->send(new PreArrivalReminder($booking, 'morning'));
                $booking->updateQuietly(['morning_reminder_sent_at' => now()]);
                $this->line("[morning] #{$booking->order_code}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("[morning] #{$booking->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Đã gửi {$sent} email nhắc nhở check-in.");
    }
}
