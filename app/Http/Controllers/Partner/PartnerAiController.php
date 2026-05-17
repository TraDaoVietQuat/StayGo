<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\RoomUnavailableDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PartnerAiController extends Controller
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
Bạn là trợ lý AI thông minh hỗ trợ đối tác khách sạn (Hotel Partner) quản lý và tối ưu hóa hoạt động kinh doanh trên nền tảng OTA StayGo.

VAI TRÒ:
Hỗ trợ chủ khách sạn / quản lý khách sạn trong việc tối đa hóa doanh thu, nâng cao tỷ lệ lấp đầy phòng và cải thiện điểm đánh giá trên nền tảng OTA.

QUYỀN HẠN:
- Chỉ xem và phân tích dữ liệu của khách sạn đối tác đang đăng nhập (đã cung cấp trong DB Context)
- Không chia sẻ dữ liệu khách sạn khác
- Không can thiệp vào hệ thống thanh toán gốc

PHONG CÁCH TRẢ LỜI:
- Trả lời bằng tiếng Việt, ngắn gọn, thực tế, hướng hành động
- Dựa hoàn toàn vào dữ liệu thực tế từ DB Context được cung cấp
- Khi đưa ra gợi ý, giải thích rõ tác động đến doanh thu / tỷ lệ lấp đầy / đánh giá
- Cảnh báo sớm khi phát hiện chỉ số bất thường
- Không bịa đặt số liệu ngoài DB Context
- Nếu câu hỏi nằm ngoài phạm vi quản lý khách sạn, từ chối lịch sự
PROMPT;

    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $partner = auth('hotel_partner')->user();
        $hotel   = $partner?->managedHotel;

        if (!$hotel) {
            return response()->json(['reply' => 'Tài khoản của bạn chưa được gán khách sạn. Vui lòng liên hệ Admin để kích hoạt.']);
        }

        $rateLimitKey = 'partner_ai_chat:' . $partner->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            return response()->json(['error' => 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau ít phút.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $message = trim($request->input('message'));
        $history = $request->input('history', []);
        $context = $this->buildHotelContext($hotel);
        $reply   = $this->getAIReply($message, $history, $context);

        if ($reply === null) {
            return response()->json(['reply' => 'AI đang tạm thời không phản hồi. Vui lòng thử lại sau.', 'error' => true]);
        }

        return response()->json(['reply' => $reply]);
    }

    public function kpi(): JsonResponse
    {
        $partner = auth('hotel_partner')->user();
        $hotel   = $partner?->managedHotel;

        if (!$hotel) {
            return response()->json(['error' => 'no_hotel'], 404);
        }

        $cacheKey = 'partner_ai_kpi:' . $hotel->id;
        $data = Cache::remember($cacheKey, 120, function () use ($hotel) {
            $today     = now()->toDateString();
            $thisMonth = now()->format('Y-m');
            $roomIds   = $hotel->rooms()->pluck('id');

            $bookingsToday = Booking::whereIn('room_id', $roomIds)
                ->whereDate('created_at', $today)->count();

            $revenueMonth = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', 'like', "$thisMonth%")
                ->sum('total_price');

            $pendingCount = Booking::whereIn('room_id', $roomIds)
                ->where('status', 'pending')->count();

            // Phòng đang có khách check-in hôm nay
            $checkInsToday = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereDate('check_in', $today)->count();

            $avgRating = Review::where('hotel_id', $hotel->id)
                ->where('is_active', true)->avg('rating');

            return [
                'bookings_today'     => $bookingsToday,
                'revenue_month'      => $revenueMonth,
                'revenue_month_fmt'  => number_format($revenueMonth, 0, ',', '.') . ' ₫',
                'pending'            => $pendingCount,
                'checkins_today'     => $checkInsToday,
                'avg_rating'         => number_format((float) $avgRating, 1),
            ];
        });

        return response()->json($data);
    }

    private function buildHotelContext($hotel): string
    {
        $cacheKey = 'partner_ai_context:' . $hotel->id;
        return Cache::remember($cacheKey, 300, function () use ($hotel) {
            $today     = now()->toDateString();
            $thisMonth = now()->format('Y-m');
            $roomIds   = $hotel->rooms()->pluck('id');

            // ---- Bookings ----
            $bookingsToday = Booking::whereIn('room_id', $roomIds)->whereDate('created_at', $today)->count();
            $bookingsMonth = Booking::whereIn('room_id', $roomIds)->where('created_at', 'like', "$thisMonth%")->count();

            $revenueToday = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereDate('created_at', $today)->sum('total_price');

            $revenueMonth = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', 'like', "$thisMonth%")->sum('total_price');

            $pending   = Booking::whereIn('room_id', $roomIds)->where('status', 'pending')->count();
            $confirmed = Booking::whereIn('room_id', $roomIds)->where('status', 'confirmed')->count();
            $cancelled = Booking::whereIn('room_id', $roomIds)->where('status', 'cancelled')
                ->where('created_at', 'like', "$thisMonth%")->count();

            $cancellationRate = $bookingsMonth > 0 ? round($cancelled / $bookingsMonth * 100, 1) : 0;

            // ---- Upcoming check-ins (7 ngày tới) ----
            $upcomingCheckIns = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed'])
                ->whereBetween('check_in', [$today, Carbon::now()->addDays(7)->toDateString()])
                ->count();

            // ---- Occupancy (tháng này) ----
            $totalRooms   = $hotel->rooms()->sum('quantity') ?: 1;
            $daysInMonth  = now()->daysInMonth;
            $maxRoomNights = $totalRooms * $daysInMonth;
            $roomNightsBooked = Booking::whereIn('room_id', $roomIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', 'like', "$thisMonth%")
                ->selectRaw('SUM(DATEDIFF(check_out, check_in)) as nights')
                ->value('nights') ?? 0;
            $occupancyRate = round($roomNightsBooked / $maxRoomNights * 100, 1);

            // ---- ADR & RevPAR ----
            $adr    = $roomNightsBooked > 0 ? round($revenueMonth / $roomNightsBooked) : 0;
            $revpar = round($adr * $occupancyRate / 100);

            // ---- Rooms info ----
            $roomsSummary = $hotel->rooms()->get(['room_name', 'quantity', 'price', 'bed_type'])
                ->map(fn($r) => "{$r->room_name} ({$r->bed_type}): {$r->quantity} phòng, giá " . number_format($r->price) . " VNĐ/đêm")
                ->implode(' | ');

            // ---- Reviews ----
            $avgRating    = round((float) Review::where('hotel_id', $hotel->id)->where('is_active', true)->avg('rating'), 2);
            $totalReviews = Review::where('hotel_id', $hotel->id)->where('is_active', true)->count();
            $lowReviews   = Review::where('hotel_id', $hotel->id)->where('is_active', true)->where('rating', '<=', 2)->count();

            $recentLowReviews = Review::where('hotel_id', $hotel->id)
                ->where('is_active', true)
                ->where('rating', '<=', 2)
                ->latest('created_at')
                ->limit(3)
                ->get()
                ->map(fn($r) => "★{$r->rating} — \"{$r->comment}\"")
                ->implode(' || ');

            // ---- 5 đơn gần nhất ----
            $recentBookings = Booking::whereIn('room_id', $roomIds)
                ->with(['room:id,room_name'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($b) => sprintf(
                    '#%s | %s | %s → %s | %s | %s VNĐ',
                    $b->order_code ?? $b->id,
                    $b->room?->room_name ?? 'N/A',
                    $b->check_in?->format('d/m') ?? '?',
                    $b->check_out?->format('d/m') ?? '?',
                    $b->status,
                    number_format($b->total_price)
                ))
                ->implode(' || ');

            // ---- Lead time TB ----
            $avgLeadTime = round(
                Booking::whereIn('room_id', $roomIds)
                    ->whereNotNull('check_in')
                    ->where('created_at', 'like', "$thisMonth%")
                    ->selectRaw('AVG(DATEDIFF(check_in, DATE(created_at))) as avg_lead')
                    ->value('avg_lead') ?? 0,
                1
            );

            $commission = $hotel->partnerUser?->partnerProfile?->commission_rate ?? 0;

            return <<<CONTEXT
=== DB CONTEXT: {$hotel->name} (cập nhật: {$today}) ===
[THÔNG TIN KHÁCH SẠN]
Tên: {$hotel->name} | Loại: {$hotel->type} | Sao: {$hotel->stars} sao | Địa điểm: {$hotel->location?->name}
Rating hiện tại: {$hotel->rating}/5 | Tổng đánh giá: {$hotel->review_count}
Hoa hồng nền tảng: {$commission}% | Chính sách hủy: {$hotel->cancellation_policy}
Check-in: {$hotel->checkin_time} | Check-out: {$hotel->checkout_time}

[PHÒNG]
Tổng số loại phòng: {$hotel->rooms()->count()} | Tổng phòng vật lý: {$totalRooms}
Chi tiết: {$roomsSummary}

[HOẠT ĐỘNG ĐẶT PHÒNG — {$thisMonth}]
Hôm nay: {$bookingsToday} đơn | Tháng này: {$bookingsMonth} đơn
Đang chờ xác nhận: {$pending} | Đã xác nhận: {$confirmed} | Hủy tháng này: {$cancelled}
Tỷ lệ hủy: {$cancellationRate}% | Check-in trong 7 ngày tới: {$upcomingCheckIns} đơn
Lead time trung bình: {$avgLeadTime} ngày

[DOANH THU]
Hôm nay: {$revenueToday} VNĐ | Tháng này: {$revenueMonth} VNĐ

[CHỈ SỐ HIỆU SUẤT — {$thisMonth}]
Occupancy Rate: {$occupancyRate}% (mục tiêu: >70%)
ADR (Giá trung bình/đêm): {$adr} VNĐ
RevPAR: {$revpar} VNĐ

[ĐÁNH GIÁ & UY TÍN]
Rating trung bình: {$avgRating}/5 | Tổng đánh giá: {$totalReviews}
Đánh giá 1-2 sao: {$lowReviews} cái
Đánh giá thấp gần đây: {$recentLowReviews}

[5 ĐƠN ĐẶT PHÒNG GẦN NHẤT]
{$recentBookings}
=== END CONTEXT ===
CONTEXT;
        });
    }

    private function getAIReply(string $message, array $history, string $context): ?string
    {
        $messages = [['role' => 'system', 'content' => self::SYSTEM_PROMPT . "\n\n" . $context]];

        foreach (array_slice($history, -10) as $h) {
            if (isset($h['role'], $h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => (string) $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // OpenRouter (primary)
        $openrouterKey = config('services.openrouter.api_key');
        if ($openrouterKey) {
            try {
                $res = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $openrouterKey,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Partner AI',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemma-3-12b-it:free'),
                        'messages' => $messages,
                    ]);

                if ($res->successful()) {
                    return $res->json('choices.0.message.content');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerAI OpenRouter failed: ' . $e->getMessage());
            }
        }

        // OpenAI (fallback)
        $openaiKey = config('services.openai.api_key');
        if ($openaiKey) {
            try {
                $res = Http::timeout(30)
                    ->withToken($openaiKey)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-3.5-turbo'),
                        'messages' => $messages,
                    ]);

                if ($res->successful()) {
                    return $res->json('choices.0.message.content');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerAI OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return null;
    }
}
