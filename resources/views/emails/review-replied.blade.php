<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Partner phản hồi đánh giá - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#1B3A6B;padding:20px 28px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.body{padding:24px 28px;}
.stars{color:#f59e0b;font-size:18px;letter-spacing:2px;}
.review-box{background:#f8fafc;border-left:3px solid #94a3b8;border-radius:4px;padding:12px 16px;margin:8px 0 16px;font-size:14px;color:#374151;line-height:1.6;font-style:italic;}
.reply-box{background:#eff6ff;border-left:3px solid #1B3A6B;border-radius:4px;padding:12px 16px;margin:8px 0 16px;font-size:14px;color:#1e3a5f;line-height:1.6;}
.hotel-name{font-weight:700;color:#1B3A6B;}
.btn{display:inline-block;background:#1B3A6B;color:#fff;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;margin-top:16px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>💬 Partner vừa phản hồi đánh giá của bạn</h1>
  </div>
  <div class="body">
    <p style="margin:0 0 16px;font-size:15px;color:#374151;">
      Xin chào <strong>{{ $review->user?->full_name ?? 'Quý khách' }}</strong>,
    </p>
    <p style="font-size:14px;color:#374151;margin:0 0 16px;">
      <span class="hotel-name">{{ $review->hotel?->name }}</span> vừa phản hồi đánh giá
      @if($review->check_in ?? false) cho kỳ lưu trú {{ $review->booking?->check_in?->format('d/m/Y') }}@endif của bạn.
    </p>

    <p style="margin:0 0 6px;font-size:13px;color:#718096;">Đánh giá của bạn ({{ number_format($review->rating, 1) }}/5):</p>
    <div class="stars">{{ str_repeat('★', (int)round($review->rating)) }}{{ str_repeat('☆', 5 - (int)round($review->rating)) }}</div>
    @if($review->comment)
    <div class="review-box">"{{ $review->comment }}"</div>
    @endif

    <p style="margin:16px 0 6px;font-size:13px;color:#718096;">Phản hồi từ <strong>{{ $review->hotel?->name }}</strong>:</p>
    <div class="reply-box">{{ $review->partner_reply }}</div>

    <a href="{{ config('app.url') }}/hotels/{{ $review->hotel_id }}" class="btn">Xem khách sạn</a>

    <p style="font-size:12px;color:#94a3b8;margin-top:20px;">
      Cảm ơn bạn đã đánh giá và giúp cộng đồng StayGo ngày càng tốt hơn.
    </p>
  </div>
  <div class="footer">
    <p>StayGo — Email tự động, vui lòng không reply trực tiếp vào email này.</p>
  </div>
</div>
</body>
</html>
