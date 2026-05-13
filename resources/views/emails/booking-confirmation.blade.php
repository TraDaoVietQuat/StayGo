<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Xác nhận đặt phòng - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#e91e8c,#c2185b);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:26px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);border-radius:20px;padding:4px 14px;font-size:13px;margin-top:12px;}
.body{padding:32px 36px;}
.greeting{font-size:17px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.info-box h3{margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;}
.total{background:#fdf2f8;border:2px solid #e91e8c30;border-radius:10px;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.total .lbl{font-size:15px;font-weight:600;color:#1a202c;}
.total .price{font-size:22px;font-weight:800;color:#e91e8c;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Đặt phòng của bạn đã được ghi nhận thành công!</p>
    <div class="badge">✓ Xác nhận đặt phòng</div>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="sub">Cảm ơn bạn đã tin tưởng StayGo. Dưới đây là chi tiết đặt phòng của bạn.</div>

    <div class="info-box">
      <h3>Mã đặt phòng</h3>
      <div class="row">
        <span class="lbl">Mã đơn</span>
        <span class="val" style="color:#e91e8c;font-size:18px;letter-spacing:1px;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trạng thái</span>
        <span class="val" style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:12px;font-size:12px;">⏳ Chờ xác nhận</span>
      </div>
      <div class="row">
        <span class="lbl">Ngày đặt</span>
        <span class="val">{{ $booking->created_at?->format('d/m/Y H:i') }}</span>
      </div>
    </div>

    <div class="info-box">
      <h3>Thông tin lưu trú</h3>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại phòng</span>
        <span class="val">{{ $booking->room?->room_name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Địa chỉ</span>
        <span class="val">{{ $booking->room?->hotel?->address ?? '—' }}</span>
      </div>
      @php $nights = $booking->check_in && $booking->check_out ? $booking->check_in->diffInDays($booking->check_out) : 0; @endphp
      <div class="row">
        <span class="lbl">Nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }} • từ {{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trả phòng</span>
        <span class="val">{{ $booking->check_out?->format('d/m/Y') }} • trước {{ $booking->room?->hotel?->checkout_time ?? '12:00' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Số đêm</span>
        <span class="val">{{ $nights }} đêm</span>
      </div>
    </div>

    <div class="info-box">
      <h3>Thanh toán</h3>
      @php $methods=['hotel'=>'Tại khách sạn','momo'=>'Ví MoMo','vnpay'=>'VNPay','bank'=>'Chuyển khoản','bank_transfer'=>'Chuyển khoản','zalopay'=>'ZaloPay','cod'=>'Khi nhận phòng']; @endphp
      <div class="row">
        <span class="lbl">Phương thức</span>
        <span class="val">{{ $methods[$booking->payment_method] ?? strtoupper($booking->payment_method) }}</span>
      </div>
    </div>

    <div class="total">
      <span class="lbl">💰 Tổng tiền thanh toán</span>
      <span class="price">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
    </div>

    @if($booking->note)
    <div class="note"><strong>Ghi chú:</strong> {{ $booking->note }}</div>
    @endif

    <div class="note">⚠️ Vui lòng giữ email này để xuất trình khi nhận phòng. Mã đặt phòng: <strong>{{ $booking->order_code }}</strong></div>

    <div style="text-align:center;margin-top:20px;">
      <a href="{{ config('app.url') }}/dat-phong-cua-toi" style="display:inline-block;background:#f8fafc;color:#64748b;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:13px;border:1px solid #e2e8f0;">
        Cần hủy phòng? Vào <strong>Đặt phòng của tôi</strong> →
      </a>
    </div>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</p>
    <p>© {{ date('Y') }} StayGo. Email tự động, vui lòng không reply.</p>
  </div>
</div>
</body>
</html>
