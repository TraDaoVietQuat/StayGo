<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hoàn tiền thành công - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#7c3aed,#8b5cf6);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:32px 36px;}
.greeting{font-size:16px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.info-box{background:#f5f3ff;border:1px solid #ddd6fe;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.info-box h3{margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #ede9fe;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;}
.amount-box{background:#f0fdf4;border:2px solid #6ee7b7;border-radius:10px;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.amount-box .lbl{font-size:15px;font-weight:600;color:#1a202c;}
.amount-box .price{font-size:22px;font-weight:800;color:#059669;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Hoàn tiền đã được xử lý thành công</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Yêu cầu hoàn tiền cho đặt phòng của bạn đã được xử lý thành công. Số tiền sẽ được chuyển về tài khoản trong 3–5 ngày làm việc.</div>

    <div class="info-box">
      <h3>Chi tiết hoàn tiền</h3>
      <div class="row">
        <span class="lbl">Mã đơn</span>
        <span class="val" style="color:#7c3aed;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Phương thức hoàn</span>
        <span class="val">Về nguồn thanh toán gốc</span>
      </div>
      <div class="row">
        <span class="lbl">Ngày xử lý</span>
        <span class="val">{{ now()->format('d/m/Y') }}</span>
      </div>
    </div>

    <div class="amount-box">
      <span class="lbl">💰 Số tiền hoàn trả</span>
      <span class="price">{{ number_format($booking->refund_amount ?? 0, 0, ',', '.') }}đ</span>
    </div>

    <div class="note">⏳ Thời gian nhận tiền phụ thuộc vào ngân hàng/ví điện tử của bạn (thường 3–5 ngày làm việc). Nếu sau 7 ngày chưa nhận được, vui lòng liên hệ supportstaygo@gmail.com.</div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
