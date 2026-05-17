<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Thông báo tranh chấp — StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#1e40af,#1d4ed8);padding:32px 36px;text-align:center;}
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
.penalty-box{background:#fff7ed;border:2px solid #fed7aa;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.penalty-title{font-size:16px;font-weight:700;color:#9a3412;margin-bottom:8px;}
.action-box{background:#f0fdf4;border:2px solid #bbf7d0;border-radius:10px;padding:20px 24px;margin-bottom:20px;}
.action-title{font-size:15px;font-weight:700;color:#065f46;margin-bottom:10px;}
.note{background:#fef2f2;border-left:4px solid #fca5a5;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#7f1d1d;margin-bottom:20px;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#1d4ed8;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>⚖️ StayGo Partner — Thông báo tranh chấp</h1>
    <p>Kết quả xử lý khiếu nại liên quan đến cơ sở lưu trú của bạn</p>
    <div class="badge">Tranh chấp #{{ $dispute->id }}</div>
  </div>

  <div class="body">
    <div class="greeting">Kính gửi {{ $notifiable->full_name ?? $notifiable->name ?? 'Đối tác' }},</div>
    <div class="sub">
      StayGo đã hoàn tất xử lý khiếu nại liên quan đến cơ sở lưu trú của bạn.
      Vui lòng đọc kỹ thông báo dưới đây.
    </div>

    {{-- Thông tin tranh chấp --}}
    <div class="info-box">
      <h3>Thông tin tranh chấp</h3>
      <div class="row">
        <span class="lbl">Mã tranh chấp</span>
        <span class="val">#{{ $dispute->id }}</span>
      </div>
      <div class="row">
        <span class="lbl">Loại khiếu nại</span>
        <span class="val">{{ \App\Models\Dispute::typeLabels()[$dispute->type] ?? $dispute->type }}</span>
      </div>
      <div class="row">
        <span class="lbl">Tiêu đề</span>
        <span class="val">{{ $dispute->title }}</span>
      </div>
      @if($dispute->booking)
      <div class="row">
        <span class="lbl">Đặt phòng liên quan</span>
        <span class="val">{{ $dispute->booking->order_code }} — {{ $dispute->booking->full_name }}</span>
      </div>
      @endif
      <div class="row">
        <span class="lbl">Bên chịu trách nhiệm</span>
        <span class="val">{{ \App\Models\Dispute::faultLabels()[$dispute->fault_party] ?? ($dispute->fault_party ?? 'Đang xác định') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Phán quyết</span>
        <span class="val">{{ \App\Models\Dispute::verdictLabels()[$dispute->verdict] ?? $dispute->verdict }}</span>
      </div>
    </div>

    {{-- Penalty nếu có --}}
    @if($dispute->penalty_applied)
    <div class="penalty-box">
      <div class="penalty-title">⚠️ Vi phạm đã được ghi nhận vào hồ sơ đối tác</div>
      <p style="font-size:14px;color:#7c2d12;line-height:1.7;margin:0;">
        {{ $dispute->penalty_details ?: 'Vi phạm liên quan đến tranh chấp #' . $dispute->id . ' đã được ghi vào hồ sơ cơ sở lưu trú của bạn trên hệ thống StayGo.' }}
      </p>
      <p style="font-size:13px;color:#9a3412;margin:10px 0 0;">
        Lưu ý: Nếu vi phạm tái diễn, tài khoản đối tác có thể bị tạm đình chỉ hoặc chấm dứt hợp đồng.
      </p>
    </div>
    @endif

    {{-- Hành động cần thực hiện --}}
    @if($dispute->verdict === 'hotel_compensate')
    <div class="action-box">
      <div class="action-title">✅ Hành động bắt buộc từ phía khách sạn</div>
      <p style="font-size:14px;color:#065f46;line-height:1.7;margin:0;">
        Bạn cần liên hệ trực tiếp với khách hàng trong vòng <strong>48 giờ</strong>
        để thực hiện bồi thường theo yêu cầu của StayGo.
        @if($dispute->booking)
          <br><br>Email khách hàng: <strong>{{ $dispute->booking->email }}</strong><br>
          SĐT: <strong>{{ $dispute->booking->phone }}</strong>
        @endif
      </p>
    </div>
    @endif

    {{-- Chi tiết phán quyết --}}
    @if($dispute->verdict_details)
    <div class="info-box">
      <h3>Chi tiết quyết định của StayGo</h3>
      <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">{{ $dispute->verdict_details }}</p>
    </div>
    @endif

    {{-- Phản đối --}}
    <div class="note">
      <strong>Không đồng ý với quyết định?</strong><br>
      Vui lòng liên hệ <a href="mailto:partner@staygo.vn" style="color:#7f1d1d;">partner@staygo.vn</a>
      trong vòng <strong>7 ngày</strong> kèm bằng chứng bổ sung để được xem xét lại.
      Mọi khiếu nại sau 7 ngày sẽ không được tiếp nhận.
    </div>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} StayGo — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
    <p>Partner Support: <a href="mailto:partner@staygo.vn">partner@staygo.vn</a></p>
    <p style="margin-top:8px;">
      <a href="{{ config('app.url') }}/partner">Đăng nhập Partner Portal</a>
    </p>
  </div>

</div>
</body>
</html>
