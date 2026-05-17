<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class EmailAiController extends Controller
{
    // -----------------------------------------------------------------------
    // E-11 System prompt — personalized email generator
    // -----------------------------------------------------------------------
    private const PROMPT_GENERATE = <<<'PROMPT'
Bạn là chuyên gia email marketing cho nền tảng OTA StayGo (đặt phòng khách sạn & resort tại Vũng Tàu, Nha Trang, Đà Nẵng, Đà Lạt).

Nhiệm vụ: Soạn email marketing cá nhân hóa dựa trên dữ liệu người dùng và trigger được cung cấp.

QUY TẮC BẮT BUỘC:
• Đừng bao giờ bịa thông tin — chỉ dùng data thực từ JSON input.
• Subject line < 60 ký tự (đếm chính xác).
• Preheader < 100 ký tự.
• Chỉ có 1 CTA duy nhất, action-oriented.
• Đề xuất 2 variant subject để A/B test.
• Nếu field nào null/thiếu, bỏ qua phần đó — không placeholder.
• Ngôn ngữ: tiếng Việt tự nhiên, thân thiện.
• Output đầy đủ — không tóm tắt, không "...".

TRIGGER LOGIC:

[win_back — Khách lâu không đặt >60 ngày]
Tone: Nhớ nhung, chân thành, có giá trị thực.
Cấu trúc: Nhắc kỳ nghỉ trước → cảm ơn vì đã tin tưởng → offer voucher exclusive → urgency (hết hạn 7 ngày).
Subject mẫu: "Chúng tôi nhớ bạn, [Tên]! Đây là món quà nhỏ"

[upsell — Sau booking xác nhận]
Tone: Hữu ích, không áp đặt, tạo FOMO nhẹ.
Cấu trúc: Chúc mừng booking → gợi ý 2-3 dịch vụ phù hợp ngân sách → so sánh giá tại chỗ vs giá online → urgency (slots giới hạn).
Subject mẫu: "Nâng cấp trải nghiệm tại [Khách sạn] — chỉ còn X suất"

[loyalty_upgrade — Sắp lên hạng]
Tone: Ăn mừng, tạo động lực, exclusive feeling.
Cấu trúc: Bạn sắp lên hạng! → Còn X điểm → Quyền lợi hạng tiếp theo → Gợi ý booking để tích điểm → CTA gấp.
Subject mẫu: "Chỉ còn X điểm nữa là bạn lên hạng [Tier]!"

[cart_abandon — Đã xem phòng nhưng chưa đặt]
Tone: Nhắc nhở nhẹ nhàng, urgency có căn cứ thực.
Cấu trúc: "Phòng bạn xem vẫn còn" → Hiện lại thông tin phòng + giá → Số phòng còn lại → Lý do nên đặt ngay → CTA rõ ràng.
Subject mẫu: "Phòng bạn xem vẫn còn — nhưng chỉ còn X phòng"

[seasonal — Dịp lễ / mùa cao điểm]
Tone: Phấn khích, tạo FOMO mùa, warm.
Cấu trúc: Dịp lễ đang đến → Deal dành riêng + điểm đến phù hợp → Giá ưu đãi → Lý do đặt sớm.

OUTPUT FORMAT BẮT BUỘC (giữ nguyên tiêu đề section):
---
SUBJECT A: [subject line chính, <60 ký tự]
SUBJECT B: [variant A/B test, <60 ký tự]
PREHEADER: [<100 ký tự, không trùng subject]
---
EMAIL BODY:
[Nội dung email đầy đủ, plain text có format rõ ràng]
---
NOTES: [Lý do chọn angle này, tip personalization thêm nếu có]
PROMPT;

    // -----------------------------------------------------------------------
    // E-12 System prompt — email quality scorer
    // -----------------------------------------------------------------------
    private const PROMPT_SCORE = <<<'PROMPT'
Bạn là chuyên gia kiểm định chất lượng email marketing. Đánh giá email được cung cấp theo thang 100 điểm.

THANG ĐIỂM CHI TIẾT:

[Subject Line — 25 điểm]
1. Độ dài ≤60 ký tự: 0 hoặc 5đ
2. Chứa thông tin cụ thể/value rõ ràng: 0-5đ
3. Cá nhân hóa hoặc kích thích tò mò: 0-5đ
4. Không dùng spam trigger words (FREE, MIỄN PHÍ 100%, CLICK NGAY, !!!): 0-5đ
5. Preheader bổ sung, không lặp subject: 0-5đ

[Nội dung — 40 điểm]
6. Đoạn mở đầu cuốn hút trong 2 câu đầu: 0-8đ
7. Thông tin chính rõ ràng, có thể scan nhanh: 0-8đ
8. Cá nhân hóa: dùng tên, thông tin lịch sử cụ thể: 0-8đ
9. Tone phù hợp với đối tượng và trigger: 0-8đ
10. Không quá dài, đoạn văn ≤4 dòng: 0-8đ

[CTA — 20 điểm]
11. Chỉ có 1 CTA chính duy nhất: 0-5đ
12. CTA rõ ràng, action-oriented (động từ mạnh): 0-5đ
13. Vị trí CTA hợp lý, dễ nhìn thấy: 0-5đ
14. Tạo urgency hợp lý nếu cần: 0-5đ

[Kỹ thuật & Compliance — 15 điểm]
15. Template variables đúng format {{variable}}: 0-3đ
16. Có đề cập/placeholder cho link unsubscribe: 0-3đ
17. Mobile-friendly (đoạn ngắn, không bảng lồng nhau): 0-3đ
18. Không có broken placeholder chưa thay: 0-3đ
19. Tuân thủ GDPR/CAN-SPAM (no deceptive subject): 0-3đ

OUTPUT FORMAT BẮT BUỘC:

## ĐIỂM TỔNG: XX/100 — [Xuất sắc ≥85 / Tốt 70-84 / Trung bình 50-69 / Cần cải thiện <50]

## BẢNG ĐIỂM CHI TIẾT
| # | Tiêu chí | Điểm tối đa | Điểm đạt | Nhận xét |
|---|----------|-------------|----------|----------|
[Điền đầy đủ 19 dòng]

## TOP 3 ĐIỂM MẠNH
1. ...
2. ...
3. ...

## TOP 3 CẦN CẢI THIỆN
1. [Vấn đề cụ thể] — Lý do: ... — Cách sửa: ...
2. ...
3. ...

## EMAIL ĐÃ TỐI ƯU
[Nếu điểm < 70: Rewrite toàn bộ. Nếu ≥70: Chỉ sửa những điểm yếu, giữ nguyên phần tốt]

## 2 VARIANT SUBJECT ĐỂ A/B TEST
A: [subject variant A]
B: [subject variant B]
PROMPT;

    // -----------------------------------------------------------------------
    // E-13 System prompt — campaign KPI analyzer
    // -----------------------------------------------------------------------
    private const PROMPT_ANALYZE = <<<'PROMPT'
Bạn là chuyên gia phân tích email marketing cho OTA. Phân tích toàn diện dữ liệu hiệu suất chiến dịch được cung cấp.

INDUSTRY BENCHMARK CHO OTA (dùng làm baseline so sánh):
• Delivery rate: >98% | Bounce: <2% | Spam rate: <0.1%
• Open rate: Transactional >60% | Pre-arrival >55% | Marketing >25% | Win-back >15%
• CTR: >3% | CTOR: >12% | Unsubscribe: <0.5%
• Booking conversion: >2% (từ click → complete)

QUY TẮC PHÂN TÍCH:
• Dùng số liệu cụ thể, tránh nhận xét chung chung.
• Khi so sánh, chỉ rõ: so với benchmark / so với chiến dịch trước.
• A/B test: chỉ kết luận winner khi confidence >95% (tối thiểu 1000 mẫu mỗi variant).
• Action plan phải actionable và có thể đo lường được.
• Nếu dữ liệu không đủ để kết luận, nói rõ "Cần thêm dữ liệu về X".

OUTPUT FORMAT BẮT BUỘC:

## 1. TỔNG QUAN CHIẾN DỊCH
Tên | Ngày gửi | Segment | Tổng số gửi | Loại trigger

## 2. HIỆU SUẤT VS BENCHMARK
[Bảng so sánh từng KPI với benchmark OTA và chiến dịch trước nếu có]
| KPI | Kết quả | Benchmark | So sánh | Đánh giá |

## 3. PHÂN TÍCH THEO SEGMENT
[Insight từ segment: hạng thành viên / lịch sử booking / địa lý]

## 4. KẾT QUẢ A/B TEST
[Nếu có. Winner? Confidence level? → Apply variant nào làm default]
[Nếu không có: "Đề xuất thiết lập A/B test cho chiến dịch tiếp theo"]

## 5. ANOMALY & INSIGHTS
[Điểm bất thường? Nguyên nhân giả thuyết? Pattern đáng chú ý?]

## 6. ACTION PLAN (3-5 items)
Cho chiến dịch tiếp theo:
1. Subject line direction: ...
2. Thời gian gửi tối ưu: ...
3. Segment: ...
4. Nội dung/CTA: ...
5. [Item bổ sung nếu cần]
PROMPT;

    // -----------------------------------------------------------------------
    // Public endpoints
    // -----------------------------------------------------------------------

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'trigger'   => 'required|string|in:win_back,upsell,loyalty_upgrade,cart_abandon,seasonal',
            'user_data' => 'required|string|max:4000',
            'context'   => 'nullable|string|max:2000',
        ]);

        return $this->callAi(
            rateLimitKey: 'email_ai_gen:' . auth('admin')->id(),
            systemPrompt: self::PROMPT_GENERATE,
            userMessage:  $this->buildGenerateMessage($request),
        );
    }

    public function score(Request $request): JsonResponse
    {
        $request->validate(['email_content' => 'required|string|max:8000']);

        return $this->callAi(
            rateLimitKey: 'email_ai_score:' . auth('admin')->id(),
            systemPrompt: self::PROMPT_SCORE,
            userMessage:  "Hãy chấm điểm và tối ưu email sau:\n\n" . $request->input('email_content'),
        );
    }

    public function analyze(Request $request): JsonResponse
    {
        $request->validate(['campaign_data' => 'required|string|max:8000']);

        return $this->callAi(
            rateLimitKey: 'email_ai_analyze:' . auth('admin')->id(),
            systemPrompt: self::PROMPT_ANALYZE,
            userMessage:  "Phân tích chiến dịch email sau:\n\n" . $request->input('campaign_data'),
        );
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function buildGenerateMessage(Request $request): string
    {
        $trigger = $request->input('trigger');
        $userData = $request->input('user_data');
        $context  = $request->input('context', '');

        $msg  = "TRIGGER: {$trigger}\n\n";
        $msg .= "DỮ LIỆU NGƯỜI DÙNG:\n{$userData}\n";
        if ($context) {
            $msg .= "\nCONTEXT BỔ SUNG:\n{$context}\n";
        }
        $msg .= "\nSoạn email marketing cá nhân hóa hoàn chỉnh theo trigger và dữ liệu trên.";
        return $msg;
    }

    private function callAi(string $rateLimitKey, string $systemPrompt, string $userMessage): JsonResponse
    {
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            return response()->json(['error' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userMessage],
        ];

        // OpenRouter (primary)
        $key = config('services.openrouter.api_key');
        if ($key) {
            try {
                $res = Http::timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $key,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Email AI Studio',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'       => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages'    => $messages,
                        'temperature' => 0.7,
                    ]);

                if ($res->successful()) {
                    return response()->json(['result' => $res->json('choices.0.message.content')]);
                }
            } catch (\Exception $e) {
                Log::warning('EmailAI OpenRouter failed: ' . $e->getMessage());
            }
        }

        // OpenAI (fallback)
        $key2 = config('services.openai.api_key');
        if ($key2) {
            try {
                $res = Http::timeout(60)
                    ->withToken($key2)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'       => config('services.openai.model', 'gpt-4o-mini'),
                        'messages'    => $messages,
                        'temperature' => 0.7,
                    ]);

                if ($res->successful()) {
                    return response()->json(['result' => $res->json('choices.0.message.content')]);
                }
            } catch (\Exception $e) {
                Log::warning('EmailAI OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return response()->json(['error' => 'AI hiện tạm thời không phản hồi. Vui lòng thử lại sau.'], 503);
    }
}
