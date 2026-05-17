<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Hủy đặt phòng - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.body{padding:32px 36px;}
.greeting{font-size:17px;font-weight:700;color:#1a202c;margin:0 0 6px;}
.intro{color:#64748b;font-size:14px;line-height:1.65;margin:0 0 24px;}
.info-box{border-radius:12px;padding:20px 22px;margin-bottom:20px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(0,0,0,.06);font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;} .val{font-weight:600;color:#1a202c;text-align:right;}
.cta{display:inline-block;text-decoration:none;padding:13px 36px;border-radius:10px;font-size:14px;font-weight:700;color:#fff!important;}
.footer{background:#f8fafc;padding:22px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#e91e8c;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

@if($freeCancellation)
{{-- ══════════════════ CASE A: HỦY MIỄN PHÍ — HOÀN TIỀN 100% ══════════════════ --}}
  <div style="background:linear-gradient(135deg,#16a34a,#15803d);padding:34px 36px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:#fff;margin:0 0 6px;">🏨 StayGo</div>
    <div style="color:rgba(255,255,255,.85);font-size:14px;margin:0 0 14px;">Xác nhận hủy đặt phòng</div>
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.5);border-radius:24px;padding:7px 20px;color:#fff;font-size:13px;font-weight:700;">
      ✓ Hủy thành công · Hoàn tiền 100%
    </div>
  </div>

  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">
      Chúng tôi đã xác nhận hủy đặt phòng của bạn theo yêu cầu. Vì bạn hủy trong thời gian miễn phí, toàn bộ số tiền sẽ được hoàn lại.
    </div>

    <div class="section-label" style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:10px;">Xác nhận hủy</div>
    <div class="info-box" style="background:#f0fdf4;border:1px solid #bbf7d0;">
      <div class="row">
        <span class="lbl">Mã đặt phòng</span>
        <span class="val" style="color:#16a34a;">{{ $booking->order_code }}</span>
      </div>
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Ngày nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Thời gian hủy</span>
        <span class="val">{{ now()->format('H:i d/m/Y') }}</span>
      </div>
    </div>

    @if($refundAmount > 0)
    <div style="background:linear-gradient(135deg,#dcfce7,#f0fdf4);border:2px solid #86efac;border-radius:12px;padding:20px 22px;margin-bottom:20px;">
      <div style="font-size:13px;font-weight:700;color:#15803d;margin-bottom:12px;">💚 Thông tin hoàn tiền</div>
      <div class="row" style="border-color:#bbf7d0;">
        <span class="lbl">Số tiền hoàn</span>
        <span class="val" style="color:#15803d;font-size:20px;">{{ number_format($refundAmount, 0, ',', '.') }}đ</span>
      </div>
      <div class="row" style="border-color:#bbf7d0;">
        <span class="lbl">Phương thức hoàn</span>
        @php $methods=['hotel'=>'Tại khách sạn','momo'=>'Ví MoMo','vnpay'=>'VNPay','bank'=>'Chuyển khoản','bank_transfer'=>'Chuyển khoản','zalopay'=>'ZaloPay','cod'=>'Không phát sinh']; @endphp
        <span class="val">{{ $methods[$booking->payment_method] ?? strtoupper($booking->payment_method) }}</span>
      </div>
      <div class="row" style="border-bottom:none;">
        <span class="lbl">Thời gian dự kiến</span>
        <span class="val">{{ $refundEta }}</span>
      </div>
    </div>
    <div style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;">
      ⚠️ Thời gian hoàn tiền thực tế phụ thuộc vào ngân hàng/ví điện tử của bạn. Nếu sau <strong>{{ $refundEta }}</strong> chưa nhận được, vui lòng liên hệ hỗ trợ.
    </div>
    @else
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;font-size:13px;color:#166534;margin-bottom:20px;">
      ℹ️ Đặt phòng chưa thanh toán — không phát sinh hoàn tiền.
    </div>
    @endif

    <div style="text-align:center;margin-bottom:20px;">
      <div style="font-size:14px;color:#64748b;margin-bottom:12px;">Kế hoạch thay đổi? Hãy để chúng tôi giúp bạn!</div>
      <a href="{{ config('app.url') }}/hotels" class="cta" style="background:#16a34a;">Tìm khách sạn khác →</a>
    </div>
  </div>

@else
{{-- ══════════════════ CASE B: HỦY NGOÀI THỜI GIAN MIỄN PHÍ ══════════════════ --}}
  <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);padding:34px 36px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:#fff;margin:0 0 6px;">🏨 StayGo</div>
    <div style="color:rgba(255,255,255,.85);font-size:14px;margin:0 0 14px;">Xác nhận hủy đặt phòng</div>
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.4);border-radius:24px;padding:7px 20px;color:#fff;font-size:13px;font-weight:700;">
      Đã hủy · Lưu ý chính sách hoàn tiền
    </div>
  </div>

  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">
      Chúng tôi đã xử lý yêu cầu hủy đặt phòng của bạn. Vì thời gian hủy nằm ngoài chính sách miễn phí, một phần phí sẽ được áp dụng theo quy định.
    </div>

    <div class="section-label" style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:10px;">Thông tin hủy phòng</div>
    <div class="info-box" style="background:#fef2f2;border:1px solid #fecaca;">
      <div class="row" style="border-color:#fee2e2;">
        <span class="lbl">Mã đặt phòng</span>
        <span class="val" style="color:#dc2626;">{{ $booking->order_code }}</span>
      </div>
      <div class="row" style="border-color:#fee2e2;">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $booking->room?->hotel?->name ?? '—' }}</span>
      </div>
      <div class="row" style="border-color:#fee2e2;">
        <span class="lbl">Ngày nhận phòng</span>
        <span class="val">{{ $booking->check_in?->format('d/m/Y') }}</span>
      </div>
      <div class="row" style="border-color:#fee2e2;">
        <span class="lbl">Hủy lúc</span>
        <span class="val">{{ now()->format('H:i d/m/Y') }}</span>
      </div>
      <div class="row" style="border-color:#fee2e2;">
        <span class="lbl">Tổng đã thanh toán</span>
        <span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
      </div>
    </div>

    @if($refundAmount > 0)
    <div style="background:#fef3c7;border:1.5px solid #fcd34d;border-radius:12px;padding:18px 22px;margin-bottom:20px;">
      <div style="font-size:13px;font-weight:700;color:#92400e;margin-bottom:12px;">💰 Hoàn tiền một phần (theo chính sách)</div>
      <div class="row" style="border-color:#fde68a;">
        <span class="lbl">Phí hủy phòng</span>
        <span class="val" style="color:#dc2626;">{{ number_format($booking->total_price - $refundAmount, 0, ',', '.') }}đ</span>
      </div>
      <div class="row" style="border-bottom:none;">
        <span class="lbl">Số tiền hoàn lại</span>
        <span class="val" style="color:#15803d;font-size:18px;">{{ number_format($refundAmount, 0, ',', '.') }}đ</span>
      </div>
    </div>
    @else
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;font-size:13px;color:#991b1b;margin-bottom:20px;">
      ⚠️ Theo chính sách hủy, đặt phòng này không được hoàn tiền.
    </div>
    @endif

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;font-size:13px;color:#475569;margin-bottom:20px;line-height:1.7;">
      Chúng tôi hiểu rằng đôi khi kế hoạch thay đổi ngoài ý muốn. Nếu có hoàn cảnh đặc biệt, hãy liên hệ đội hỗ trợ — chúng tôi sẽ xem xét từng trường hợp cụ thể.
    </div>

    <div style="text-align:center;margin-bottom:20px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="mailto:{{ config('mail.from.address') }}" class="cta" style="background:#dc2626;">📧 Liên hệ hỗ trợ hoàn tiền</a>
      <a href="{{ config('app.url') }}/hotels" class="cta" style="background:#64748b;">Tìm khách sạn khác</a>
    </div>

    <div style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;">
      💡 Lần tới, hãy chọn gói <strong>"Hủy miễn phí"</strong> để linh hoạt hơn khi kế hoạch thay đổi!
    </div>
  </div>
@endif

  <div class="footer">
    <p><strong>StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hỗ trợ: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> | © {{ date('Y') }} StayGo</p>
  </div>

</div>
</body>
</html>
