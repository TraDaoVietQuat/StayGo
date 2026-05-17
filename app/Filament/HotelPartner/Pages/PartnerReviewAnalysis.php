<?php

namespace App\Filament\HotelPartner\Pages;

use App\Models\Review;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerReviewAnalysis extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Phân tích đánh giá';
    protected static ?string $title           = 'Phân tích xu hướng đánh giá';
    protected static ?string $navigationGroup = 'Đánh giá';
    protected static ?int    $navigationSort  = 2;

    protected static string $view = 'filament.hotel-partner.pages.partner-review-analysis';

    // ---- Livewire state ----
    public string $period    = 'week';  // week | month | quarter
    public string $aiReport  = '';
    public bool   $aiLoading = false;

    // -----------------------------------------------------------------------
    // Data helpers
    // -----------------------------------------------------------------------

    public function getStats(): array
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if (!$hotel) return $this->emptyStats();

        [$start, $end, $prevStart, $prevEnd] = $this->getPeriodBounds();

        $cur  = $this->computePeriodStats($hotel->id, $start, $end);
        $prev = $this->computePeriodStats($hotel->id, $prevStart, $prevEnd);

        return ['current' => $cur, 'previous' => $prev, 'hotel' => $hotel];
    }

    private function getPeriodBounds(): array
    {
        $now = Carbon::now();
        [$start, $end] = match ($this->period) {
            'month'   => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            'quarter' => [$now->copy()->subMonths(3)->startOfMonth(), $now->copy()->endOfDay()],
            default   => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()], // week
        };
        $diff     = $start->diffInDays($end) + 1;
        $prevEnd   = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($diff - 1)->startOfDay();
        return [$start, $end, $prevStart, $prevEnd];
    }

    private function computePeriodStats(int $hotelId, Carbon $start, Carbon $end): array
    {
        $reviews = Review::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->whereBetween('created_at', [$start, $end])
            ->get(['rating', 'comment', 'partner_reply', 'cleanliness', 'service_score', 'location_score', 'value_score']);

        $total       = $reviews->count();
        $avgRating   = $total > 0 ? round($reviews->avg('rating'), 2) : 0;
        $fiveStars   = $reviews->where('rating', 5)->count();
        $fourStars   = $reviews->where('rating', 4)->count();
        $threeStars  = $reviews->where('rating', 3)->count();
        $twoStars    = $reviews->where('rating', 2)->count();
        $oneStar     = $reviews->where('rating', 1)->count();
        $unresponded = $reviews->whereNull('partner_reply')->count();

        $positiveCount = $fiveStars + $fourStars;
        $negativeCount = $twoStars + $oneStar;
        $positiveRate  = $total > 0 ? round($positiveCount / $total * 100, 1) : 0;
        $responseRate  = $total > 0 ? round(($total - $unresponded) / $total * 100, 1) : 0;

        // Sub-score averages
        $withCleanliness = $reviews->whereNotNull('cleanliness');
        $withService     = $reviews->whereNotNull('service_score');
        $withLocation    = $reviews->whereNotNull('location_score');
        $withValue       = $reviews->whereNotNull('value_score');
        $avgCleanliness = $withCleanliness->count() > 0 ? round($withCleanliness->avg('cleanliness'), 2) : null;
        $avgService     = $withService->count()     > 0 ? round($withService->avg('service_score'), 2)   : null;
        $avgLocation    = $withLocation->count()    > 0 ? round($withLocation->avg('location_score'), 2) : null;
        $avgValue       = $withValue->count()       > 0 ? round($withValue->avg('value_score'), 2)       : null;

        // Lấy comments để phân tích keywords (top từ khóa đơn giản)
        $allComments = $reviews->pluck('comment')->filter()->implode(' ');

        // Extract keywords: loại bỏ stopwords, đếm tần suất
        $keywords = $this->extractKeywords($allComments);

        // Các reviews gần nhất cần phản hồi
        $urgentReviews = Review::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->whereNull('partner_reply')
            ->where('rating', '<=', 2)
            ->whereBetween('created_at', [$start, $end])
            ->with('user')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return compact(
            'total', 'avgRating', 'fiveStars', 'fourStars', 'threeStars',
            'twoStars', 'oneStar', 'unresponded', 'positiveRate',
            'responseRate', 'keywords', 'urgentReviews', 'positiveCount', 'negativeCount',
            'avgCleanliness', 'avgService', 'avgLocation', 'avgValue'
        );
    }

    private function extractKeywords(string $text): array
    {
        if (empty($text)) return ['positive' => [], 'negative' => []];

        $positiveWords = [
            'sạch', 'sạch sẽ', 'tốt', 'tuyệt', 'tuyệt vời', 'đẹp', 'thân thiện', 'nhiệt tình',
            'chuyên nghiệp', 'nhanh', 'ngon', 'tiện nghi', 'thoải mái', 'yên tĩnh', 'view đẹp',
            'vị trí đẹp', 'hài lòng', 'thích', 'xuất sắc', 'ổn', 'phòng rộng', 'giá hợp lý',
            'nhân viên tốt', 'dịch vụ tốt', 'bể bơi', 'ăn sáng ngon', 'gần biển',
        ];
        $negativeWords = [
            'bẩn', 'ồn', 'ồn ào', 'chậm', 'kém', 'tệ', 'thất vọng', 'hôi', 'nhỏ', 'cũ',
            'xuống cấp', 'mùi', 'lạnh', 'nóng', 'hỏng', 'đắt', 'chưa hài lòng', 'không tốt',
            'nhân viên thái độ', 'không sạch', 'wifi kém', 'điều hòa', 'nước nóng',
            'chưa được', 'không như mô tả',
        ];

        $text = mb_strtolower($text);
        $positiveFound = [];
        $negativeFound = [];

        foreach ($positiveWords as $w) {
            $count = substr_count($text, $w);
            if ($count > 0) $positiveFound[$w] = $count;
        }
        foreach ($negativeWords as $w) {
            $count = substr_count($text, $w);
            if ($count > 0) $negativeFound[$w] = $count;
        }

        arsort($positiveFound);
        arsort($negativeFound);

        return [
            'positive' => array_slice($positiveFound, 0, 6, true),
            'negative' => array_slice($negativeFound, 0, 6, true),
        ];
    }

    private function emptyStats(): array
    {
        $empty = [
            'total' => 0, 'avgRating' => 0, 'fiveStars' => 0, 'fourStars' => 0,
            'threeStars' => 0, 'twoStars' => 0, 'oneStar' => 0, 'unresponded' => 0,
            'positiveRate' => 0, 'responseRate' => 0, 'keywords' => ['positive'=>[],'negative'=>[]],
            'urgentReviews' => collect(), 'positiveCount' => 0, 'negativeCount' => 0,
            'avgCleanliness' => null, 'avgService' => null, 'avgLocation' => null, 'avgValue' => null,
        ];
        return ['current' => $empty, 'previous' => $empty, 'hotel' => null];
    }

    // -----------------------------------------------------------------------
    // AI Analysis
    // -----------------------------------------------------------------------

    public function generateAiReport(): void
    {
        $this->aiLoading = true;
        $this->aiReport  = '';

        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if (!$hotel) {
            $this->aiReport  = 'Không tìm thấy thông tin khách sạn.';
            $this->aiLoading = false;
            return;
        }

        $stats = $this->getStats();
        $cur   = $stats['current'];
        $prev  = $stats['previous'];

        $periodLabel = match($this->period) {
            'month'   => 'Tháng này',
            'quarter' => '3 tháng gần đây',
            default   => '7 ngày gần đây',
        };

        $pct = fn($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100 : 0);
        $arr = fn($c, $p) => $c > $p ? '↑' : ($c < $p ? '↓' : '→');

        $posKw = implode(', ', array_keys($cur['keywords']['positive'])) ?: 'chưa đủ dữ liệu';
        $negKw = implode(', ', array_keys($cur['keywords']['negative'])) ?: 'không có từ khóa tiêu cực';

        $recentComments = Review::where('hotel_id', $hotel->id)
            ->where('is_active', true)
            ->latest('created_at')
            ->limit(20)
            ->get(['rating', 'comment'])
            ->map(fn($r) => "★{$r->rating}: \"{$r->comment}\"")
            ->implode("\n");

        $prompt = <<<PROMPT
Bạn là chuyên gia tư vấn chất lượng khách sạn OTA. Hãy phân tích dữ liệu đánh giá của khách sạn "{$hotel->name}" và lập báo cáo cải thiện chất lượng.

KỲ BÁO CÁO: {$periodLabel}

=== DỮ LIỆU ĐÁNH GIÁ ===
Kỳ này: {$cur['total']} đánh giá | Rating TB: {$cur['avgRating']}/5 {$arr($cur['avgRating'], $prev['avgRating'])} (kỳ trước: {$prev['avgRating']}/5)
Phân bổ: ⭐⭐⭐⭐⭐ {$cur['fiveStars']} | ⭐⭐⭐⭐ {$cur['fourStars']} | ⭐⭐⭐ {$cur['threeStars']} | ⭐⭐ {$cur['twoStars']} | ⭐ {$cur['oneStar']}
Tỷ lệ đánh giá tích cực (4-5 sao): {$cur['positiveRate']}%
Tỷ lệ phản hồi: {$cur['responseRate']}% (chưa phản hồi: {$cur['unresponded']})
Kỳ trước: {$prev['total']} đánh giá | Rating TB: {$prev['avgRating']}/5

Từ khóa tích cực phổ biến: {$posKw}
Từ khóa tiêu cực phổ biến: {$negKw}

20 NHẬN XÉT GẦN NHẤT:
{$recentComments}

Hãy phân tích và viết báo cáo theo format:

## 1. TỔNG QUAN TUẦN
(Nhận xét ngắn về xu hướng, so sánh kỳ trước, đánh giá tích cực/tiêu cực)

## 2. ĐIỂM MẠNH (Top 3 từ khóa khách hay khen)
(Phân tích điểm mạnh nổi bật, đây là lợi thế cạnh tranh)

## 3. ĐIỂM CẦN CẢI THIỆN (Top 3 vấn đề phổ biến)
(Phân tích vấn đề cụ thể từ nhận xét khách)

## 4. PHÒNG / DỊCH VỤ ĐƯỢC NHẮC NHIỀU
(Phân tích phòng hoặc dịch vụ nào xuất hiện nhiều trong đánh giá)

## 5. ĐỀ XUẤT CẢI TIẾN NGAY (2-3 hành động cụ thể)
(Ưu tiên những gì có thể làm ngay trong tuần tới, có tác động rõ rệt nhất)

Viết bằng tiếng Việt, ngắn gọn, thực tế, hướng hành động.
PROMPT;

        try {
            $orKey = config('services.openrouter.api_key');
            if ($orKey) {
                $res = Http::timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $orKey,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Review Analysis',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful() && $res->json('choices.0.message.content')) {
                    $this->aiReport  = $res->json('choices.0.message.content');
                    $this->aiLoading = false;
                    return;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PartnerReviewAnalysis OpenRouter failed: ' . $e->getMessage());
        }

        try {
            $oaKey = config('services.openai.api_key');
            if ($oaKey) {
                $res = Http::timeout(60)
                    ->withToken($oaKey)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful() && $res->json('choices.0.message.content')) {
                    $this->aiReport  = $res->json('choices.0.message.content');
                    $this->aiLoading = false;
                    return;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PartnerReviewAnalysis OpenAI fallback failed: ' . $e->getMessage());
        }

        $this->aiReport  = 'Không thể kết nối AI. Vui lòng thử lại.';
        $this->aiLoading = false;
    }
}
