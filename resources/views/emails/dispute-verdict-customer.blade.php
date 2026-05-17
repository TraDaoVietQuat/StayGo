<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Kết quả khiếu nại — StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{padding:32px 36px;text-align:center;}
.hdr.approve{background:linear-gradient(135deg,#10b981,#059669);}
.hdr.partial{background:linear-gradient(135deg,#3b82f6,#2563eb);}
.hdr.voucher{background:linear-gradient(135deg,#8b5cf6,#7c3aed);}
.hdr.rejected{background:linear-gradient(135deg,#6b7280,#4b5563);}
.hdr.hotel{background:linear-gradient(135deg,#f59e0b,#d97706);}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);border-radius:20px;padding:5px 16px;font-size:13px;font-weight:600;margin-top:12px;}
.body{padding:32px 36px;}
.greeting{font-size:17px;font-weight:600;color:#1a202c;margin-bottom:6px;}
.sub{color:#718096;font-size:14px;margin-bottom:24px;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.info-box h3{margin:0 0 12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;}
.val{font-weight:600;color:#1a202c;text-align:right;max-width:60%;}
.verdict-box{border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.verdict-box.approve{background:#f0fdf4;border:2px solid #bbf7d0;}
.verdict-box.partial{background:#eff6ff;border:2px solid #bfdbfe;}
.verdict-box.voucher{background:#f5f3ff;border:2px solid #ddd6fe;}
.verdict-box.rejected{background:#f9fafb;border:2px solid #e5e7eb;}
.verdict-box.hotel{background:#fffbeb;border:2px solid #fde68a;}
.verdict-title{font-size:16px;font-weight:700;margin-bottom:8px;}
.verdict-title.approve{color:#065f46;}
.verdict-title.partial{color:#1e40af;}
.verdict-title.voucher{color:#4c1d95;}
.verdict-title.rejected{color:#374151;}
.verdict-title.hotel{color:#92400e;}
.verdict-detail{font-size:14px;color:#374151;line-height:1.6;}
.note{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#e91e8c;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  {{-- Header theo loại phán quyết --}}
  @php
    $cls = match($dispute->verdict) {
      'refund_full'      => 'approve',
      'refund_partial'   => 'partial',
      'voucher'          => 'voucher',
      'hotel_compensate' => 'hotel',
      default            => 'rejected',
    };
    $icon = match($dispute->verdict) {
      'refund_full'      => '✅',
      'refund_partial'   => '💰',
      'voucher'          => '🎟️',
      'hotel_compensate' => '🏨',
      default            => '📋',
    };
  @endphp

  <div class="hdr {{ $cls }}">
    <h1>⚖️ StayGo — Kết quả khiếu nại</h1>
    <p>Tranh chấp #{{ $dispute->id }}: {{ $dispute->title }}</p>
    <div class="badge">{{ $icon }} {{ $verdictLabel }}</div>
  </div>

  <div class="body">
    <div class="greeting">Xin chào, {{ $notifiable->full_name ?? $notifiable->name ?? 'Quý khách' }}!</div>
    <div class="sub">
      Đội ngũ xử lý tranh chấp của StayGo đã hoàn tất xem xét khiếu nại của bạn.
      Dưới đây là kết quả chính thức.
    </div>

    {{-- Thông tin khiếu nại --}}
    <div class="info-box">
      <h3>Thông tin khiếu nại</h3>
      <div class="row">
        <span class="lbl">Mã tranh chấp</span>
        <span class="val">#{{ $dispute->id }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại khiếu nại</span>
        <span class="val">{{ \App\Models\Dispute::typeLabels()[$dispute->type] ?? $dispute->type }}</span>
      </div>
      @if($dispute->booking)
      <div class="row">
        <span class="lbl">Mã đặt phòng</span>
        <span class="val">{{ $dispute->booking->order_code }}</span>
      </div>
      @endif
      @if($dispute->hotel)
      <div class="row">
        <span class="lbl">Khách sạn</span>
        <span class="val">{{ $dispute->hotel->name }}</span>
      </div>
      @endif
      <div class="row">
        <span class="lbl">Ngày tiếp nhận</span>
        <span class="val">{{ $dispute->created_at->format('d/m/Y H:i') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Ngày giải quyết</span>
        <span class="val">{{ $dispute->resolved_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
      </div>
    </div>

    {{-- Phán quyết --}}
    <div class="verdict-box {{ $cls }}">
      <div class="verdict-title {{ $cls }}">{{ $icon }} Phán quyết: {{ $verdictLabel }}</div>
      @if($dispute->verdict === 'refund_full' && $dispute->refund_amount)
        <div class="verdict-detail">
          Số tiền hoàn trả: <strong>{{ number_format($dispute->refund_amount) }} VNĐ</strong><br>
          Thời gian hoàn tiền dự kiến: <strong>3–7 ngày làm việc</strong> tùy phương thức thanh toán gốc.
        </div>
      @elseif($dispute->verdict === 'refund_partial' && $dispute->refund_amount)
        <div class="verdict-detail">
          Số tiền hoàn trả: <strong>{{ number_format($dispute->refund_amount) }} VNĐ</strong>
          @if($dispute->refund_percentage) ({{ $dispute->refund_percentage }}%) @endif<br>
          Thời gian hoàn tiền dự kiến: <strong>3–7 ngày làm việc</strong>.
        </div>
      @elseif($dispute->verdict === 'voucher' && $dispute->voucher_amount)
        <div class="verdict-detail">
          Voucher trị giá <strong>{{ number_format($dispute->voucher_amount) }} VNĐ</strong>
          sẽ được cấp vào tài khoản StayGo của bạn trong vòng 24 giờ.
          Voucher có hiệu lực 6 tháng kể từ ngày cấp.
        </div>
      @elseif($dispute->verdict === 'hotel_compensate')
        <div class="verdict-detail">
          Chúng tôi đã yêu cầu khách sạn liên hệ và bồi thường trực tiếp cho bạn.
          Khách sạn sẽ liên hệ trong vòng <strong>48 giờ</strong>.
          Nếu không nhận được phản hồi, vui lòng liên hệ lại StayGo.
        </div>
      @elseif($dispute->verdict === 'rejected')
        <div class="verdict-detail">
          Sau khi xem xét toàn bộ bằng chứng và chính sách, chúng tôi không thể chấp nhận khiếu nại này.
        </div>
      @endif
    </div>

    {{-- Chi tiết phán quyết --}}
    @if($dispute->verdict_details)
    <div class="info-box">
      <h3>Chi tiết & lý do quyết định</h3>
      <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">{{ $dispute->verdict_details }}</p>
    </div>
    @endif

    {{-- Không đồng ý --}}
    @if($dispute->verdict === 'rejected')
    <div class="note">
      <strong>Không đồng ý với quyết định?</strong><br>
      Bạn có thể gửi khiếu nại lần 2 kèm bằng chứng bổ sung trong vòng <strong>7 ngày</strong>.
      Liên hệ: <a href="mailto:support@staygo.vn">support@staygo.vn</a>
    </div>
    @endif
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} StayGo — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Hotline: <a href="tel:1900xxxx">1900 xxxx</a> | Email: <a href="mailto:support@staygo.vn">support@staygo.vn</a></p>
    <p style="margin-top:8px;"><a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
  </div>

</div>
</body>
</html>
