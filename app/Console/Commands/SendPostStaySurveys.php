<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPostStaySurveys extends Command
{
    protected $signature   = 'staygo:post-stay-surveys';
    protected $description = 'Gửi email khảo sát sau checkout và reminder 3 ngày sau nếu chưa đánh giá';

    public function handle(): void
    {
        $sent = 0;

        // ── Email khảo sát đầu tiên: 2h sau check-out (check thực tế: ngày hôm qua) ──
        $yesterday  = now()->subDay()->toDateString();
        $cutoffDate = now()->subDays(7)->toDateString();

        $surveys = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereDate('check_out', '<=', $yesterday)
            ->whereDate('check_out', '>=', $cutoffDate)
            ->whereNull('survey_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($surveys as $booking) {
            if (!$booking->email) continue;
            // Bỏ qua nếu đã có review
            if (Review::where('booking_id', $booking->id)->exists()) {
                $booking->updateQuietly(['survey_sent_at' => now()]);
                continue;
            }
            try {
                Mail::to($booking->email)->send(new \App\Mail\PostStaySurvey($booking));
                $booking->updateQuietly(['survey_sent_at' => now()]);
                $this->line("[survey] #{$booking->order_code}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("[survey] #{$booking->order_code}: " . $e->getMessage());
            }
        }

        // ── Reminder 3 ngày sau survey (nếu chưa review, chưa gửi reminder) ──
        $remindCutoff  = now()->subDays(3)->startOfDay();
        $remindCutoff2 = now()->subDays(10)->startOfDay();

        $reminders = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereNotNull('survey_sent_at')
            ->whereNull('survey_reminder_sent_at')
            ->where('survey_sent_at', '<=', $remindCutoff)
            ->where('survey_sent_at', '>=', $remindCutoff2)
            ->with('room.hotel')
            ->get();

        foreach ($reminders as $booking) {
            if (!$booking->email) continue;
            // Bỏ qua nếu đã có review
            if (Review::where('booking_id', $booking->id)->exists()) {
                $booking->updateQuietly(['survey_reminder_sent_at' => now()]);
                continue;
            }
            try {
                Mail::to($booking->email)->send(new \App\Mail\PostStaySurveyReminder($booking));
                $booking->updateQuietly(['survey_reminder_sent_at' => now()]);
                $this->line("[reminder] #{$booking->order_code}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("[reminder] #{$booking->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Đã gửi {$sent} email survey/reminder.");
    }
}
