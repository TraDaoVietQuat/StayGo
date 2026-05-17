<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class BusinessAnalytics extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Phân tích kinh doanh';
    protected static ?string $title           = 'Báo cáo & Phân tích KPI';
    protected static ?string $navigationGroup = 'Công cụ';
    protected static ?int    $navigationSort  = 5;

    protected static string $view = 'filament.pages.business-analytics';

    // ---- Livewire state ----
    public string $period = 'this_month'; // this_month | last_month | last_3m | last_year
    public string $aiReport = '';
    public bool   $aiLoading = false;

    // -----------------------------------------------------------------------
    // Date helpers
    // -----------------------------------------------------------------------

    private function getPeriodDates(): array
    {
        return match ($this->period) {
            'last_month'  => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'last_3m'     => [
                Carbon::now()->subMonths(3)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            'last_year'   => [
                Carbon::now()->subYear()->startOfYear(),
                Carbon::now()->subYear()->endOfYear(),
            ],
            default       => [ // this_month
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfDay(),
            ],
        };
    }

    private function getPrevPeriodDates(): array
    {
        [$start, $end] = $this->getPeriodDates();
        $diff = $start->diffInDays($end) + 1;
        return [
            $start->copy()->subDays($diff),
            $start->copy()->subDay(),
        ];
    }

    // -----------------------------------------------------------------------
    // KPI computation
    // -----------------------------------------------------------------------

    public function getKpis(): array
    {
        [$start, $end]     = $this->getPeriodDates();
        [$pStart, $pEnd]   = $this->getPrevPeriodDates();

        $cur  = $this->computeMetrics($start, $end);
        $prev = $this->computeMetrics($pStart, $pEnd);

        return ['current' => $cur, 'previous' => $prev];
    }

    private function computeMetrics(Carbon $start, Carbon $end): array
    {
        // ---- Revenue ----
        $gmv = Booking::whereBetween('created_at', [$start, $end])
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_price');

        $refundTotal = Booking::whereBetween('created_at', [$start, $end])
            ->where('refund_requested', true)
            ->sum(DB::raw('COALESCE(refund_amount, 0)'));

        $netRevenue = $gmv - $refundTotal;
        $takeRate   = $gmv > 0 ? round($netRevenue / $gmv * 100, 2) : 0;

        // Commission từ partner hotels (ước tính từ commission_rate trung bình)
        $commissionEarned = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->join('hotel_partner_profiles', 'hotels.partner_user_id', '=', 'hotel_partner_profiles.user_id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->whereNotIn('bookings.status', ['cancelled'])
            ->selectRaw('SUM(bookings.total_price * hotel_partner_profiles.commission_rate / 100) as total')
            ->value('total') ?? 0;

        // ---- Bookings ----
        $totalBookings     = Booking::whereBetween('created_at', [$start, $end])->count();
        $confirmedBookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['confirmed', 'completed'])->count();
        $cancelledBookings = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'cancelled')->count();

        $cancellationRate = $totalBookings > 0 ? round($cancelledBookings / $totalBookings * 100, 1) : 0;

        $abv = $totalBookings > 0 ? round($gmv / max($totalBookings, 1)) : 0;

        $avgLeadTime = Booking::whereBetween('created_at', [$start, $end])
            ->whereNotNull('check_in')
            ->selectRaw('AVG(DATEDIFF(check_in, DATE(created_at))) as avg_lead')
            ->value('avg_lead') ?? 0;

        // ---- Users ----
        $newUsers    = User::where('role', 'user')->whereBetween('created_at', [$start, $end])->count();
        $activeUsers = Booking::whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        $repeatUsers = Booking::whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->whereIn('user_id', function ($q) use ($start) {
                $q->select('user_id')->from('bookings')
                    ->where('created_at', '<', $start)->whereNotNull('user_id');
            })
            ->distinct('user_id')->count('user_id');

        $repeatRate = $activeUsers > 0 ? round($repeatUsers / $activeUsers * 100, 1) : 0;

        // ---- Partners / Hotels ----
        $activeHotels  = Hotel::where('is_active', true)->count();
        $newHotels     = Hotel::whereBetween('created_at', [$start, $end])->count();
        $avgRating     = round((float) Hotel::where('is_active', true)->whereNotNull('rating')->avg('rating'), 2);
        $hotelsAtRisk  = Hotel::where('is_active', true)
            ->where(fn($q) => $q->where('rating', '<', 3.5)->orWhereNull('rating'))
            ->count();

        // ---- Top Hotels ----
        $topHotels = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->whereNotIn('bookings.status', ['cancelled'])
            ->select('hotels.name', 'hotels.stars',
                DB::raw('SUM(bookings.total_price) as revenue'),
                DB::raw('COUNT(bookings.id) as bookings_count'))
            ->groupBy('hotels.id', 'hotels.name', 'hotels.stars')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // ---- Top Destinations ----
        $topDestinations = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->join('locations', 'hotels.location_id', '=', 'locations.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->whereNotIn('bookings.status', ['cancelled'])
            ->select('locations.name as destination',
                DB::raw('SUM(bookings.total_price) as revenue'),
                DB::raw('COUNT(bookings.id) as bookings_count'))
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // ---- NPS approximation từ reviews ----
        $npsData = Review::whereBetween('created_at', [$start, $end])
            ->whereNotNull('rating')
            ->selectRaw('
                SUM(CASE WHEN rating >= 9 THEN 1 ELSE 0 END) as promoters,
                SUM(CASE WHEN rating <= 6 THEN 1 ELSE 0 END) as detractors,
                COUNT(*) as total,
                AVG(rating) as avg_rating
            ')->first();

        $npsScore = 0;
        if ($npsData && $npsData->total > 0) {
            // Chuyển đổi rating 1-5 sang NPS: 5=promoter, 4=passive, ≤3=detractor
            $promoters  = Review::whereBetween('created_at', [$start, $end])->where('rating', 5)->count();
            $detractors = Review::whereBetween('created_at', [$start, $end])->where('rating', '<=', 3)->count();
            $total      = $npsData->total;
            $npsScore   = $total > 0 ? round(($promoters - $detractors) / $total * 100) : 0;
        }

        $avgReviewRating = round((float) ($npsData?->avg_rating ?? 0), 2);

        return compact(
            'gmv', 'netRevenue', 'takeRate', 'commissionEarned',
            'totalBookings', 'confirmedBookings', 'cancelledBookings',
            'cancellationRate', 'abv', 'avgLeadTime',
            'newUsers', 'activeUsers', 'repeatRate',
            'activeHotels', 'newHotels', 'avgRating', 'hotelsAtRisk',
            'topHotels', 'topDestinations', 'npsScore', 'avgReviewRating',
            'refundTotal'
        );
    }

    // -----------------------------------------------------------------------
    // AI Analysis
    // -----------------------------------------------------------------------

    public function generateAiReport(): void
    {
        $this->aiLoading = true;
        $this->aiReport  = '';

        $kpis    = $this->getKpis();
        $cur     = $kpis['current'];
        $prev    = $kpis['previous'];

        $pct = fn($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100 : 0);
        $arr = fn($c, $p) => $c > $p ? '↑' : ($c < $p ? '↓' : '→');

        $periodLabel = match ($this->period) {
            'last_month' => 'Tháng trước',
            'last_3m'    => '3 tháng gần đây',
            'last_year'  => 'Năm ngoái',
            default      => 'Tháng này',
        };

        $prompt = "Bạn là chuyên viên phân tích dữ liệu kinh doanh của nền tảng OTA StayGo (đặt phòng khách sạn & resort Việt Nam).\n\n"
            . "KỲ BÁO CÁO: {$periodLabel}\n\n"
            . "=== DỮ LIỆU KPI HIỆN TẠI ===\n"
            . "[DOANH THU]\n"
            . "• GMV: " . number_format($cur['gmv']) . " VNĐ  {$arr($cur['gmv'], $prev['gmv'])} " . $pct($cur['gmv'], $prev['gmv']) . "% so kỳ trước\n"
            . "• Net Revenue: " . number_format($cur['netRevenue']) . " VNĐ\n"
            . "• Take Rate: {$cur['takeRate']}% (mục tiêu 12-18%)\n"
            . "• Commission Earned: " . number_format($cur['commissionEarned']) . " VNĐ\n"
            . "• Hoàn tiền: " . number_format($cur['refundTotal']) . " VNĐ\n\n"
            . "[HOẠT ĐỘNG ĐẶT PHÒNG]\n"
            . "• Total Bookings: {$cur['totalBookings']} {$arr($cur['totalBookings'], $prev['totalBookings'])} " . $pct($cur['totalBookings'], $prev['totalBookings']) . "%\n"
            . "• Confirmed: {$cur['confirmedBookings']}\n"
            . "• Cancelled: {$cur['cancelledBookings']}\n"
            . "• Cancellation Rate: {$cur['cancellationRate']}% (cảnh báo nếu >15%)\n"
            . "• ABV: " . number_format($cur['abv']) . " VNĐ\n"
            . "• Lead Time TB: " . round($cur['avgLeadTime'], 1) . " ngày\n\n"
            . "[NGƯỜI DÙNG]\n"
            . "• New Users: {$cur['newUsers']} {$arr($cur['newUsers'], $prev['newUsers'])} " . $pct($cur['newUsers'], $prev['newUsers']) . "%\n"
            . "• Active Users: {$cur['activeUsers']}\n"
            . "• Repeat Booking Rate: {$cur['repeatRate']}% (mục tiêu >40%)\n"
            . "• NPS Score: {$cur['npsScore']}\n"
            . "• Avg Review Rating: {$cur['avgReviewRating']}/5\n\n"
            . "[ĐỐI TÁC KHÁCH SẠN]\n"
            . "• Active Hotels: {$cur['activeHotels']}\n"
            . "• New Hotels: {$cur['newHotels']} {$arr($cur['newHotels'], $prev['newHotels'])}\n"
            . "• Avg Rating: {$cur['avgRating']}/5 (mục tiêu ≥4.2)\n"
            . "• Hotels at Risk (rating <3.5): {$cur['hotelsAtRisk']}\n\n"
            . "TOP 5 KHÁCH SẠN DOANH THU CAO NHẤT:\n"
            . collect($cur['topHotels'])->map(fn($h, $i) =>
                ($i + 1) . ". {$h->name}: " . number_format($h->revenue) . " VNĐ ({$h->bookings_count} đặt phòng)"
            )->implode("\n") . "\n\n"
            . "TOP 5 ĐIỂM ĐẾN:\n"
            . collect($cur['topDestinations'])->map(fn($d, $i) =>
                ($i + 1) . ". {$d->destination}: " . number_format($d->revenue) . " VNĐ ({$d->bookings_count} đặt phòng)"
            )->implode("\n") . "\n\n"
            . "Hãy phân tích toàn diện và lập báo cáo CHÍNH XÁC theo format:\n\n"
            . "## 1. EXECUTIVE SUMMARY\n(5-7 dòng highlight quan trọng nhất, dùng ký hiệu ↑↓→)\n\n"
            . "## 2. SO SÁNH KỲ TRƯỚC (MoM)\n(Bảng so sánh các chỉ số, đánh giá tích cực/tiêu cực)\n\n"
            . "## 3. TOP ĐIỂM ĐẾN & KHÁCH SẠN\n(Phân tích top 5, nhận xét xu hướng)\n\n"
            . "## 4. XU HƯỚNG & BẤT THƯỜNG\n(Phát hiện anomaly, pattern đáng chú ý)\n\n"
            . "## 5. CẢNH BÁO\n🔴 Cảnh báo đỏ (cần hành động ngay):\n🟡 Cảnh báo vàng (cần theo dõi):\n\n"
            . "## 6. ĐỀ XUẤT HÀNH ĐỘNG\n(3-5 action items cụ thể, ưu tiên cao → thấp)";

        try {
            $key = config('services.openrouter.api_key');
            if ($key) {
                $res = Http::withToken($key)->timeout(90)
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);
                if ($res->successful() && isset($res['choices'][0]['message']['content'])) {
                    $this->aiReport = $res['choices'][0]['message']['content'];
                    $this->aiLoading = false;
                    return;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenRouter failed in BusinessAnalytics: ' . $e->getMessage());
        }

        try {
            $key2 = config('services.openai.api_key');
            if ($key2) {
                $res2 = Http::withToken($key2)->timeout(90)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);
                if ($res2->successful() && isset($res2['choices'][0]['message']['content'])) {
                    $this->aiReport = $res2['choices'][0]['message']['content'];
                    $this->aiLoading = false;
                    return;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI failed in BusinessAnalytics: ' . $e->getMessage());
        }

        $this->aiReport  = 'Không thể kết nối AI. Vui lòng thử lại.';
        $this->aiLoading = false;
    }

    // -----------------------------------------------------------------------
    // Alert computation
    // -----------------------------------------------------------------------

    public function getAlerts(array $cur): array
    {
        $red    = [];
        $yellow = [];

        if ($cur['cancellationRate'] > 15) {
            $red[] = "Tỷ lệ hủy phòng {$cur['cancellationRate']}% — vượt ngưỡng cảnh báo 15%";
        } elseif ($cur['cancellationRate'] > 10) {
            $yellow[] = "Tỷ lệ hủy phòng {$cur['cancellationRate']}% — đang tiệm cận ngưỡng 15%";
        }

        if ($cur['avgRating'] > 0 && $cur['avgRating'] < 4.0) {
            $red[] = "Rating trung bình {$cur['avgRating']}/5 — thấp hơn mục tiêu 4.2";
        } elseif ($cur['avgRating'] > 0 && $cur['avgRating'] < 4.2) {
            $yellow[] = "Rating trung bình {$cur['avgRating']}/5 — sát ngưỡng mục tiêu 4.2";
        }

        if ($cur['hotelsAtRisk'] > 0) {
            $red[] = "{$cur['hotelsAtRisk']} khách sạn có rating <3.5 — cần xem xét đình chỉ";
        }

        if ($cur['takeRate'] < 12 && $cur['gmv'] > 0) {
            $red[] = "Take Rate {$cur['takeRate']}% — thấp hơn mục tiêu 12-18%";
        } elseif ($cur['takeRate'] > 18) {
            $yellow[] = "Take Rate {$cur['takeRate']}% — cao hơn mục tiêu, kiểm tra chính sách giá";
        }

        if ($cur['repeatRate'] < 30) {
            $yellow[] = "Repeat Booking Rate {$cur['repeatRate']}% — thấp hơn mục tiêu 40%, cần chương trình loyalty";
        }

        if ($cur['refundTotal'] > $cur['gmv'] * 0.1 && $cur['gmv'] > 0) {
            $red[] = 'Tổng hoàn tiền vượt 10% GMV — cần kiểm tra chính sách và nguyên nhân hủy phòng';
        }

        return ['red' => $red, 'yellow' => $yellow];
    }
}
