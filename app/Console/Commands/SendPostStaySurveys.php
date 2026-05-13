<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPostStaySurveys extends Command
{
    protected $signature   = 'staygo:post-stay-surveys';
    protected $description = 'Gửi email khảo sát sau khi khách trả phòng (1 ngày sau check-out)';

    public function handle(): void
    {
        $yesterday    = now()->subDay()->toDateString();
        $cutoffDate   = now()->subDays(7)->toDateString();

        $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereDate('check_out', '<=', $yesterday)
            ->whereDate('check_out', '>=', $cutoffDate)
            ->whereNull('survey_sent_at')
            ->with('room.hotel')
            ->get();

        foreach ($bookings as $booking) {
            if (!$booking->email) continue;
            try {
                Mail::to($booking->email)->send(new \App\Mail\PostStaySurvey($booking));
                $booking->updateQuietly(['survey_sent_at' => now()]);
                $this->line("Sent survey for #{$booking->order_code}");
            } catch (\Exception $e) {
                $this->error("Failed #{$booking->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$bookings->count()} post-stay survey(s).");
    }
}
