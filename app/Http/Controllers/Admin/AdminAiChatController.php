<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AdminAiChatController extends Controller
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
Bạn là trợ lý AI thông minh của nền tảng quản trị OTA (Online Travel Agency) StayGo.
Bạn hỗ trợ Super Admin quản lý toàn bộ hoạt động của nền tảng gồm 4 điểm đến: Vũng Tàu, Nha Trang, Đà Nẵng, Đà Lạt.

Nhiệm vụ của bạn:
1. Quản lý đối tác khách sạn & resort: tình trạng phòng, giá phòng, hình ảnh, đánh giá, chính sách hủy phòng.
2. Quản lý người dùng: thông tin tài khoản, lịch sử đặt phòng, phản hồi & khiếu nại.
3. Quản lý đặt phòng: trạng thái đơn (pending/confirmed/cancelled/completed/refunded), xử lý tranh chấp, xác nhận thanh toán.
4. Quản lý tài chính: doanh thu theo ngày/tháng/năm, tỷ lệ hoàn tiền, phương thức thanh toán phổ biến, theo dõi công nợ đối tác.
5. Quản lý khuyến mãi: mã giảm giá, chương trình ưu đãi, hiệu quả chiến dịch.
6. Quản lý nội dung: blog, địa điểm, banner, SEO.
7. Phân tích & báo cáo: tỷ lệ lấp đầy phòng, thời gian đặt phòng trung bình, khách sạn hiệu suất cao/thấp.
8. Cấu hình hệ thống: cài đặt thanh toán, email, thông báo, bảo mật.

Nguyên tắc:
- Trả lời bằng tiếng Việt, súc tích, chuyên nghiệp.
- Dựa trên dữ liệu thực tế từ DB Context được cung cấp.
- Nếu được hỏi về số liệu cụ thể, hãy phân tích và đưa ra nhận xét, gợi ý hành động.
- Không bịa đặt số liệu ngoài DB Context.
- Nếu câu hỏi nằm ngoài phạm vi OTA, từ chối lịch sự và đề nghị hỏi về nghiệp vụ.
PROMPT;

    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $adminId = auth('admin')->id();
        $rateLimitKey = 'admin_ai_chat:' . $adminId;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            return response()->json(['error' => 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $message  = trim($request->input('message'));
        $history  = $request->input('history', []);
        $context  = $this->buildAdminContext();
        $reply    = $this->getAIReply($message, $history, $context);

        if ($reply === null) {
            return response()->json(['reply' => 'Xin lỗi, AI đang tạm thời không phản hồi. Vui lòng thử lại sau.', 'error' => true]);
        }

        return response()->json(['reply' => $reply]);
    }

    public function kpi(): JsonResponse
    {
        $data = Cache::remember('admin_ai_kpi', 120, function () {
            $today     = now()->toDateString();
            $thisMonth = now()->format('Y-m');

            $bookingsToday = Booking::whereDate('created_at', $today)->count();
            $revenueMonth  = Booking::where('created_at', 'like', "$thisMonth%")
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price');
            $pending     = Booking::where('status', 'pending')->count();
            $openTickets = SupportRequest::whereIn('status', ['pending', 'processing'])->count();

            return [
                'bookings_today'    => $bookingsToday,
                'revenue_month'     => $revenueMonth,
                'revenue_month_fmt' => number_format($revenueMonth, 0, ',', '.') . ' ₫',
                'pending'           => $pending,
                'open_tickets'      => $openTickets,
            ];
        });

        return response()->json($data);
    }

    private function buildAdminContext(): string
    {
        return Cache::remember('admin_ai_context', 300, function () {
            $today     = now()->toDateString();
            $thisMonth = now()->format('Y-m');

            $bookingsToday = Booking::whereDate('created_at', $today)->count();
            $bookingsMonth = Booking::where('created_at', 'like', "$thisMonth%")->count();
            $revenueToday  = Booking::whereDate('created_at', $today)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price');
            $revenueMonth  = Booking::where('created_at', 'like', "$thisMonth%")
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price');

            $pending   = Booking::where('status', 'pending')->count();
            $confirmed = Booking::where('status', 'confirmed')->count();
            $cancelled = Booking::where('status', 'cancelled')->count();
            $completed = Booking::where('status', 'completed')->count();

            $totalUsers  = User::count();
            $newUsersDay = User::whereDate('created_at', $today)->count();

            $openTickets = SupportRequest::whereIn('status', ['pending', 'processing'])->count();

            $topHotels = Hotel::withCount(['bookings' => fn ($q) => $q->whereIn('status', ['confirmed', 'completed'])])
                ->orderByDesc('bookings_count')
                ->limit(5)
                ->get(['id', 'name', 'bookings_count'])
                ->map(fn ($h) => "{$h->name} ({$h->bookings_count} đơn)")
                ->implode(', ');

            $recentBookings = Booking::with(['user:id,full_name', 'room.hotel:id,name'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($b) => sprintf(
                    '#%s | %s | %s | %s | %s VND',
                    $b->order_code ?? $b->id,
                    $b->user?->full_name ?? 'Khách vãng lai',
                    $b->room?->hotel?->name ?? 'N/A',
                    $b->status,
                    number_format($b->total_price)
                ))
                ->implode(' || ');

            return <<<CONTEXT
=== DB CONTEXT (cập nhật: {$today}) ===
Đặt phòng hôm nay: {$bookingsToday} đơn | Tháng này: {$bookingsMonth} đơn
Doanh thu hôm nay: {$revenueToday} VND | Tháng này: {$revenueMonth} VND
Trạng thái đơn: Chờ={$pending} | Xác nhận={$confirmed} | Đã hủy={$cancelled} | Hoàn thành={$completed}
Người dùng: Tổng={$totalUsers} | Mới hôm nay={$newUsersDay}
Yêu cầu hỗ trợ chưa giải quyết: {$openTickets}
Top khách sạn: {$topHotels}
5 đơn gần nhất: {$recentBookings}
=== END CONTEXT ===
CONTEXT;
        });
    }

    private function getAIReply(string $message, array $history, string $context): ?string
    {
        $messages = [['role' => 'system', 'content' => self::SYSTEM_PROMPT . "\n\n" . $context]];

        foreach (array_slice($history, -10) as $h) {
            if (isset($h['role'], $h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // OpenRouter (primary)
        $openrouterKey = config('services.openrouter.api_key');
        if ($openrouterKey) {
            try {
                $res = Http::timeout(30)
                    ->withHeaders([
                        'Authorization'  => 'Bearer ' . $openrouterKey,
                        'HTTP-Referer'   => config('app.url'),
                        'X-Title'        => 'StayGo Admin AI',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemma-3-12b-it:free'),
                        'messages' => $messages,
                    ]);

                if ($res->successful()) {
                    return $res->json('choices.0.message.content');
                }
            } catch (\Exception $e) {
                Log::warning('AdminAI OpenRouter failed: ' . $e->getMessage());
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
                Log::warning('AdminAI OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return null;
    }
}
