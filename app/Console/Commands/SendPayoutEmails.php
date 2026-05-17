<?php

namespace App\Console\Commands;

use App\Mail\PartnerPayoutStatement;
use App\Models\PartnerPayout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPayoutEmails extends Command
{
    protected $signature   = 'staygo:send-payout-emails';
    protected $description = 'E-07: Gửi email bảng kê thanh toán cho partner khi payout được đánh dấu paid hôm nay';

    public function handle(): int
    {
        $today = now()->toDateString();

        $payouts = PartnerPayout::with(['hotel.partnerUser'])
            ->where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->whereNull('email_sent_at')
            ->get();

        if ($payouts->isEmpty()) {
            $this->info('Không có payout nào cần gửi email hôm nay.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($payouts as $payout) {
            $partner = $payout->hotel?->partnerUser;

            if (!$partner?->email) {
                $this->warn("Payout #{$payout->id}: partner không có email — bỏ qua.");
                continue;
            }

            try {
                Mail::to($partner->email)->send(new PartnerPayoutStatement($payout));
                $payout->update(['email_sent_at' => now()]);
                $sent++;
                $this->line("✓ Gửi thành công → {$partner->email} (Payout #{$payout->id})");
            } catch (\Exception $e) {
                $this->error("Payout #{$payout->id} lỗi: " . $e->getMessage());
            }
        }

        $this->info("Hoàn thành: đã gửi {$sent}/{$payouts->count()} email payout.");
        return self::SUCCESS;
    }
}
