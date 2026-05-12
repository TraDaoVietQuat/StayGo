<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hủy đặt phòng - StayGo Admin</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#7f1d1d;padding:20px 28px;display:flex;align-items:center;gap:12px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.badge{display:inline-block;background:#dc2626;color:#fff;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;margin-left:auto;}
.body{padding:24px 28px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;}
.val{font-weight:600;color:#1a202c;}
.refund-row .val{color:#dc2626;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🚫 Hủy đặt phòng</h1>
    <span class="badge">-1 booking</span>
  </div>
  <div class="body">
    <div class="row"><span class="lbl">Mã đơn</span><span class="val" style="color:#7f1d1d;letter-spacing:1px;">{{ $booking->order_code }}</span></div>
    <div class="row"><span class="lbl">Khách hàng</span><span class="val">{{ $booking->full_name }}</span></div>
    <div class="row"><span class="lbl">Điện thoại</span><span class="val">{{ $booking->phone }}</span></div>
    <div class="row"><span class="lbl">Email</span><span class="val">{{ $booking->email }}</span></div>
    <div class="row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Phòng</span><span class="val">{{ $booking->room?->room_name ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Check-in</span><span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span></div>
    <div class="row"><span class="lbl">Check-out</span><span class="val">{{ $booking->check_out?->format('d/m/Y') }}</span></div>
    <div class="row"><span class="lbl">Tổng tiền</span><span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></div>
    <div class="row refund-row"><span class="lbl">Trạng thái thanh toán</span><span class="val">{{ $booking->payment?->payment_status === 'completed' ? '⚠️ Đã thanh toán — cần hoàn tiền' : 'Chưa thanh toán' }}</span></div>
    <div class="row"><span class="lbl">Thời điểm hủy</span><span class="val">{{ now()->format('H:i d/m/Y') }}</span></div>
  </div>
  <div class="footer">
    <p>StayGo Admin — Email tự động, không reply</p>
  </div>
</div>
</body>
</html>
