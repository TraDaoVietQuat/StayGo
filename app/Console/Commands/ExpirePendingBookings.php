<?php

namespace App\Console\Commands;

use App\Mail\BookingExpired;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpirePendingBookings extends Command
{
    protected $signature   = 'staygo:expire-pending-bookings';
    protected $description = 'Tự động huỷ booking pending chưa thanh toán sau 30 phút';

    public function handle(): void
    {
        $bookings = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(30))
            ->with('room.hotel')
            ->get();

        foreach ($bookings as $booking) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->email) {
                try {
                    Mail::to($booking->email)->send(new BookingExpired($booking));
                } catch (\Exception) {}
            }
        }

        $this->info("Cancelled {$bookings->count()} expired pending booking(s).");
    }
}
