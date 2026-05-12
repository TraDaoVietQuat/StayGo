<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Thanh toán thành công - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#059669,#10b981);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:26px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.9);font-size:14px;}
.check{display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;background:rgba(255,255,255,.2);border-radius:50%;margin-bottom:16px;}
.check svg{width:36px;height:36px;}
.body{padding:32px 36px;}
.greeting{font-size:17px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;margin-bottom:16px;}
.info-box h3{margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;max-width:60%;}
.total{background:#f0fdf4;border:2px solid #6ee7b740;border-radius:10px;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.total .lbl{font-size:15px;font-weight:600;color:#1a202c;}
.total .price{font-size:22px;font-weight:800;color:#059669;}
.badge-paid{display:inline-block;background:#059669;color:#fff;border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;margin-bottom:8px;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <div class="check">
      <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1>🏨 StayGo</h1>
    <p>Thanh toán của bạn đã được xác nhận!</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Chúng tôi xác nhận đã nhận được thanh toán cho đặt phòng của bạn. Chúc bạn có kỳ nghỉ tuyệt vời!</div>

    <div style="text-align:center;margin-bottom:20px;">
      <span class="badge-paid">✅ Đã thanh toán thành công</span>
    </div>

    <div class="info-box">
      <h3>Thông tin đặt phòng</h3>
      <div class="row">
        <span class="lbl">Mã đơn</span>
        <span class="val" style="color:#059669;font-size:16px;letter-spacing:1px;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại phòng</span>
        <span class="val">{{ $booking->room?->room_name ?? '—' }}</span>
      </div>
      @php
        $nights = ($booking->check_in && $booking->check_out)
            ? $booking->check_in->diffInDays($booking->check_out) : 0;
        $isDay  = ($booking->stay_type ?? 'night') === 'day';
      @endphp
      <div class="row">
        <span class="lbl">Nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trả phòng</span>
        <span class="val">{{ $booking->check_out?->format('d/m/Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Thời gian</span>
        <span class="val">{{ $nights }} {{ $isDay ? 'ngày' : 'đêm' }}</span>
      </div>
    </div>

    <div class="info-box">
      <h3>Thanh toán</h3>
      @php $methods=['hotel'=>'Tại khách sạn','momo'=>'Ví MoMo','vnpay'=>'VNPay','bank'=>'Chuyển khoản','bank_transfer'=>'Chuyển khoản','zalopay'=>'ZaloPay','cod'=>'Khi nhận phòng']; @endphp
      <div class="row">
        <span class="lbl">Phương thức</span>
        <span class="val">{{ $methods[$booking->payment_method] ?? strtoupper($booking->payment_method) }}</span>
      </div>
      @if($booking->payment?->transaction_no)
      <div class="row">
        <span class="lbl">Mã giao dịch</span>
        <span class="val">{{ $booking->payment->transaction_no }}</span>
      </div>
      @endif
      @if($booking->payment?->paid_at)
      <div class="row">
        <span class="lbl">Thời gian thanh toán</span>
        <span class="val">{{ $booking->payment->paid_at->format('H:i d/m/Y') }}</span>
      </div>
      @endif
    </div>

    <div class="total">
      <span class="lbl">💰 Đã thanh toán</span>
      <span class="price">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
    </div>

    <div class="note">📋 Vui lòng lưu email này và xuất trình mã đặt phòng <strong>{{ $booking->order_code }}</strong> khi nhận phòng.</div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</p>
    <p>Hỗ trợ: supportstaygo@gmail.com | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
