<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Nhắc nhở đánh giá - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:520px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#e91e8c,#c2185b);padding:32px 36px;text-align:center;}
.hdr h1{margin:0 0 6px;color:#fff;font-size:22px;font-weight:800;}
.hdr p{margin:0;color:rgba(255,255,255,.9);font-size:14px;}
.body{padding:30px 36px;text-align:center;}
.points-box{background:linear-gradient(135deg,#fdf2f8,#fce7f3);border:2px solid #f9a8d4;border-radius:16px;padding:24px;margin-bottom:24px;}
.points-num{font-size:56px;font-weight:900;color:#e91e8c;line-height:1;}
.points-label{font-size:14px;color:#9d174d;margin-top:6px;}
.points-value{font-size:13px;color:#be185d;margin-top:8px;padding:6px 14px;background:rgba(233,30,140,.1);border-radius:20px;display:inline-block;}
.hotel-info{font-size:14px;color:#64748b;margin-bottom:22px;}
.hotel-info strong{color:#1a202c;}
.cta{display:inline-block;background:linear-gradient(135deg,#e91e8c,#c2185b);color:#fff!important;text-decoration:none;padding:14px 40px;border-radius:12px;font-size:15px;font-weight:700;margin-bottom:10px;}
.expires{font-size:12px;color:#94a3b8;margin-top:8px;}
.footer{background:#f8fafc;padding:18px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#e91e8c;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>🎁 Điểm thưởng đang chờ bạn!</h1>
    <p>StayGo — Nhắc nhở đánh giá chuyến lưu trú</p>
  </div>

  <div class="body">
    <div style="font-size:17px;font-weight:700;color:#1a202c;margin-bottom:8px;">Xin chào, {{ $booking->full_name }}!</div>
    <div style="color:#64748b;font-size:14px;margin-bottom:22px;">
      Bạn vẫn còn điểm thưởng từ chuyến lưu trú tại <strong style="color:#1a202c;">{{ $booking->room?->hotel?->name }}</strong> chưa nhận.
    </div>

    <div class="points-box">
      <div class="points-num">50</div>
      <div class="points-label">điểm thưởng đang chờ bạn</div>
      <div class="points-value">= 50,000đ cho lần đặt phòng tiếp theo</div>
    </div>

    <div class="hotel-info">
      Chuyến lưu trú: <strong>{{ $booking->check_in?->format('d/m') }} – {{ $booking->check_out?->format('d/m/Y') }}</strong><br>
      Mã đặt phòng: <strong>{{ $booking->order_code }}</strong>
    </div>

    @php $hotelId = $booking->room?->hotel_id; $bookingId = $booking->id; @endphp
    <a href="{{ config('app.url') }}/hotels/{{ $hotelId }}?booking_id={{ $bookingId }}&rating=5#review-form" class="cta">
      ⭐ Đánh giá & nhận điểm ngay
    </a>
    <div class="expires">⏰ Ưu đãi này hết hạn sau 7 ngày — đừng bỏ lỡ!</div>
  </div>

  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p><a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> | © {{ date('Y') }} StayGo</p>
  </div>

</div>
</body>
</html>
