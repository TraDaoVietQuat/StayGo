<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chúc mừng! Hồ sơ đối tác đã được duyệt — StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#059669,#10b981);padding:28px 28px 20px;}
.hdr-icon{font-size:40px;line-height:1;margin-bottom:10px;}
.hdr h1{margin:0;color:#fff;font-size:22px;font-weight:800;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.88);font-size:14px;}
.body{padding:28px;}
.greeting{font-size:15px;color:#374151;line-height:1.7;margin:0 0 24px;}
/* Partnership info box */
.info-box{background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:18px 20px;margin:0 0 24px;}
.info-box .title{font-size:11px;font-weight:800;color:#16a34a;text-transform:uppercase;letter-spacing:.8px;margin:0 0 12px;}
.info-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #dcfce7;font-size:14px;}
.info-row:last-child{border-bottom:none;}
.info-lbl{color:#4b5563;}
.info-val{font-weight:700;color:#1a202c;}
/* Steps */
.steps-title{font-size:14px;font-weight:700;color:#1a202c;margin:0 0 14px;}
.step{display:flex;gap:14px;margin-bottom:16px;align-items:flex-start;}
.step-num{min-width:32px;height:32px;background:#059669;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;}
.step-body{}
.step-title{font-weight:700;font-size:14px;color:#1a202c;margin:0 0 4px;}
.step-desc{font-size:13px;color:#6b7280;margin:0;}
/* Credentials */
.creds{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px 18px;margin:20px 0;}
.creds p{margin:0 0 8px;font-size:13px;color:#92400e;}
.creds p:last-child{margin:0;}
.creds code{background:#fef3c7;border-radius:4px;padding:2px 7px;font-size:13px;font-family:monospace;color:#78350f;font-weight:700;}
/* CTA */
.btn-cta{display:block;background:#059669;color:#fff;padding:14px 0;border-radius:8px;text-align:center;font-weight:800;font-size:16px;text-decoration:none;margin:24px 0;}
.support{font-size:13px;color:#6b7280;text-align:center;}
.support a{color:#059669;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <div class="hdr-icon">🎉</div>
    <h1>Hồ sơ đã được phê duyệt!</h1>
    <p>{{ $profile->hotel?->name ?? $profile->business_name }} đã chính thức trở thành đối tác của StayGo</p>
  </div>

  <div class="body">

    <p class="greeting">
      Kính gửi <strong>{{ $profile->contact_name ?? $profile->user?->full_name }}</strong>,<br><br>
      Chúng tôi vui mừng thông báo rằng hồ sơ đăng ký của <strong>{{ $profile->hotel?->name ?? $profile->business_name }}</strong> đã được xét duyệt và <strong style="color:#059669;">CHẤP THUẬN</strong>.<br><br>
      Chào mừng bạn đến với cộng đồng đối tác StayGo! Hãy hoàn tất 3 bước dưới đây để bắt đầu nhận booking trong hôm nay.
    </p>

    {{-- Partnership info --}}
    <div class="info-box">
      <p class="title">Thông tin hợp tác</p>
      <div class="info-row">
        <span class="info-lbl">Ngày hiệu lực</span>
        <span class="info-val">{{ now()->format('d/m/Y') }}</span>
      </div>
      <div class="info-row">
        <span class="info-lbl">Mức hoa hồng</span>
        <span class="info-val">{{ number_format($profile->commission_rate ?? 15, 1) }}% / booking</span>
      </div>
      <div class="info-row">
        <span class="info-lbl">Chu kỳ thanh toán</span>
        <span class="info-val">Hàng tháng (ngày 5 hàng tháng)</span>
      </div>
      <div class="info-row">
        <span class="info-lbl">Hạng đối tác</span>
        <span class="info-val">Standard</span>
      </div>
    </div>

    {{-- Login credentials --}}
    <div class="creds">
      <p><strong>🔑 Thông tin đăng nhập Partner Dashboard:</strong></p>
      <p>Tên đăng nhập: <code>{{ $profile->user?->email }}</code></p>
      <p>Mật khẩu tạm: <code>{{ $tempPassword }}</code></p>
      <p style="margin-top:8px;font-size:12px;color:#b45309;">⚠️ Vui lòng đổi mật khẩu ngay khi đăng nhập lần đầu.</p>
    </div>

    {{-- Steps --}}
    <p class="steps-title">Hoàn tất trong 48 giờ để kích hoạt tài khoản:</p>

    <div class="step">
      <div class="step-num">1</div>
      <div class="step-body">
        <p class="step-title">Đăng nhập Partner Dashboard</p>
        <p class="step-desc">Dùng thông tin đăng nhập ở trên. Đổi mật khẩu và cập nhật thông tin hồ sơ cá nhân.</p>
      </div>
    </div>

    <div class="step">
      <div class="step-num">2</div>
      <div class="step-body">
        <p class="step-title">Cập nhật thông tin phòng và giá</p>
        <p class="step-desc">Thêm ít nhất 1 loại phòng với giá và số phòng available. Upload tối thiểu 10 ảnh chất lượng cao.</p>
      </div>
    </div>

    <div class="step">
      <div class="step-num">3</div>
      <div class="step-body">
        <p class="step-title">Xác nhận tài khoản ngân hàng nhận tiền</p>
        <p class="step-desc">Điền đầy đủ thông tin ngân hàng trong mục Tài chính để nhận thanh toán đúng kỳ.</p>
      </div>
    </div>

    <a href="{{ url('/partner') }}" class="btn-cta">Đăng nhập Partner Dashboard ngay →</a>

    <p class="support">
      Cần hỗ trợ onboarding? Liên hệ đội Partner Success:<br>
      <a href="mailto:partner@staygo.vn">partner@staygo.vn</a> &nbsp;·&nbsp; Phản hồi trong vòng 2 giờ làm việc
    </p>

  </div>

  <div class="footer">
    <p>StayGo — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
  </div>
</div>
</body>
</html>
