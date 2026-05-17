<?php

namespace App\Console\Commands;

use App\Mail\PartnerKpiAlert;
use App\Models\Booking;
use App\Models\Dispute;
use App\Models\Hotel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckPartnerKpiAlerts extends Command
{
    protected $signature   = 'staygo:check-partner-kpi-alerts';
    protected $description = 'E-08: Kiểm tra KPI của từng khách sạn có partner, gửi cảnh báo nếu vượt ngưỡng (yellow/red)';

    // Yellow thresholds
    private const WARN_CANCEL_RATE    = 15.0;
    private const WARN_RATING         = 3.5;
    private const WARN_DISPUTES       = 2;

    // Red thresholds
    private const CRIT_CANCEL_RATE    = 30.0;
    private const CRIT_RATING         = 3.0;
    private const CRIT_DISPUTES       = 5;

    public function handle(): int
    {
        $window    = now()->subDays(30);
        $hotels    = Hotel::with('partnerUser')->whereNotNull('partner_user_id')->get();
        $alertSent = 0;

        foreach ($hotels as $hotel) {
            $partner = $hotel->partnerUser;
            if (!$partner?->email) {
                continue;
            }

            $roomIds = $hotel->rooms()->pluck('id');

            // Cancellation rate (last 30 days)
            $totalBookings  = Booking::whereIn('room_id', $roomIds)
                ->where('created_at', '>=', $window)->count();
            $cancelledCount = Booking::whereIn('room_id', $roomIds)
                ->where('created_at', '>=', $window)
                ->where('status', 'cancelled')->count();

            $cancelRate = $totalBookings > 0 ? ($cancelledCount / $totalBookings) * 100 : 0;

            // Rating (from pre-computed hotel column)
            $rating = (float) ($hotel->rating ?? 5.0);

            // Open disputes (last 30 days)
            $openDisputes = Dispute::where('hotel_id', $hotel->id)
                ->where('created_at', '>=', $window)
                ->whereNotIn('status', ['resolved', 'closed'])->count();

            $kpis = [
                'cancellation_rate' => round($cancelRate, 1),
                'rating'            => $rating,
                'open_disputes'     => $openDisputes,
            ];

            // Build issues array and determine level
            $issues = [];
            $level  = null;

            // Critical checks (red)
            if ($cancelRate > self::CRIT_CANCEL_RATE) {
                $issues[] = [
                    'metric'    => 'Tỷ lệ hủy phòng',
                    'value'     => number_format($cancelRate, 1) . '%',
                    'threshold' => '≤ ' . self::CRIT_CANCEL_RATE . '%',
                    'tip'       => 'Tỷ lệ hủy quá cao. Hãy đảm bảo phòng luôn sẵn sàng và cập nhật lịch chính xác. Xem xét chính sách hủy linh hoạt hơn.',
                ];
                $level = 'red';
            } elseif ($cancelRate > self::WARN_CANCEL_RATE) {
                $issues[] = [
                    'metric'    => 'Tỷ lệ hủy phòng',
                    'value'     => number_format($cancelRate, 1) . '%',
                    'threshold' => '≤ ' . self::WARN_CANCEL_RATE . '%',
                    'tip'       => 'Tỷ lệ hủy đang tăng. Kiểm tra lại tình trạng phòng và xác nhận booking kịp thời.',
                ];
                $level = $level ?? 'yellow';
            }

            if ($rating < self::CRIT_RATING && $hotel->rating !== null) {
                $issues[] = [
                    'metric'    => 'Điểm đánh giá khách hàng',
                    'value'     => number_format($rating, 1) . ' ★',
                    'threshold' => '≥ ' . self::CRIT_RATING . ' ★',
                    'tip'       => 'Điểm đánh giá ở mức thấp nghiêm trọng. Hãy chủ động phản hồi đánh giá và cải thiện chất lượng dịch vụ ngay lập tức.',
                ];
                $level = 'red';
            } elseif ($rating < self::WARN_RATING && $hotel->rating !== null) {
                $issues[] = [
                    'metric'    => 'Điểm đánh giá khách hàng',
                    'value'     => number_format($rating, 1) . ' ★',
                    'threshold' => '≥ ' . self::WARN_RATING . ' ★',
                    'tip'       => 'Điểm đánh giá dưới mức tốt. Phản hồi đánh giá khách hàng và cải thiện trải nghiệm check-in/checkout.',
                ];
                $level = $level ?? 'yellow';
            }

            if ($openDisputes > self::CRIT_DISPUTES) {
                $issues[] = [
                    'metric'    => 'Tranh chấp chưa giải quyết',
                    'value'     => $openDisputes . ' vụ',
                    'threshold' => '≤ ' . self::CRIT_DISPUTES . ' vụ',
                    'tip'       => 'Quá nhiều tranh chấp mở. Hãy xử lý từng vụ trên Partner Dashboard và liên hệ StayGo để được hỗ trợ.',
                ];
                $level = 'red';
            } elseif ($openDisputes > self::WARN_DISPUTES) {
                $issues[] = [
                    'metric'    => 'Tranh chấp chưa giải quyết',
                    'value'     => $openDisputes . ' vụ',
                    'threshold' => '≤ ' . self::WARN_DISPUTES . ' vụ',
                    'tip'       => 'Có tranh chấp đang chờ xử lý. Vào Partner Dashboard để phản hồi và giải quyết kịp thời.',
                ];
                $level = $level ?? 'yellow';
            }

            // No issues — hotel is healthy
            if (empty($issues) || $level === null) {
                continue;
            }

            try {
                Mail::to($partner->email)->send(new PartnerKpiAlert($hotel, $level, $issues, $kpis));
                $alertSent++;
                $this->line("✓ [{$level}] Gửi KPI alert → {$partner->email} ({$hotel->name})");
            } catch (\Exception $e) {
                $this->error("{$hotel->name}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn thành: đã gửi {$alertSent} KPI alert email.");
        return self::SUCCESS;
    }
}
