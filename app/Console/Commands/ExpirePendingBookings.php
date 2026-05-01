<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature   = 'staygo:expire-pending-bookings';
    protected $description = 'Tự động huỷ booking pending chưa thanh toán sau 30 phút';

    public function handle(): void
    {
        $expired = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(30))
            ->update(['status' => 'cancelled']);

        $this->info("Cancelled {$expired} expired pending booking(s).");
    }
}
