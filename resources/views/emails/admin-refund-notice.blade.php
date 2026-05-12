<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hoàn tiền - StayGo Admin</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#065f46;padding:20px 28px;display:flex;align-items:center;gap:12px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.badge{display:inline-block;background:#059669;color:#fff;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;margin-left:auto;}
.body{padding:24px 28px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;}
.val{font-weight:600;color:#1a202c;}
.amount-row .val{color:#059669;font-size:18px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>💰 Hoàn tiền tự động</h1>
    <span class="badge">refunded</span>
  </div>
  <div class="body">
    <div class="row"><span class="lbl">Mã đơn</span><span class="val" style="color:#065f46;letter-spacing:1px;">{{ $booking->order_code }}</span></div>
    <div class="row"><span class="lbl">Khách hàng</span><span class="val">{{ $booking->full_name }}</span></div>
    <div class="row"><span class="lbl">Điện thoại</span><span class="val">{{ $booking->phone }}</span></div>
    <div class="row"><span class="lbl">Email</span><span class="val">{{ $booking->email }}</span></div>
    <div class="row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Check-in</span><span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span></div>
    <div class="row"><span class="lbl">Check-out</span><span class="val">{{ $booking->check_out?->format('d/m/Y') }}</span></div>
    <div class="row"><span class="lbl">Giá trị đơn gốc</span><span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></div>
    <div class="row amount-row"><span class="lbl">Số tiền hoàn (80%)</span><span class="val">{{ number_format($booking->refund_amount ?? $booking->total_price * 0.8, 0, ',', '.') }}đ</span></div>
    <div class="row"><span class="lbl">Yêu cầu lúc</span><span class="val">{{ $booking->refund_requested_at?->format('H:i d/m/Y') ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Hoàn tiền lúc</span><span class="val">{{ now()->format('H:i d/m/Y') }}</span></div>
  </div>
  <div class="footer">
    <p>StayGo Admin — Hoàn tiền tự động sau 3 ngày, không reply</p>
  </div>
</div>
</body>
</html>
