<?php

namespace App\Console\Commands;

use App\Mail\BookingExpired;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpirePendingBookings extends Command
{
    protected $signature   = 'staygo:expire-pending-bookings';
    protected $description = 'Tự động huỷ booking pending chưa thanh toán (online: 30 phút, offline/bank: 24 giờ)';

    // Phương thức thanh toán offline — SePay xác nhận tự động, không cancel sớm
    private const OFFLINE_METHODS = ['bank', 'bank_transfer', 'hotel', 'cod'];

    public function handle(): void
    {
        // 1. Online payment (VNPay, MoMo, ZaloPay): cancel sau 30 phút
        $onlineExpired = Booking::where('status', 'pending')
            ->whereNotIn('payment_method', self::OFFLINE_METHODS)
            ->where('created_at', '<', now()->subMinutes(30))
            ->with('room.hotel')
            ->get();

        // 2. Offline/bank transfer: cancel sau 24 giờ (SePay webhook có thể đến muộn)
        $offlineExpired = Booking::where('status', 'pending')
            ->whereIn('payment_method', self::OFFLINE_METHODS)
            ->where('created_at', '<', now()->subHours(24))
            ->with('room.hotel')
            ->get();

        $all = $onlineExpired->merge($offlineExpired);

        foreach ($all as $booking) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->email) {
                try {
                    Mail::to($booking->email)->send(new BookingExpired($booking));
                } catch (\Exception) {}
            }
        }

        $this->info("Cancelled {$onlineExpired->count()} online + {$offlineExpired->count()} offline expired booking(s).");
    }
}
