<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng kê thanh toán — StayGo Partner</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#1B3A6B,#2563eb);padding:24px 28px;}
.hdr h1{margin:0;color:#fff;font-size:20px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:13px;}
.body{padding:24px 28px;}
.meta{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;margin-bottom:20px;}
.meta .row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #edf2f7;font-size:13px;}
.meta .row:last-child{border-bottom:none;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;}
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:20px 0 10px;}
/* Revenue table */
table.revenue{width:100%;border-collapse:collapse;font-size:14px;}
table.revenue th{background:#f1f5f9;text-align:left;padding:9px 12px;font-size:12px;color:#64748b;font-weight:700;border-bottom:2px solid #e2e8f0;}
table.revenue td{padding:9px 12px;border-bottom:1px solid #f1f5f9;color:#374151;}
table.revenue tr:last-child td{border-bottom:none;}
table.revenue td.amount{text-align:right;font-weight:600;}
table.revenue td.deduct{color:#dc2626;}
/* Summary box */
.summary{margin:20px 0;border:2px solid #2563eb;border-radius:8px;overflow:hidden;}
.summary .row{display:flex;justify-content:space-between;align-items:center;padding:10px 18px;border-bottom:1px solid #e2e8f0;font-size:14px;}
.summary .row:last-child{border-bottom:none;background:#eff6ff;padding:14px 18px;}
.summary .lbl{color:#374151;}
.summary .val{font-weight:700;}
.net-label{font-weight:800;font-size:15px;color:#1B3A6B;}
.net-val{font-weight:800;font-size:20px;color:#059669;}
/* Transfer info */
.transfer{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:14px 18px;margin:20px 0;}
.transfer p{margin:0 0 6px;font-size:13px;color:#065f46;}
.transfer p:last-child{margin-bottom:0;}
.transfer strong{color:#047857;}
/* Dashboard link */
.btn-dashboard{display:block;background:#1B3A6B;color:#fff;padding:12px 0;border-radius:7px;text-align:center;font-weight:700;font-size:14px;text-decoration:none;margin:20px 0;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>💰 Bảng kê thanh toán</h1>
    <p>
      {{ $payout->hotel?->name ?? '—' }} &nbsp;·&nbsp;
      Kỳ {{ $payout->period_start?->format('d/m/Y') }} – {{ $payout->period_end?->format('d/m/Y') }}
    </p>
  </div>

  <div class="body">

    {{-- Meta info --}}
    <div class="meta">
      <div class="row"><span class="lbl">Mã thanh toán</span><span class="val" style="color:#1B3A6B;">#{{ $payout->id }}</span></div>
      <div class="row"><span class="lbl">Ngày thanh toán</span><span class="val">{{ $payout->paid_at?->format('d/m/Y H:i') ?? '—' }}</span></div>
      <div class="row"><span class="lbl">Số booking</span><span class="val">{{ number_format($payout->booking_count) }} đơn</span></div>
      @if($payout->transfer_ref)
      <div class="row"><span class="lbl">Mã chuyển khoản</span><span class="val">{{ $payout->transfer_ref }}</span></div>
      @endif
    </div>

    {{-- Revenue breakdown --}}
    <p class="section-title">Chi tiết doanh thu</p>
    <table class="revenue">
      <thead>
        <tr>
          <th>Khoản mục</th>
          <th style="text-align:right;">Số tiền</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Tổng doanh thu gộp ({{ $payout->booking_count }} đơn)</td>
          <td class="amount">{{ number_format($payout->gross_revenue, 0, ',', '.') }}đ</td>
        </tr>
        <tr>
          <td>Phí dịch vụ StayGo ({{ number_format($payout->commission_rate, 0) }}%)</td>
          <td class="amount deduct">−{{ number_format($payout->commission_amount, 0, ',', '.') }}đ</td>
        </tr>
        @php
          $vatAmount = round($payout->commission_amount * 0.10, 0);
        @endphp
        <tr>
          <td>VAT trên phí dịch vụ (10%)</td>
          <td class="amount deduct">−{{ number_format($vatAmount, 0, ',', '.') }}đ</td>
        </tr>
      </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
      <div class="row">
        <span class="lbl">Doanh thu gộp</span>
        <span class="val">{{ number_format($payout->gross_revenue, 0, ',', '.') }}đ</span>
      </div>
      <div class="row">
        <span class="lbl">Phí dịch vụ + VAT</span>
        <span class="val" style="color:#dc2626;">−{{ number_format($payout->commission_amount + $vatAmount, 0, ',', '.') }}đ</span>
      </div>
      <div class="row">
        <span class="net-label">Bạn nhận được (thực nhận)</span>
        <span class="net-val">{{ number_format($payout->net_amount, 0, ',', '.') }}đ</span>
      </div>
    </div>

    {{-- Transfer confirmation --}}
    <div class="transfer">
      <p>✅ <strong>Đã chuyển khoản thành công</strong></p>
      <p>Số tiền <strong>{{ number_format($payout->net_amount, 0, ',', '.') }}đ</strong> đã được ghi có vào tài khoản ngân hàng đã đăng ký.</p>
      @if($payout->transfer_ref)
      <p>Mã tham chiếu: <strong>{{ $payout->transfer_ref }}</strong></p>
      @endif
      <p>Thời gian xử lý: Tiền sẽ phản ánh trong 1–2 ngày làm việc tùy ngân hàng.</p>
    </div>

    @if($payout->note)
    <p style="font-size:13px;color:#64748b;background:#f8fafc;border-radius:6px;padding:10px 14px;">
      📝 <strong>Ghi chú từ StayGo:</strong> {{ $payout->note }}
    </p>
    @endif

    <a href="{{ url('/partner/payouts') }}" class="btn-dashboard">Xem lịch sử thanh toán trên Partner Dashboard →</a>

  </div>

  <div class="footer">
    <p>StayGo Partner — Email tự động từ hệ thống tài chính, vui lòng không reply trực tiếp.</p>
    <p style="margin-top:4px;">Thắc mắc về thanh toán? Liên hệ <a href="mailto:partner@staygo.vn" style="color:#2563eb;">partner@staygo.vn</a></p>
  </div>
</div>
</body>
</html>
