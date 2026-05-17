<?php

namespace App\Console\Commands;

use App\Mail\AdminDailyDigest;
use App\Models\Booking;
use App\Models\Dispute;
use App\Models\HotelPartnerProfile;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAdminDailyDigest extends Command
{
    protected $signature   = 'staygo:send-admin-daily-digest';
    protected $description = 'E-10A: Gửi email tóm tắt hoạt động hàng ngày cho admin team lúc 7:00 sáng';

    public function handle(): int
    {
        $yesterday  = now()->subDay()->toDateString();
        $dayBefore  = now()->subDays(2)->toDateString();

        // ---- GMV & bookings ----
        $gmvYesterday = (float) Booking::whereDate('created_at', $yesterday)
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->sum('total_price');

        $gmvDayBefore = (float) Booking::whereDate('created_at', $dayBefore)
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->sum('total_price');

        $bookingsCount   = Booking::whereDate('created_at', $yesterday)->count();
        $bookingsBefore  = Booking::whereDate('created_at', $dayBefore)->count();
        $cancelledCount  = Booking::whereDate('created_at', $yesterday)->where('status', 'cancelled')->count();

        $gmvChange      = $gmvDayBefore > 0 ? round(($gmvYesterday - $gmvDayBefore) / $gmvDayBefore * 100, 1) : 0;
        $bookingsChange = $bookingsBefore > 0 ? round(($bookingsCount - $bookingsBefore) / $bookingsBefore * 100, 1) : 0;
        $cancelRate     = $bookingsCount > 0 ? round($cancelledCount / $bookingsCount * 100, 1) : 0;

        // ---- Pending bookings (chờ thanh toán) ----
        $pendingCount = Booking::where('status', 'pending')->count();

        // ---- New complaints / disputes ----
        $complaintsNew = Dispute::whereDate('created_at', $yesterday)->count();

        // ---- Pending partner applications ----
        $pendingPartners = HotelPartnerProfile::where('status', 'pending')->count();

        // ---- Urgent alerts ----
        $urgentAlerts = [];

        $expiredPending = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->count();
        if ($expiredPending > 0) {
            $urgentAlerts[] = [
                'text' => "{$expiredPending} booking đang ở trạng thái pending quá 1 giờ",
                'url'  => url('/admin/bookings?tableFilters[status][value]=pending'),
            ];
        }

        $highDisputes = Dispute::whereIn('status', ['open', 'in_review'])
            ->where('priority', 'high')
            ->count();
        if ($highDisputes > 0) {
            $urgentAlerts[] = [
                'text' => "{$highDisputes} khiếu nại độ ưu tiên CAO chưa xử lý",
                'url'  => url('/admin/disputes'),
            ];
        }

        if ($pendingPartners > 0) {
            $urgentAlerts[] = [
                'text' => "{$pendingPartners} hồ sơ đối tác đang chờ xét duyệt",
                'url'  => url('/admin/hotel-partners'),
            ];
        }

        // ---- Watch items ----
        $watchItems = [];
        if ($cancelRate > 15) {
            $watchItems[] = "Tỷ lệ hủy phòng hôm qua {$cancelRate}% — vượt ngưỡng 15%";
        }
        if ($complaintsNew > 5) {
            $watchItems[] = "{$complaintsNew} khiếu nại mới trong ngày — cao hơn bình thường";
        }

        // ---- Top 5 hotels by revenue ----
        $topHotels = Hotel::query()
            ->join('rooms', 'rooms.hotel_id', '=', 'hotels.id')
            ->join('bookings', 'bookings.room_id', '=', 'rooms.id')
            ->whereDate('bookings.created_at', $yesterday)
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->groupBy('hotels.id', 'hotels.name')
            ->selectRaw('hotels.id, hotels.name, SUM(bookings.total_price) as revenue')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn($h) => ['name' => $h->name, 'revenue' => (float) $h->revenue])
            ->toArray();

        $stats = compact(
            'gmvYesterday', 'gmvChange',
            'bookingsCount', 'bookingsChange',
            'cancelRate', 'pendingCount',
            'complaintsNew', 'pendingPartners',
            'urgentAlerts', 'watchItems', 'topHotels',
        );

        // ---- Send to all admins ----
        $admins = User::where('role', 'admin')->whereNotNull('email')->get();

        if ($admins->isEmpty()) {
            $this->warn('Không tìm thấy admin nào có email.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new AdminDailyDigest($stats));
                $sent++;
                $this->line("✓ Gửi → {$admin->email}");
            } catch (\Exception $e) {
                $this->error("{$admin->email}: " . $e->getMessage());
            }
        }

        $this->info("Daily digest đã gửi tới {$sent}/{$admins->count()} admin.");
        return self::SUCCESS;
    }
}
