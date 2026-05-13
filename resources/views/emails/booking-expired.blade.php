<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt phòng hết hạn - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#78716c,#57534e);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:32px 36px;}
.greeting{font-size:16px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.alert-box{background:#fafaf9;border:2px solid #d6d3d1;border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:20px;}
.alert-icon{font-size:36px;margin-bottom:8px;}
.alert-title{font-size:16px;font-weight:700;color:#57534e;margin-bottom:4px;}
.alert-sub{font-size:13px;color:#78716c;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:20px;}
.row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.cta{display:block;text-align:center;background:#0066cc;color:#fff !important;text-decoration:none;padding:13px 32px;border-radius:10px;font-size:14px;font-weight:700;margin-bottom:8px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Đặt phòng đã hết hạn thanh toán</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Đặt phòng của bạn đã bị hủy tự động do chưa hoàn tất thanh toán trong 30 phút.</div>

    <div class="alert-box">
      <div class="alert-icon">⏱️</div>
      <div class="alert-title">Hết hạn thanh toán</div>
      <div class="alert-sub">Mã đơn <strong>{{ $booking->order_code }}</strong> đã bị hủy lúc {{ now()->format('H:i d/m/Y') }}</div>
    </div>

    <div class="info-box">
      <div class="row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span></div>
      <div class="row"><span class="lbl">Loại phòng</span><span class="val">{{ $booking->room?->room_name ?? '—' }}</span></div>
      <div class="row"><span class="lbl">Check-in</span><span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span></div>
      <div class="row"><span class="lbl">Check-out</span><span class="val">{{ $booking->check_out?->format('d/m/Y') }}</span></div>
      <div class="row"><span class="lbl">Số tiền</span><span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></div>
    </div>

    <div class="note">
      💡 <strong>Phòng vẫn còn trống?</strong> Bạn có thể đặt lại ngay — chọn phòng, hoàn tất thanh toán trong 30 phút để giữ chỗ.
    </div>

    <a href="{{ config('app.url') }}/hotels/{{ $booking->room?->hotel_id }}" class="cta">Đặt phòng lại →</a>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
