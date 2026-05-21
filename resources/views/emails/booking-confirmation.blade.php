<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Xác nhận đặt phòng - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
/* Header */
.hdr{background:linear-gradient(135deg,#e91e8c,#c2185b);padding:36px;text-align:center;}
.hdr-logo{font-size:28px;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.5px;}
.hdr-sub{color:rgba(255,255,255,.85);font-size:14px;margin:0 0 16px;}
.confirm-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.5);border-radius:24px;padding:7px 20px;color:#fff;font-size:14px;font-weight:700;}
/* Hotel image (variant B only) */
.hotel-img{width:100%;height:200px;object-fit:cover;display:block;}
/* Body */
.body{padding:32px 36px;}
.greeting{font-size:18px;font-weight:700;color:#1a202c;margin:0 0 6px;}
.intro{color:#64748b;font-size:14px;line-height:1.65;margin:0 0 28px;}
/* Info boxes */
.section-label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin:0 0 10px;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 22px;margin-bottom:20px;}
.row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;max-width:60%;}
/* Total bar */
.total-bar{background:linear-gradient(135deg,#fdf2f8,#fce7f3);border:2px solid #f9a8d430;border-radius:12px;padding:16px 22px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.total-label{font-size:15px;font-weight:600;color:#1a202c;}
.total-price{font-size:24px;font-weight:800;color:#e91e8c;}
/* CTA */
.cta-wrap{text-align:center;margin-bottom:24px;}
.cta{display:inline-block;background:linear-gradient(135deg,#e91e8c,#c2185b);color:#fff!important;text-decoration:none;padding:14px 40px;border-radius:12px;font-size:15px;font-weight:700;letter-spacing:.3px;}
/* Notice */
.notice{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 10px 10px 0;padding:13px 16px;font-size:13px;color:#78350f;margin-bottom:20px;line-height:1.6;}
/* Can-do list */
.cando{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;}
.cando p{margin:0 0 8px;font-size:13px;font-weight:700;color:#15803d;}
.cando ul{margin:0;padding-left:18px;}
.cando li{font-size:13px;color:#166534;line-height:1.8;}
/* Variant A highlight */
.free-cancel-banner{background:#dcfce7;border:1.5px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:#15803d;}
.free-cancel-banner strong{font-size:14px;}
/* Footer */
.footer{background:#f8fafc;padding:22px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#e91e8c;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  {{-- HEADER --}}
  <div class="hdr">
    <div class="hdr-logo">🏨 StayGo</div>
    <div class="hdr-sub">Nền tảng đặt phòng khách sạn & resort Việt Nam</div>
    <div class="confirm-badge">✓ Đặt phòng đã xác nhận</div>
  </div>

  {{-- VARIANT B: Hotel image --}}
  @if($variant === 'b')
    @php $hotelImg = $booking->room?->hotel?->thumbnail ?? $booking->room?->hotel?->cover_image ?? null; @endphp
    @if($hotelImg)
      <img src="{{ str_starts_with($hotelImg, 'http') ? $hotelImg : config('app.url').'/storage/'.$hotelImg }}"
           alt="{{ $booking->room?->hotel?->name }}" class="hotel-img">
    @endif
  @endif

  <div class="body">

    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">
      Tuyệt vời! Đặt phòng của bạn đã được xác nhận thành công. Chúng tôi rất vui được đồng hành cùng chuyến đi của bạn.
    </div>

    {{-- VARIANT A: Nhấn mạnh hủy miễn phí --}}
    @if($variant === 'a')
      @php
        $policy = $booking->room?->hotel?->cancellation_policy ?? '';
        $hasFreeCancel = stripos($policy, 'miễn phí') !== false || stripos($policy, 'free') !== false;
      @endphp
      @if($hasFreeCancel)
      <div class="free-cancel-banner">
        <span style="font-size:22px;">🛡️</span>
        <div><strong>Hủy miễn phí — không mất phí!</strong><br>Bạn có thể hủy phòng này mà không mất bất kỳ khoản phí nào nếu hủy đúng hạn.</div>
      </div>
      @endif
    @endif

    {{-- BOOKING DETAIL --}}
    <div class="section-label">Chi tiết đặt phòng</div>
    <div class="info-box">
      <div class="row">
        <span class="lbl">Mã đặt phòng</span>
        <span class="val" style="color:#e91e8c;font-size:17px;letter-spacing:1px;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Địa chỉ</span>
        <span class="val">{{ $booking->room?->hotel?->address ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại phòng</span>
        <span class="val">{{ $booking->room?->room_name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }} từ {{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trả phòng</span>
        <span class="val">{{ $booking->check_out?->format('d/m/Y') }} trước {{ $booking->room?->hotel?->checkout_time ?? '12:00' }}</span>
      </div>
      <div class="row">
        <span class="lbl">{{ $booking->stay_type === 'day' ? 'Số ngày' : 'Số đêm' }}</span>
        @php $nights = ($booking->check_in && $booking->check_out) ? $booking->check_in->diffInDays($booking->check_out) : 1; @endphp
        <span class="val">{{ $nights }} {{ $booking->stay_type === 'day' ? 'ngày' : 'đêm' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Phương thức TT</span>
        @php
          $methods = [
            'hotel'         => '🏨 Tại khách sạn',
            'momo'          => '🟣 Ví MoMo',
            'vnpay'         => '🔵 VNPay',
            'bank'          => '🏦 Chuyển khoản ngân hàng',
            'bank_transfer' => '🏦 Chuyển khoản ngân hàng',
            'zalopay'       => '🔵 ZaloPay',
            'cod'           => '💵 Tiền mặt khi nhận phòng',
            'card'          => '💳 Thẻ quốc tế (Visa/MC)',
            'vietqr'        => '📱 VietQR',
            'wallet'        => '💰 Ví điện tử',
          ];
        @endphp
        <span class="val">{{ $methods[$booking->payment_method] ?? $booking->payment_method }}</span>
      </div>
      @if($booking->discount_code)
      <div class="row">
        <span class="lbl">Mã giảm giá</span>
        <span class="val" style="color:#16a34a;">{{ $booking->discount_code }} (−{{ $booking->discount_percent }}%)</span>
      </div>
      <div class="row">
        <span class="lbl">Số tiền giảm</span>
        <span class="val" style="color:#16a34a;">−{{ number_format($booking->discount_amount, 0, ',', '.') }}đ</span>
      </div>
      @endif
    </div>

    {{-- TOTAL --}}
    <div class="total-bar">
      <span class="total-label">💰 Tổng thanh toán</span>
      <span class="total-price">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
    </div>

    {{-- CTA --}}
    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/dat-phong-cua-toi" class="cta">Xem chi tiết đặt phòng →</a>
    </div>

    {{-- IMPORTANT NOTES --}}
    <div class="notice">
      <strong>⚠️ Lưu ý quan trọng:</strong><br>
      • Vui lòng mang theo CCCD/Passport khi nhận phòng<br>
      • Giữ email này (hoặc mã <strong>{{ $booking->order_code }}</strong>) để xuất trình khi check-in<br>
      • Liên hệ khách sạn trước nếu dự kiến đến muộn sau 22:00: <strong>{{ $booking->room?->hotel?->phone ?? '—' }}</strong>
    </div>

    {{-- CANDO --}}
    <div class="cando">
      <p>✅ Bạn có thể:</p>
      <ul>
        <li>Thêm yêu cầu đặc biệt (giường phụ, tầng cao, v.v.) qua trang Đặt phòng của tôi</li>
        <li>Chia sẻ thông tin đặt phòng với người đồng hành</li>
        <li>Xem chính sách hủy phòng và yêu cầu hủy nếu cần</li>
      </ul>
    </div>

    @if($booking->note)
    <div class="notice">📝 <strong>Ghi chú của bạn:</strong> {{ $booking->note }}</div>
    @endif

  </div>

  <div class="footer">
    <p><strong>StayGo</strong> — Đà Lạt • Nha Trang • Vũng Tàu • Đà Nẵng</p>
    <p>Cần hỗ trợ? <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    <p style="margin-top:8px;">© {{ date('Y') }} StayGo. Email tự động, vui lòng không reply trực tiếp.</p>
  </div>

</div>
</body>
</html>
