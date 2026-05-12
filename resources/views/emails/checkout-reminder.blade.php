<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhắc nhở trả phòng - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#0066cc,#2563eb);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:32px 36px;}
.greeting{font-size:16px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.checkout-box{background:#eff6ff;border:2px solid #bfdbfe;border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:20px;}
.checkout-time{font-size:28px;font-weight:800;color:#0066cc;margin-bottom:4px;}
.checkout-label{font-size:13px;color:#4b5563;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:20px;}
.row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>⏰ StayGo</h1>
    <p>Nhắc nhở trả phòng hôm nay</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Hôm nay là ngày trả phòng của bạn. Vui lòng chuẩn bị check-out đúng giờ để tránh phát sinh thêm chi phí.</div>

    <div class="checkout-box">
      <div class="checkout-time">{{ $booking->room?->hotel?->checkout_time ?? '12:00' }}</div>
      <div class="checkout-label">⏰ Giờ trả phòng muộn nhất hôm nay</div>
    </div>

    <div class="info-box">
      <div class="row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span></div>
      <div class="row"><span class="lbl">Phòng</span><span class="val">{{ $booking->room?->room_name ?? '—' }}</span></div>
      <div class="row"><span class="lbl">Mã đặt phòng</span><span class="val">{{ $booking->order_code }}</span></div>
    </div>

    <div class="note">
      💡 <strong>Lưu ý khi trả phòng:</strong><br>
      • Kiểm tra kỹ tư trang cá nhân trước khi rời phòng<br>
      • Trả chìa khóa / thẻ phòng tại lễ tân<br>
      • Thanh toán các dịch vụ phát sinh (nếu có)
    </div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
