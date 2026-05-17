<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Chia sẻ trải nghiệm - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#f59e0b,#d97706);padding:36px;text-align:center;}
.hdr h1{margin:0 0 6px;color:#fff;font-size:26px;font-weight:800;text-shadow:0 1px 3px rgba(0,0,0,.15);}
.hdr p{margin:0;color:rgba(255,255,255,.9);font-size:14px;}
.body{padding:32px 36px;text-align:center;}
.title{font-size:20px;font-weight:700;color:#1a202c;margin:0 0 10px;}
.desc{color:#4b5563;font-size:14px;line-height:1.7;margin:0 0 22px;}
.hotel-badge{background:#fef3c7;border:1.5px solid #fcd34d;border-radius:12px;padding:14px 22px;display:inline-block;margin-bottom:22px;}
.hotel-badge .name{font-weight:700;color:#92400e;font-size:16px;}
.hotel-badge .date{color:#a16207;font-size:13px;margin-top:4px;}
/* Star rating quick links */
.stars-row{display:flex;justify-content:center;gap:4px;margin-bottom:8px;}
.star-link{display:flex;flex-direction:column;align-items:center;text-decoration:none;padding:8px 12px;border-radius:10px;border:1.5px solid #e2e8f0;transition:background .15s;min-width:52px;}
.star-link:hover{background:#fef9c3;}
.star-emoji{font-size:26px;line-height:1;}
.star-num{font-size:11px;color:#78716c;margin-top:3px;font-weight:600;}
.star-labels{display:flex;justify-content:space-between;font-size:11px;color:#9ca3af;margin-bottom:22px;padding:0 4px;}
/* Reward points banner */
.reward-banner{background:linear-gradient(135deg,#fdf2f8,#fce7f3);border:2px solid #f9a8d4;border-radius:12px;padding:16px 20px;margin-bottom:22px;display:flex;align-items:center;gap:14px;text-align:left;}
.reward-icon{font-size:32px;flex-shrink:0;}
.reward-text strong{display:block;font-size:15px;color:#be185d;margin-bottom:3px;}
.reward-text span{font-size:13px;color:#9d174d;line-height:1.5;}
/* CTA */
.cta{display:inline-block;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff!important;text-decoration:none;padding:14px 40px;border-radius:12px;font-size:15px;font-weight:700;margin-bottom:10px;}
.skip{font-size:12px;color:#94a3b8;margin-top:6px;}
/* Why section */
.why{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px;text-align:left;margin-top:20px;}
.why p{margin:0 0 8px;font-size:13px;font-weight:700;color:#475569;}
.why ul{margin:0;padding-left:18px;}
.why li{font-size:13px;color:#64748b;line-height:1.9;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#f59e0b;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>🌟 StayGo</h1>
    <p>Cảm ơn bạn đã lưu trú cùng chúng tôi!</p>
  </div>

  <div class="body">
    <div class="title">{{ $booking->full_name }}, chuyến đi của bạn thế nào?</div>
    <div class="desc">Chỉ 2 phút — đánh giá của bạn giúp ích rất nhiều cho những khách du lịch khác đang cân nhắc đến đây.</div>

    <div class="hotel-badge">
      <div class="name">{{ $booking->room?->hotel?->name ?? '—' }}</div>
      <div class="date">{{ $booking->check_in?->format('d/m') }} – {{ $booking->check_out?->format('d/m/Y') }}</div>
    </div>

    {{-- Reward points incentive --}}
    <div class="reward-banner">
      <div class="reward-icon">🎁</div>
      <div class="reward-text">
        <strong>Nhận 50 điểm thưởng!</strong>
        <span>Tương đương 50,000đ cho lần đặt phòng tiếp theo của bạn. Điểm có hiệu lực 90 ngày.</span>
      </div>
    </div>

    {{-- Quick star rating --}}
    @php $hotelId = $booking->room?->hotel_id; $bookingId = $booking->id; @endphp
    <div style="font-size:13px;font-weight:700;color:#1a202c;margin-bottom:12px;">Đánh giá nhanh:</div>
    <div class="stars-row">
      @foreach([
        [1, '⭐', 'Rất tệ'],
        [2, '⭐⭐', 'Tệ'],
        [3, '⭐⭐⭐', 'Ổn'],
        [4, '⭐⭐⭐⭐', 'Tốt'],
        [5, '⭐⭐⭐⭐⭐', 'Xuất sắc'],
      ] as [$star, $emoji, $label])
      <a href="{{ config('app.url') }}/hotels/{{ $hotelId }}?booking_id={{ $bookingId }}&rating={{ $star }}#review-form"
         class="star-link" title="{{ $label }}">
        <span class="star-emoji">{{ $star }}★</span>
        <span class="star-num">{{ $label }}</span>
      </a>
      @endforeach
    </div>
    <div class="star-labels">
      <span>1 = Rất tệ</span>
      <span>5 = Xuất sắc</span>
    </div>

    <a href="{{ config('app.url') }}/hotels/{{ $hotelId }}?booking_id={{ $bookingId }}&rating=5#review-form" class="cta">
      ✍️ Viết đánh giá đầy đủ & nhận 50 điểm
    </a>
    <div class="skip">Mã đặt phòng: <strong>{{ $booking->order_code }}</strong></div>

    <div class="why">
      <p>🤔 Tại sao nên đánh giá?</p>
      <ul>
        <li>Giúp khách sạn cải thiện dịch vụ thực sự</li>
        <li>Giúp khách du lịch khác có quyết định đúng hơn</li>
        <li>Bạn nhận <strong>50 điểm thưởng</strong> (= 50,000đ) cho lần đặt tiếp theo</li>
      </ul>
    </div>
  </div>

  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p><a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> | © {{ date('Y') }} StayGo</p>
  </div>

</div>
</body>
</html>
