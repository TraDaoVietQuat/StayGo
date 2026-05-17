<?php

namespace App\Filament\HotelPartner\Pages;

use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerMonthlyReport extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Báo cáo tháng';
    protected static ?string $title           = 'Báo cáo hiệu suất tháng';
    protected static ?int    $navigationSort  = 10;

    protected static string $view = 'filament.hotel-partner.pages.partner-monthly-report';

    // ---- Livewire state ----
    public int    $reportMonth;
    public int    $reportYear;
    public bool   $generating  = false;
    public bool   $reportReady = false;

    // ---- Computed data (populated on generate) ----
    public array  $kpis         = [];
    public array  $prevKpis     = [];
    public array  $roomRows     = [];
    public array  $reviewStats  = [];
    public array  $guestSource  = [];
    public array  $aiSections   = [];   // parsed JSON from AI
    public string $aiRaw        = '';   // fallback if JSON parse fails
    public string $hotelName    = '';
    public float  $commissionRate = 0;

    public function mount(): void
    {
        $last = Carbon::now()->subMonth();
        $this->reportMonth = (int) $last->format('m');
        $this->reportYear  = (int) $last->format('Y');
    }

    // -----------------------------------------------------------------------
    // Main action
    // -----------------------------------------------------------------------

    public function generateReport(): void
    {
        $this->generating  = true;
        $this->reportReady = false;
        $this->aiSections  = [];
        $this->aiRaw       = '';

        $user  = auth('hotel_partner')->user();
        $hotel = $user?->managedHotel;

        if (!$hotel) {
            $this->generating = false;
            return;
        }

        $this->hotelName      = $hotel->name;
        $this->commissionRate = (float) ($user->partnerProfile?->commission_rate ?? 0);

        [$start, $end]          = $this->bounds($this->reportYear, $this->reportMonth);
        [$prevStart, $prevEnd]  = $this->prevBounds($this->reportYear, $this->reportMonth);

        $this->kpis        = $this->computeKpis($hotel, $start, $end);
        $this->prevKpis    = $this->computeKpis($hotel, $prevStart, $prevEnd);
        $this->roomRows    = $this->computeRoomBreakdown($hotel, $start, $end);
        $this->reviewStats = $this->computeReviewStats($hotel->id, $start, $end);
        $this->guestSource = $this->computeGuestSource($hotel, $start, $end);

        // AI narrative
        $prompt  = $this->buildAiPrompt($hotel);
        $aiText  = $this->callAi($prompt);

        if ($aiText) {
            // Try extract JSON block
            preg_match('/\{[\s\S]+\}/m', $aiText, $m);
            $jsonStr = $m[0] ?? $aiText;
            $decoded = @json_decode($jsonStr, true);

            if (is_array($decoded) && isset($decoded['executive_summary'])) {
                $this->aiSections = $decoded;
            } else {
                $this->aiRaw = $aiText;
            }
        }

        $this->generating  = false;
        $this->reportReady = true;
    }

    // -----------------------------------------------------------------------
    // Period helpers
    // -----------------------------------------------------------------------

    private function bounds(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();
        return [$start, $end];
    }

    private function prevBounds(int $year, int $month): array
    {
        $prev = Carbon::create($year, $month, 1)->subMonth();
        return $this->bounds((int) $prev->format('Y'), (int) $prev->format('m'));
    }

    // -----------------------------------------------------------------------
    // KPI computation
    // -----------------------------------------------------------------------

    private function computeKpis($hotel, Carbon $start, Carbon $end): array
    {
        $roomIds      = $hotel->rooms()->pluck('rooms.id');
        $totalRooms   = (int) $hotel->rooms()->sum('quantity');
        $daysInPeriod = (int) $start->daysInMonth;

        $bookings = Booking::whereIn('room_id', $roomIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('created_at', [$start, $end]);

        $totalBookings   = (clone $bookings)->count();
        $grossRevenue    = (float) (clone $bookings)->sum('total_price');
        $roomNightsSold  = (int)   (clone $bookings)
            ->selectRaw('SUM(DATEDIFF(check_out, check_in)) as nights')
            ->value('nights');

        $allBookings     = Booking::whereIn('room_id', $roomIds)->whereBetween('created_at', [$start, $end]);
        $totalAll        = (clone $allBookings)->count();
        $cancelledCount  = (clone $allBookings)->where('status', 'cancelled')->count();
        $cancellationRate = $totalAll > 0 ? round($cancelledCount / $totalAll * 100, 1) : 0;

        $maxNights      = $totalRooms * $daysInPeriod;
        $occupancyRate  = $maxNights > 0 ? round($roomNightsSold / $maxNights * 100, 1) : 0;
        $adr            = $roomNightsSold > 0 ? round($grossRevenue / $roomNightsSold) : 0;
        $revpar         = round($adr * $occupancyRate / 100);

        $otaCommission  = round($grossRevenue * $this->commissionRate / 100);
        $netRevenue     = $grossRevenue - $otaCommission;

        $avgLeadTime = round(
            Booking::whereIn('room_id', $roomIds)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('AVG(DATEDIFF(check_in, DATE(created_at))) as avg_lead')
                ->value('avg_lead') ?? 0,
            1
        );

        $avgRating = round(
            (float) Review::where('hotel_id', $hotel->id)
                ->where('is_active', true)
                ->whereBetween('created_at', [$start, $end])
                ->avg('rating'),
            2
        );

        return compact(
            'totalBookings', 'roomNightsSold', 'occupancyRate', 'adr', 'revpar',
            'grossRevenue', 'otaCommission', 'netRevenue', 'cancellationRate',
            'avgRating', 'avgLeadTime', 'maxNights', 'totalRooms', 'daysInPeriod'
        );
    }

    // -----------------------------------------------------------------------
    // Room breakdown
    // -----------------------------------------------------------------------

    private function computeRoomBreakdown($hotel, Carbon $start, Carbon $end): array
    {
        $rows = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->where('rooms.hotel_id', $hotel->id)
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->whereBetween('bookings.created_at', [$start, $end])
            ->select(
                'rooms.room_name',
                'rooms.quantity',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(DATEDIFF(bookings.check_out, bookings.check_in)) as nights_sold'),
                DB::raw('AVG(bookings.total_price / NULLIF(DATEDIFF(bookings.check_out, bookings.check_in), 0)) as avg_night_price'),
                DB::raw('SUM(bookings.total_price) as revenue')
            )
            ->groupBy('rooms.id', 'rooms.room_name', 'rooms.quantity')
            ->orderByDesc('revenue')
            ->get();

        $daysInMonth = (int) $start->daysInMonth;
        return $rows->map(function ($r) use ($daysInMonth) {
            $maxNights   = (int) $r->quantity * $daysInMonth;
            $occupancy   = $maxNights > 0 ? round($r->nights_sold / $maxNights * 100, 1) : 0;
            return [
                'name'          => $r->room_name,
                'nights_sold'   => (int) $r->nights_sold,
                'occupancy'     => $occupancy,
                'avg_price'     => (int) round($r->avg_night_price),
                'revenue'       => (float) $r->revenue,
                'booking_count' => (int) $r->booking_count,
            ];
        })->toArray();
    }

    // -----------------------------------------------------------------------
    // Review stats
    // -----------------------------------------------------------------------

    private function computeReviewStats(int $hotelId, Carbon $start, Carbon $end): array
    {
        $reviews = Review::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->whereBetween('created_at', [$start, $end]);

        $total        = (clone $reviews)->count();
        $avgRating    = round((float) (clone $reviews)->avg('rating'), 2);
        $fiveStars    = (clone $reviews)->where('rating', 5)->count();
        $fourStars    = (clone $reviews)->where('rating', 4)->count();
        $threeStars   = (clone $reviews)->where('rating', 3)->count();
        $lowStars     = (clone $reviews)->where('rating', '<=', 2)->count();

        $fivePct      = $total > 0 ? round($fiveStars  / $total * 100) : 0;
        $lowPct       = $total > 0 ? round($lowStars    / $total * 100) : 0;
        $unresponded  = (clone $reviews)->whereNull('partner_reply')->count();

        // Sample notable comments (positive + negative)
        $bestComment  = (clone $reviews)->where('rating', 5)->latest('created_at')->value('comment');
        $worstComment = (clone $reviews)->where('rating', '<=', 2)->latest('created_at')->value('comment');

        return compact(
            'total', 'avgRating', 'fiveStars', 'fourStars', 'threeStars', 'lowStars',
            'fivePct', 'lowPct', 'unresponded', 'bestComment', 'worstComment'
        );
    }

    // -----------------------------------------------------------------------
    // Guest source (estimate from phone prefix)
    // -----------------------------------------------------------------------

    private function computeGuestSource($hotel, Carbon $start, Carbon $end): array
    {
        $roomIds = $hotel->rooms()->pluck('rooms.id');

        $bookings = Booking::whereIn('room_id', $roomIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->get(['phone', 'user_id']);

        $total        = $bookings->count();
        $domestic     = $bookings->filter(fn($b) => $this->isDomestic($b->phone))->count();
        $international = $total - $domestic;

        $domesticPct      = $total > 0 ? round($domestic     / $total * 100) : 0;
        $internationalPct = $total > 0 ? round($international / $total * 100) : 0;
        $guestCount       = $bookings->whereNull('user_id')->count(); // no account

        return compact('total', 'domestic', 'international', 'domesticPct', 'internationalPct', 'guestCount');
    }

    private function isDomestic(?string $phone): bool
    {
        if (!$phone) return true;
        $clean = preg_replace('/\s+/', '', $phone);
        return str_starts_with($clean, '0') || str_starts_with($clean, '+84') || str_starts_with($clean, '84');
    }

    // -----------------------------------------------------------------------
    // AI narrative
    // -----------------------------------------------------------------------

    private function buildAiPrompt($hotel): string
    {
        $cur   = $this->kpis;
        $prev  = $this->prevKpis;
        $rev   = $this->reviewStats;
        $guest = $this->guestSource;

        $monthLabel = Carbon::create($this->reportYear, $this->reportMonth, 1)
            ->locale('vi')->isoFormat('MMMM YYYY');

        $pct = fn($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) . '%' : ($c > 0 ? '+100%' : '0%');
        $arr = fn($c, $p) => $c > $p ? '↑' : ($c < $p ? '↓' : '→');

        $roomSummary = collect($this->roomRows)->map(fn($r) =>
            "- {$r['name']}: {$r['nights_sold']} đêm | Occupancy {$r['occupancy']}% | Giá TB " . number_format($r['avg_price']) . " VNĐ | Doanh thu " . number_format($r['revenue']) . " VNĐ"
        )->implode("\n");

        $nextMonth = Carbon::create($this->reportYear, $this->reportMonth, 1)->addMonth();
        $nextMonthLabel = $nextMonth->locale('vi')->isoFormat('MMMM YYYY');
        $nextMonthNum   = $nextMonth->month;

        $vietnameseHolidays = $this->getVietnameseEvents($nextMonthNum);

        return <<<PROMPT
Bạn là chuyên gia tư vấn quản lý khách sạn OTA tại Việt Nam. Hãy phân tích dữ liệu và tạo báo cáo nội bộ cho khách sạn "{$hotel->name}".

=== DỮ LIỆU THÁNG {$monthLabel} ===
[KPI CHÍNH]
Tổng booking: {$cur['totalBookings']} ({$arr($cur['totalBookings'], $prev['totalBookings'])} {$pct($cur['totalBookings'], $prev['totalBookings'])} so tháng trước)
Phòng-đêm bán: {$cur['roomNightsSold']} / {$cur['maxNights']} có thể bán
Occupancy Rate: {$cur['occupancyRate']}% ({$arr($cur['occupancyRate'], $prev['occupancyRate'])} {$pct($cur['occupancyRate'], $prev['occupancyRate'])}) — mục tiêu >70%
ADR: {$cur['adr']} VNĐ ({$arr($cur['adr'], $prev['adr'])} {$pct($cur['adr'], $prev['adr'])})
RevPAR: {$cur['revpar']} VNĐ ({$arr($cur['revpar'], $prev['revpar'])})
Doanh thu gross: {$cur['grossRevenue']} VNĐ ({$arr($cur['grossRevenue'], $prev['grossRevenue'])} {$pct($cur['grossRevenue'], $prev['grossRevenue'])})
Hoa hồng OTA ({$this->commissionRate}%): {$cur['otaCommission']} VNĐ
Doanh thu thực nhận: {$cur['netRevenue']} VNĐ
Tỷ lệ hủy phòng: {$cur['cancellationRate']}% ({$arr($cur['cancellationRate'], $prev['cancellationRate'])}) — mục tiêu <15%
Lead time trung bình: {$cur['avgLeadTime']} ngày

[PHÂN TÍCH THEO PHÒNG]
{$roomSummary}

[ĐÁNH GIÁ]
Tổng đánh giá: {$rev['total']} | Rating TB: {$rev['avgRating']}/5
5 sao: {$rev['fivePct']}% | 1-2 sao: {$rev['lowPct']}%
Chưa phản hồi: {$rev['unresponded']}

[NGUỒN KHÁCH]
Nội địa: {$guest['domesticPct']}% ({$guest['domestic']} đơn) | Quốc tế: {$guest['internationalPct']}% ({$guest['international']} đơn)

[SỰ KIỆN THÁNG TỚI — {$nextMonthLabel}]
{$vietnameseHolidays}

=== YÊU CẦU ===
Hãy trả về CHÍNH XÁC JSON sau (không thêm text ngoài JSON):
{
  "executive_summary": "3-4 câu highlight quan trọng nhất: thành tích nổi bật, thách thức chính, điểm cần chú ý tháng tới",
  "strengths": [
    "điểm mạnh cụ thể 1 dựa trên dữ liệu",
    "điểm mạnh cụ thể 2 dựa trên dữ liệu",
    "điểm mạnh cụ thể 3 dựa trên dữ liệu"
  ],
  "improvements": [
    {"issue": "vấn đề cụ thể 1", "suggestion": "gợi ý cải thiện cụ thể, có thể làm ngay"},
    {"issue": "vấn đề cụ thể 2", "suggestion": "gợi ý cải thiện cụ thể"},
    {"issue": "vấn đề cụ thể 3", "suggestion": "gợi ý cải thiện cụ thể"}
  ],
  "action_plan": [
    {"action": "hành động cụ thể 1", "deadline": "ngày/tháng hoặc 'Tuần 1'"},
    {"action": "hành động cụ thể 2", "deadline": "ngày/tháng hoặc 'Tuần 2'"},
    {"action": "hành động cụ thể 3", "deadline": "ngày/tháng hoặc 'Cuối tháng'"}
  ],
  "next_month_events": [
    "Sự kiện/dịp đặc biệt 1 → cơ hội cụ thể (tăng giá bao nhiêu % hoặc chạy chương trình gì)",
    "Sự kiện/dịp đặc biệt 2 → cơ hội cụ thể"
  ]
}
PROMPT;
    }

    private function getVietnameseEvents(int $month): string
    {
        $events = [
            1  => "Tết Dương lịch (1/1), chuẩn bị Tết Nguyên Đán (cuối tháng/đầu tháng 2)",
            2  => "Tết Nguyên Đán — cao điểm du lịch lớn nhất năm, Lễ hội mùa xuân",
            3  => "Quốc tế Phụ nữ 8/3, du lịch mùa Xuân, thời tiết đẹp miền Bắc",
            4  => "Lễ Giỗ Tổ Hùng Vương (10/3 âm), Ngày Giải phóng 30/4, kỳ nghỉ lễ 4-7 ngày",
            5  => "Ngày Quốc tế Lao động 1/5, kỳ nghỉ lễ 30/4-1/5 (5-7 ngày) — cao điểm hè sớm",
            6  => "Bắt đầu hè, học sinh nghỉ hè, mùa du lịch biển cao điểm",
            7  => "Cao điểm hè, du lịch gia đình, lễ hội địa phương",
            8  => "Cao điểm hè kết thúc, Rằm tháng 7 (lễ Vu Lan)",
            9  => "Quốc khánh 2/9 (3-4 ngày nghỉ) — cao điểm ngắn, mùa hè kết thúc",
            10 => "Thu hoạch, du lịch mùa thu, thời tiết đẹp, ít cao điểm",
            11 => "Du lịch cuối năm bắt đầu, thời tiết dễ chịu nhiều vùng",
            12 => "Giáng Sinh 24-25/12, Tết Dương lịch 31/12-1/1, cao điểm cuối năm",
        ];
        return $events[$month] ?? 'Không có sự kiện đặc biệt lớn';
    }

    // -----------------------------------------------------------------------
    // AI helper
    // -----------------------------------------------------------------------

    private function callAi(string $prompt): string
    {
        // OpenRouter (primary)
        $orKey = config('services.openrouter.api_key');
        if ($orKey) {
            try {
                $res = Http::timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $orKey,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Monthly Report',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful()) {
                    return trim($res->json('choices.0.message.content') ?? '');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerReport OpenRouter failed: ' . $e->getMessage());
            }
        }

        // OpenAI (fallback)
        $oaKey = config('services.openai.api_key');
        if ($oaKey) {
            try {
                $res = Http::timeout(60)
                    ->withToken($oaKey)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful()) {
                    return trim($res->json('choices.0.message.content') ?? '');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerReport OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return '';
    }

    // -----------------------------------------------------------------------
    // Helpers for view
    // -----------------------------------------------------------------------

    public function getMonthOptions(): array
    {
        $opts = [];
        for ($m = 1; $m <= 12; $m++) {
            $opts[$m] = "Tháng $m";
        }
        return $opts;
    }

    public function getYearOptions(): array
    {
        $cur = (int) date('Y');
        return array_combine(range($cur - 2, $cur), range($cur - 2, $cur));
    }

    public function getReportTitle(): string
    {
        return sprintf('%02d/%d', $this->reportMonth, $this->reportYear);
    }

    private function fmt(float $n): string
    {
        if ($n >= 1_000_000_000) return number_format($n / 1_000_000_000, 2) . ' tỷ';
        if ($n >= 1_000_000)     return number_format($n / 1_000_000, 1) . ' triệu';
        return number_format($n, 0, ',', '.');
    }
}
