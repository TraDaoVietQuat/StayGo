<?php

namespace App\Http\Controllers;

use App\Mail\AdminChatEscalation;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\SupportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ChatbotController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $key = 'chatbot:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json(['reply' => 'Bạn đang gửi quá nhiều tin nhắn. Vui lòng thử lại sau ít phút.'], 429);
        }
        RateLimiter::hit($key, 60);

        $message = $request->input('message');

        $quick = $this->quickReply($message);
        if ($quick) {
            return response()->json(['reply' => $quick]);
        }

        $reply = $this->getAIReply($message);

        if ($reply === null) {
            return response()->json([
                'reply'    => 'Xin lỗi, tôi chưa thể trả lời câu hỏi này. Bạn có muốn chuyển câu hỏi tới nhân viên hỗ trợ không?',
                'escalate' => true,
                'message'  => $message,
            ]);
        }

        return response()->json(['reply' => $reply]);
    }

    public function escalate(Request $request): JsonResponse
    {
        $request->validate([
            'message'   => ['required', 'string', 'max:1000'],
            'user_name' => ['nullable', 'string', 'max:100'],
            'user_email'=> ['nullable', 'email', 'max:100'],
        ]);

        $user      = Auth::user();
        $name      = $request->input('user_name') ?: ($user?->full_name ?? 'Khách vãng lai');
        $email     = $request->input('user_email') ?: ($user?->email ?? null);
        $message   = $request->input('message');

        try {
            $ticket = SupportRequest::create([
                'user_id'  => $user?->id,
                'full_name'=> $name,
                'phone'    => $user?->phone ?? '(chatbot)',
                'email'    => $email,
                'subject'  => 'Chatbot không trả lời được: ' . mb_substr($message, 0, 100),
                'note'     => $message,
                'status'   => 'pending',
            ]);

            $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'supportstaygo@gmail.com'));
            Mail::to($adminEmail)->send(new AdminChatEscalation($ticket, $message));
        } catch (\Exception $e) {
            Log::error('Chatbot escalation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể gửi yêu cầu. Vui lòng gọi hotline 037 384 8395.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi câu hỏi tới nhân viên hỗ trợ. Chúng tôi sẽ phản hồi sớm nhất có thể!',
        ]);
    }

    // ---------------------------------------------------------------
    private function quickReply(string $message): ?string
    {
        $msg = mb_strtolower(trim($message));

        $greetings = ['xin chào', 'hello', 'hi', 'chào', 'hey', 'alo'];
        foreach ($greetings as $g) {
            if (str_contains($msg, $g)) {
                return 'Xin chào! Tôi là trợ lý AI của StayGo. Tôi có thể giúp bạn tìm khách sạn, tra giá phòng, chính sách đặt/hủy phòng và nhiều hơn nữa. Bạn cần hỗ trợ gì?';
            }
        }

        $thanks = ['cảm ơn', 'thanks', 'thank you', 'cám ơn'];
        foreach ($thanks as $t) {
            if (str_contains($msg, $t)) {
                return 'Không có gì! Nếu bạn cần thêm thông tin về khách sạn hay đặt phòng, tôi luôn sẵn sàng hỗ trợ.';
            }
        }

        return null;
    }

    // ---------------------------------------------------------------
    private function buildContext(): string
    {
        return Cache::remember('chatbot_context', 300, function () {
            $locations = Location::all();
            $hotels    = Hotel::where('is_active', true)
                ->with(['location', 'rooms'])
                ->get();

            $lines = [];
            $lines[] = '=== THÔNG TIN HỆ THỐNG ===';
            $lines[] = 'Tên nền tảng: StayGo';
            $lines[] = 'Mô tả: Nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng.';
            $lines[] = 'Hotline: 037 384 8395 | Email: support@staygo.vn';
            $lines[] = '';
            $lines[] = '=== CHÍNH SÁCH ===';
            $lines[] = 'Thanh toán: Chuyển khoản ngân hàng (QR), Ví MoMo, VNPay.';
            $lines[] = 'Xác nhận thanh toán: trong vòng 15 phút.';
            $lines[] = 'Hủy phòng: Có thể hủy khi trạng thái "Đang chờ" hoặc "Đã xác nhận". Hoàn tiền 80% nếu hủy trước 24h.';
            $lines[] = '';

            foreach ($locations as $loc) {
                $hotelsByLoc = $hotels->where('location_id', $loc->id);
                if ($hotelsByLoc->isEmpty()) continue;

                $lines[] = "=== KHÁCH SẠN TẠI: {$loc->name} ===";
                foreach ($hotelsByLoc as $hotel) {
                    $lines[] = "[{$loc->name}] {$hotel->name} — Giá từ: " . number_format($hotel->price) . 'đ/đêm';
                    $lines[] = 'Địa chỉ: ' . ($hotel->address ?? 'Chưa cập nhật');
                    $lines[] = "Đánh giá: {$hotel->rating}/5 ({$hotel->review_count} đánh giá)";
                    if ($hotel->checkin_time) {
                        $lines[] = "Check-in: {$hotel->checkin_time} | Check-out: {$hotel->checkout_time}";
                    }
                    if ($hotel->rooms->isNotEmpty()) {
                        foreach ($hotel->rooms as $room) {
                            $lines[] = "  + {$room->room_name}: " . number_format($room->price) . 'đ/đêm';
                        }
                    }
                    $lines[] = '';
                }
            }

            return implode("\n", $lines);
        });
    }

    // ---------------------------------------------------------------
    private function getAIReply(string $message): ?string
    {
        $context      = $this->buildContext();
        $systemPrompt = $this->buildSystemPrompt($context);
        $messages     = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $message],
        ];

        // --- OpenRouter (primary) ---
        $orKey   = env('OPENROUTER_API_KEY');
        $orModel = env('OPENROUTER_MODEL', 'google/gemma-3-12b-it:free');

        if ($orKey) {
            try {
                $res = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $orKey,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Chatbot',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'       => $orModel,
                        'messages'    => $messages,
                        'max_tokens'  => 400,
                        'temperature' => 0.7,
                    ]);

                if ($res->successful()) {
                    $reply = $res->json('choices.0.message.content');
                    if ($reply) return trim($reply);
                }

                Log::warning('OpenRouter returned no content', ['status' => $res->status()]);
            } catch (\Exception $e) {
                Log::warning('OpenRouter failed: ' . $e->getMessage());
            }
        }

        // --- OpenAI (fallback) ---
        $aiKey   = env('OPENAI_API_KEY');
        $aiModel = env('OPENAI_MODEL', 'gpt-3.5-turbo');

        if ($aiKey) {
            try {
                $res = Http::timeout(15)
                    ->withHeaders(['Authorization' => 'Bearer ' . $aiKey])
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'       => $aiModel,
                        'messages'    => $messages,
                        'max_tokens'  => 400,
                        'temperature' => 0.7,
                    ]);

                if ($res->successful()) {
                    $reply = $res->json('choices.0.message.content');
                    if ($reply) return trim($reply);
                }

                Log::warning('OpenAI fallback returned no content', ['status' => $res->status()]);
            } catch (\Exception $e) {
                Log::warning('OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return null;
    }

    // ---------------------------------------------------------------
    private function buildSystemPrompt(string $context): string
    {
        return <<<PROMPT
Bạn là trợ lý AI của StayGo — nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng.

DỮ LIỆU HỆ THỐNG:
{$context}

QUY TẮC TRẢ LỜI (bắt buộc tuân theo):
1. Chỉ trả lời đúng trọng tâm câu hỏi, không liệt kê thừa.
2. LỌC ĐỊA ĐIỂM CHÍNH XÁC: Nếu hỏi "Đà Lạt" → chỉ lấy khách sạn [Đà Lạt]. Tương tự cho Nha Trang, Vũng Tàu, Đà Nẵng. Không trộn lẫn địa điểm.
3. Nếu hỏi 1 khách sạn → chỉ nói về khách sạn đó.
4. Nếu hỏi danh sách → tối đa 3 gợi ý, mỗi gợi ý 1-2 dòng.
5. Dùng **tên** để in đậm, - để liệt kê. Tối đa 150 từ mỗi câu trả lời.
6. Không mở đầu bằng "Tôi là AI..." hay giải thích dài dòng.
7. Giá luôn kèm đơn vị đ/đêm. Không bịa thông tin ngoài dữ liệu trên.
PROMPT;
    }
}
