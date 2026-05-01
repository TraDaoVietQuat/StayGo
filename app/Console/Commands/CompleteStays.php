<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CompleteStays extends Command
{
    protected $signature   = 'staygo:complete-stays';
    protected $description = 'Tự động chuyển booking sang completed sau ngày check-out';

    public function handle(): void
    {
        $updated = Booking::where('status', 'confirmed')
            ->whereDate('check_out', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        $this->info("Completed {$updated} booking(s).");
    }
}
