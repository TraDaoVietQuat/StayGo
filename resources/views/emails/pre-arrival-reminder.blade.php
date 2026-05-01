<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Nhắc nhở check-in - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#e91e8c,#c2185b);padding:28px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:22px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:28px 36px;}
.greeting{font-size:16px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:20px;}
.checkin-box{background:linear-gradient(135deg,#fdf2f8,#fff);border:2px solid #e91e8c30;border-radius:12px;padding:18px 22px;margin-bottom:20px;text-align:center;}
.checkin-date{font-size:28px;font-weight:800;color:#e91e8c;}
.checkin-label{font-size:13px;color:#94a3b8;margin-top:4px;}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.info-row:last-child{border-bottom:none;}
.lbl{color:#718096;} .val{font-weight:600;color:#1a202c;}
.tips{background:#f0fdf4;border-left:4px solid #22c55e;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#166534;margin-top:16px;line-height:1.6;}
.footer{background:#f8fafc;padding:18px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 Nhắc nhở check-in ngày mai!</h1>
    <p>StayGo – Đặt phòng tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Đặt phòng của bạn sắp đến ngày check-in. Đây là thông tin lưu trú của bạn.</div>

    <div class="checkin-box">
      <div class="checkin-date">{{ $booking->check_in?->format('d/m/Y') }}</div>
      <div class="checkin-label">📅 Ngày check-in của bạn</div>
    </div>

    <div class="info-row"><span class="lbl">Mã đặt phòng</span><span class="val" style="color:#e91e8c">{{ $booking->order_code }}</span></div>
    <div class="info-row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name }}</span></div>
    <div class="info-row"><span class="lbl">Loại phòng</span><span class="val">{{ $booking->room?->room_name }}</span></div>
    <div class="info-row"><span class="lbl">Địa chỉ</span><span class="val">{{ $booking->room?->hotel?->address }}</span></div>
    <div class="info-row"><span class="lbl">Giờ nhận phòng</span><span class="val">Từ {{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</span></div>
    <div class="info-row"><span class="lbl">Trả phòng</span><span class="val">{{ $booking->check_out?->format('d/m/Y') }} trước {{ $booking->room?->hotel?->checkout_time ?? '12:00' }}</span></div>

    <div class="tips">
      ✅ <strong>Chuẩn bị check-in:</strong><br>
      • Mang theo CMND/CCCD để đăng ký lưu trú<br>
      • Xuất trình mã đặt phòng: <strong>{{ $booking->order_code }}</strong><br>
      • Liên hệ khách sạn nếu đến muộn: {{ $booking->room?->hotel?->phone ?? '—' }}
    </div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> – Chúc bạn có kỳ nghỉ tuyệt vời!</p>
    <p>© {{ date('Y') }} StayGo. Email tự động, vui lòng không reply.</p>
  </div>
</div>
</body>
</html>
