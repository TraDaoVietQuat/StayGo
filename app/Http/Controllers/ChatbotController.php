<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use OpenAI\Laravel\Facades\OpenAI;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $key = 'chatbot:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json(['reply' => 'Bạn đang gửi quá nhiều tin nhắn. Vui lòng thử lại sau ít phút.'], 429);
        }
        RateLimiter::hit($key, 60);

        $message = $request->input('message');

        // Câu hỏi chào hỏi đơn giản → trả lời ngay, không cần gọi AI
        $quick = $this->quickReply($message);
        if ($quick) {
            return response()->json(['reply' => $quick]);
        }

        return response()->json(['reply' => $this->getAIReply($message)]);
    }

    // ---------------------------------------------------------------
    // Trả lời nhanh không cần AI (chào hỏi, cảm ơn)
    // ---------------------------------------------------------------
    private function quickReply(string $message): ?string
    {
        $msg = mb_strtolower(trim($message));

        $greetings = ['xin chào', 'hello', 'hi', 'chào', 'hey', 'alo'];
        foreach ($greetings as $g) {
            if (str_contains($msg, $g)) {
                return 'Xin chào! 👋 Tôi là trợ lý AI của StayGo. Tôi có thể giúp bạn tìm khách sạn, tra giá phòng, chính sách đặt/hủy phòng và nhiều hơn nữa. Bạn cần hỗ trợ gì?';
            }
        }

        $thanks = ['cảm ơn', 'thanks', 'thank you', 'cám ơn'];
        foreach ($thanks as $t) {
            if (str_contains($msg, $t)) {
                return 'Không có gì! 😊 Nếu bạn cần thêm thông tin về khách sạn hay đặt phòng, tôi luôn sẵn sàng hỗ trợ.';
            }
        }

        return null;
    }

    // ---------------------------------------------------------------
    // Xây dựng context từ DB (cache 5 phút)
    // ---------------------------------------------------------------
    private function buildContext(): string
    {
        return Cache::remember('chatbot_context', 300, function () {
            $locations = Location::all();
            $hotels    = Hotel::where('is_active', true)
                ->with(['location', 'rooms'])
                ->get();

            $lines = [];

            // Thông tin chung
            $lines[] = '=== THÔNG TIN HỆ THỐNG ===';
            $lines[] = 'Tên nền tảng: StayGo';
            $lines[] = 'Mô tả: Nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng, Việt Nam.';
            $lines[] = 'Website: staygo.local';
            $lines[] = 'Hotline: 037 384 8395';
            $lines[] = 'Email: support@staygo.vn';
            $lines[] = 'Địa chỉ: 146 Nguyễn Văn Cừ, TP. Kon Tum';
            $lines[] = '';

            // Chính sách
            $lines[] = '=== CHÍNH SÁCH ===';
            $lines[] = 'Thanh toán: Chuyển khoản ngân hàng (QR), Ví MoMo, VNPay.';
            $lines[] = 'Xác nhận thanh toán: trong vòng 15 phút.';
            $lines[] = 'Hủy phòng: Có thể hủy khi trạng thái "Đang chờ" hoặc "Đã xác nhận". Hoàn tiền 80% nếu hủy trước 24h.';
            $lines[] = 'Đánh giá: Chỉ được đánh giá sau khi hoàn thành lưu trú.';
            $lines[] = '';

            // Danh sách khách sạn nhóm theo địa điểm
            foreach ($locations as $loc) {
                $hotelsByLoc = $hotels->where('location_id', $loc->id);
                if ($hotelsByLoc->isEmpty()) continue;

                $lines[] = "=== KHÁCH SẠN TẠI: {$loc->name} ===";

                foreach ($hotelsByLoc as $hotel) {
                    $lines[] = '';
                    $lines[] = "[{$loc->name}] Tên: {$hotel->name}";
                    $lines[] = "Địa chỉ: " . ($hotel->address ?? 'Chưa cập nhật');
                    $lines[] = "Đánh giá: {$hotel->rating}/5 ({$hotel->review_count} đánh giá)";
                    $lines[] = "Giá từ: " . number_format($hotel->price) . "đ/đêm";
                    if ($hotel->old_price) {
                        $lines[] = "Giá gốc: " . number_format($hotel->old_price) . "đ/đêm";
                    }
                    if ($hotel->is_weekend_deal) {
                        $lines[] = "Ưu đãi cuối tuần: Có";
                    }
                    if ($hotel->checkin_time) {
                        $lines[] = "Giờ nhận phòng: {$hotel->checkin_time} | Giờ trả phòng: {$hotel->checkout_time}";
                    }
                    if ($hotel->description) {
                        $desc = mb_substr(strip_tags($hotel->description), 0, 150);
                        $lines[] = "Mô tả: {$desc}";
                    }
                    if ($hotel->rooms->isNotEmpty()) {
                        $lines[] = "Các loại phòng:";
                        foreach ($hotel->rooms as $room) {
                            $lines[] = "  + {$room->room_name} ({$room->bed_type}): " . number_format($room->price) . "đ/đêm, số lượng: {$room->quantity}";
                        }
                    }
                }
                $lines[] = '';
            }

            return implode("\n", $lines);
        });
    }

    // ---------------------------------------------------------------
    // Gọi OpenAI với context từ DB
    // ---------------------------------------------------------------
    private function getAIReply(string $message): string
    {
        try {
            $context = $this->buildContext();

            $systemPrompt = <<<PROMPT
Bạn là trợ lý AI của StayGo — nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng.

DỮ LIỆU HỆ THỐNG:
{$context}

QUY TẮC TRẢ LỜI (bắt buộc tuân theo):
1. Chỉ trả lời đúng trọng tâm câu hỏi, không liệt kê thừa.
2. **LỌC ĐỊA ĐIỂM CHÍNH XÁC**: Nếu người dùng hỏi về "Đà Lạt" → chỉ lấy khách sạn có nhãn [Đà Lạt]. Nếu hỏi "Nha Trang" → chỉ lấy [Nha Trang]. Nếu hỏi "Vũng Tàu" → chỉ lấy [Vũng Tàu]. Nếu hỏi "Đà Nẵng" → chỉ lấy [Đà Nẵng]. Tuyệt đối không trộn lẫn khách sạn của địa điểm khác.
3. Nếu hỏi 1 khách sạn → chỉ nói về khách sạn đó.
4. Nếu hỏi danh sách → tối đa 3 gợi ý, mỗi gợi ý 1-2 dòng.
5. Dùng định dạng: **tên** để in đậm, - để liệt kê.
6. Không mở đầu bằng "Tôi là AI..." hay giải thích dài dòng.
7. Tối đa 150 từ mỗi câu trả lời.
8. Giá luôn kèm đơn vị đ/đêm.
9. Không bịa thông tin ngoài dữ liệu trên.
PROMPT;

            $response = OpenAI::chat()->create([
                'model'    => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $message],
                ],
                'max_tokens'  => 400,
                'temperature' => 0.7,
            ]);

            return $response->choices[0]->message->content
                ?? 'Xin lỗi, tôi không hiểu câu hỏi của bạn. Bạn có thể hỏi lại không?';

        } catch (\Exception $e) {
            return 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng gọi hotline 037 384 8395 để được hỗ trợ trực tiếp.';
        }
    }
}
