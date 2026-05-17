<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>{{ $autoConfirm ? 'Booking mới' : 'Cần xác nhận booking' }} — StayGo Partner</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:580px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{padding:22px 28px;}
.hdr-auto{background:linear-gradient(135deg,#059669,#10b981);}
.hdr-manual{background:linear-gradient(135deg,#d97706,#f59e0b);}
.hdr h1{margin:0;color:#fff;font-size:19px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.88);font-size:13px;}
.body{padding:24px 28px;}
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:0 0 10px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;}
.val{font-weight:600;color:#1a202c;}
.code{color:#1B3A6B;letter-spacing:1px;}
.total{font-size:16px;color:#059669;}
.divider{border:none;border-top:1px solid #e2e8f0;margin:20px 0;}
/* Commission box */
.commission{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;margin:20px 0;}
.commission .row{border-bottom:1px solid #e9edf1;font-size:13px;}
.commission .row:last-child{border-bottom:none;padding-top:12px;}
.net-label{font-weight:700;color:#1a202c;font-size:14px;}
.net-val{font-weight:800;font-size:16px;color:#059669;}
/* Deadline box */
.deadline{background:#fffbeb;border:2px solid #f59e0b;border-radius:8px;padding:14px 18px;margin:20px 0;text-align:center;}
.deadline .icon{font-size:28px;line-height:1;}
.deadline p{margin:8px 0 0;font-size:13px;color:#92400e;}
.deadline strong{font-size:15px;color:#78350f;}
/* Buttons */
.btn-row{display:flex;gap:12px;margin:20px 0;}
.btn{display:inline-block;padding:12px 22px;border-radius:7px;font-weight:700;font-size:14px;text-decoration:none;text-align:center;flex:1;}
.btn-confirm{background:#059669;color:#fff;}
.btn-view{background:#e2e8f0;color:#334155;}
.btn-dashboard{display:block;background:#1B3A6B;color:#fff;padding:12px 0;border-radius:7px;text-align:center;font-weight:700;font-size:14px;text-decoration:none;margin:20px 0;}
/* Notice */
.notice-auto{background:#ecfdf5;border-left:4px solid #059669;padding:12px 16px;border-radius:0 6px 6px 0;font-size:13px;color:#065f46;}
.notice-manual{background:#fff7ed;border-left:4px solid #f97316;padding:12px 16px;border-radius:0 6px 6px 0;font-size:13px;color:#7c2d12;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  {{-- ===== HEADER ===== --}}
  <div class="hdr {{ $autoConfirm ? 'hdr-auto' : 'hdr-manual' }}">
    @if($autoConfirm)
      <h1>✓ Có booking mới — đã xử lý tự động</h1>
      <p>Đơn đã được xác nhận. Không cần hành động thêm.</p>
    @else
      <h1>⏰ Booking mới cần xác nhận</h1>
      <p>Vui lòng phản hồi trong vòng 2 giờ để tránh đơn bị tự động hủy.</p>
    @endif
  </div>

  <div class="body">

    {{-- ===== DEADLINE WARNING (Variant B) ===== --}}
    @unless($autoConfirm)
    <div class="deadline">
      <div class="icon">⏳</div>
      <strong>Còn 2 giờ để phản hồi</strong>
      <p>Đơn nhận lúc {{ now()->format('H:i — d/m/Y') }}. Hạn chót: <strong>{{ now()->addHours(2)->format('H:i d/m/Y') }}</strong></p>
    </div>
    @endunless

    {{-- ===== BOOKING DETAILS ===== --}}
    <p class="section-title">Thông tin đặt phòng</p>
    <div class="row"><span class="lbl">Mã đơn</span><span class="val code">{{ $booking->order_code }}</span></div>
    <div class="row"><span class="lbl">Khách hàng</span><span class="val">{{ $booking->full_name }}</span></div>
    <div class="row"><span class="lbl">Điện thoại</span><span class="val">{{ $booking->phone }}</span></div>
    <div class="row"><span class="lbl">Loại phòng</span><span class="val">{{ $booking->room?->room_name ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Check-in</span><span class="val">{{ $booking->check_in?->format('d/m/Y') ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Check-out</span><span class="val">{{ $booking->check_out?->format('d/m/Y') ?? '—' }}</span></div>
    <div class="row"><span class="lbl">Loại ở</span><span class="val">{{ $booking->stay_type === 'day' ? 'Theo ngày' : 'Qua đêm' }}</span></div>
    <div class="row"><span class="lbl">Thanh toán</span><span class="val">{{ strtoupper($booking->payment_method) }}</span></div>
    @if($booking->note)
    <div class="row"><span class="lbl">Ghi chú</span><span class="val" style="text-align:right;max-width:65%;">{{ $booking->note }}</span></div>
    @endif
    <div class="row"><span class="lbl">Tổng tiền khách trả</span><span class="val total">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></div>

    {{-- ===== COMMISSION BREAKDOWN ===== --}}
    <hr class="divider">
    <p class="section-title">Phân chia doanh thu</p>
    <div class="commission">
      <div class="row">
        <span class="lbl">Doanh thu gộp</span>
        <span class="val">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
      </div>
      <div class="row">
        <span class="lbl">Phí dịch vụ StayGo ({{ number_format($commissionRate, 0) }}%)</span>
        <span class="val" style="color:#dc2626;">−{{ number_format($commissionAmount, 0, ',', '.') }}đ</span>
      </div>
      <div class="row">
        <span class="net-label">Bạn nhận được</span>
        <span class="net-val">{{ number_format($netAmount, 0, ',', '.') }}đ</span>
      </div>
    </div>

    {{-- ===== CTA BUTTONS ===== --}}
    @if($autoConfirm)
      {{-- Variant A: single dashboard link --}}
      <div class="notice-auto">
        ✓ Booking đã được hệ thống tự động xác nhận. Khách đã nhận email xác nhận. Vui lòng chuẩn bị đón tiếp khách đúng ngày check-in.
      </div>
      <a href="{{ url('/partner/bookings/' . $booking->id . '/edit') }}" class="btn-dashboard">Xem chi tiết trên Partner Dashboard →</a>
    @else
      {{-- Variant B: confirm + view buttons --}}
      <div class="btn-row">
        <a href="{{ url('/partner/bookings/' . $booking->id . '/edit') }}" class="btn btn-confirm">✓ Xác nhận đơn</a>
        <a href="{{ url('/partner/bookings/' . $booking->id . '/edit') }}" class="btn btn-view">Xem chi tiết</a>
      </div>
      <div class="notice-manual">
        ⚠️ Nếu không phản hồi trước <strong>{{ now()->addHours(2)->format('H:i ngày d/m/Y') }}</strong>, đơn sẽ tự động bị hủy và khách được hoàn tiền. Điều này ảnh hưởng đến tỷ lệ chấp nhận (acceptance rate) của khách sạn.
      </div>
    @endif

  </div>

  <div class="footer">
    <p>StayGo Partner — Email tự động từ hệ thống, vui lòng không reply trực tiếp.</p>
  </div>
</div>
</body>
</html>
