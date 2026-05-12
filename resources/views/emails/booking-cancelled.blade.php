<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hủy đặt phòng - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#dc2626,#ef4444);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:32px 36px;}
.greeting{font-size:16px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.info-box{background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.info-box h3{margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #fee2e2;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;}
.refund-box{background:#fef3c7;border:1.5px solid #fcd34d;border-radius:10px;padding:16px 20px;margin-bottom:20px;font-size:14px;color:#78350f;}
.refund-box strong{display:block;margin-bottom:4px;font-size:15px;}
.cta{display:block;text-align:center;background:#0066cc;color:#fff !important;text-decoration:none;padding:12px 32px;border-radius:10px;font-size:14px;font-weight:700;margin-bottom:8px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Đặt phòng của bạn đã được hủy</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Chúng tôi xác nhận đặt phòng dưới đây đã được hủy thành công theo yêu cầu của bạn.</div>

    <div class="info-box">
      <h3>Thông tin đặt phòng đã hủy</h3>
      <div class="row">
        <span class="lbl">Mã đơn</span>
        <span class="val" style="color:#dc2626;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại phòng</span>
        <span class="val">{{ $booking->room?->room_name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trả phòng</span>
        <span class="val">{{ $booking->check_out?->format('d/m/Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Tổng tiền</span>
        <span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
      </div>
      <div class="row">
        <span class="lbl">Thời gian hủy</span>
        <span class="val">{{ now()->format('H:i d/m/Y') }}</span>
      </div>
    </div>

    @if($booking->payment && $booking->payment->payment_status === 'completed')
    <div class="refund-box">
      <strong>💰 Chính sách hoàn tiền</strong>
      Nếu bạn đã thanh toán, chúng tôi sẽ xử lý hoàn tiền trong vòng <strong>3–5 ngày làm việc</strong>.
      Số tiền hoàn: <strong>{{ number_format($booking->total_price * 0.8, 0, ',', '.') }}đ</strong> (80% giá trị đơn).
    </div>
    @else
    <div class="refund-box">
      <strong>ℹ️ Lưu ý</strong>
      Đặt phòng chưa thanh toán — không phát sinh hoàn tiền.
    </div>
    @endif

    <a href="{{ config('app.url') }}/hotels" class="cta">Tìm kiếm khách sạn khác →</a>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
