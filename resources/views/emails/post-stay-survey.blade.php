<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chia sẻ trải nghiệm - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#f59e0b,#fbbf24);padding:36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:26px;font-weight:800;text-shadow:0 1px 3px rgba(0,0,0,.15);}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.9);font-size:15px;}
.body{padding:32px 36px;text-align:center;}
.stars{font-size:42px;letter-spacing:4px;margin:20px 0;}
.title{font-size:20px;font-weight:700;color:#1a202c;margin-bottom:10px;}
.desc{color:#4b5563;font-size:14px;line-height:1.7;margin-bottom:28px;}
.hotel-badge{background:#fef3c7;border:1.5px solid #fcd34d;border-radius:10px;padding:12px 20px;display:inline-block;margin-bottom:24px;}
.hotel-badge .name{font-weight:700;color:#92400e;font-size:15px;}
.hotel-badge .date{color:#a16207;font-size:13px;margin-top:4px;}
.cta{display:inline-block;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff !important;text-decoration:none;padding:14px 40px;border-radius:10px;font-size:16px;font-weight:700;margin-bottom:12px;}
.skip{font-size:12px;color:#94a3b8;margin-top:8px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🌟 StayGo</h1>
    <p>Cảm ơn bạn đã lưu trú!</p>
  </div>
  <div class="body">
    <div class="title">Chuyến đi của bạn thế nào?</div>
    <div class="desc">Nhấn vào số sao để đánh giá nhanh — chỉ mất 5 giây!</div>

    <div class="hotel-badge">
      <div class="name">{{ $booking->room?->hotel?->name ?? '—' }}</div>
      <div class="date">{{ $booking->check_in?->format('d/m') }} – {{ $booking->check_out?->format('d/m/Y') }}</div>
    </div>

    {{-- Quick star rating — each star links to hotel review section with pre-filled rating --}}
    @php $hotelId = $booking->room?->hotel_id; $bookingId = $booking->id; @endphp
    <div style="margin:24px 0 8px;display:flex;justify-content:center;gap:6px;">
      @foreach([1,2,3,4,5] as $star)
      <a href="{{ config('app.url') }}/hotels/{{ $hotelId }}?booking_id={{ $bookingId }}&rating={{ $star }}#review-form"
         style="display:inline-block;font-size:36px;text-decoration:none;line-height:1;" title="{{ $star }} sao">⭐</a>
      @endforeach
    </div>
    <div style="font-size:12px;color:#9ca3af;margin-bottom:20px;">1 = Tệ &nbsp;•&nbsp; 5 = Tuyệt vời</div>

    <a href="{{ config('app.url') }}/hotels/{{ $hotelId }}?booking_id={{ $bookingId }}&rating=5#review-form" class="cta">Viết đánh giá chi tiết →</a>
    <div class="skip">Chỉ mất 1 phút • Đánh giá của bạn rất có giá trị</div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
