<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Mật khẩu đã thay đổi - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:520px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#0284c7,#0369a1);padding:32px 36px;text-align:center;}
.hdr h1{margin:0 0 6px;color:#fff;font-size:22px;font-weight:800;}
.hdr p{margin:0;color:rgba(255,255,255,.9);font-size:14px;}
.body{padding:30px 36px;text-align:center;}
.icon-box{width:72px;height:72px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;}
.title{font-size:19px;font-weight:700;color:#1a202c;margin:0 0 8px;}
.desc{color:#64748b;font-size:14px;line-height:1.7;margin:0 0 22px;}
.changed-at{background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px 20px;display:inline-block;font-size:14px;color:#0369a1;margin-bottom:22px;}
.changed-at strong{display:block;font-size:16px;color:#0284c7;}
.alert{background:#fef2f2;border:1.5px solid #fca5a5;border-radius:12px;padding:16px 20px;text-align:left;margin-bottom:20px;}
.alert p{margin:0 0 6px;font-size:13px;font-weight:700;color:#dc2626;}
.alert ul{margin:0;padding-left:18px;}
.alert li{font-size:13px;color:#991b1b;line-height:1.9;}
.cta{display:inline-block;background:#0284c7;color:#fff!important;text-decoration:none;padding:12px 32px;border-radius:10px;font-size:14px;font-weight:700;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#0284c7;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>🔐 StayGo — Bảo mật tài khoản</h1>
    <p>Xác nhận thay đổi mật khẩu</p>
  </div>

  <div class="body">
    <div class="icon-box">✅</div>
    <div class="title">Mật khẩu đã thay đổi thành công!</div>
    <div class="desc">
      Xin chào <strong>{{ $user->full_name ?? $user->name }}</strong>, mật khẩu tài khoản <strong>{{ $user->email }}</strong> của bạn vừa được thay đổi thành công.
    </div>

    <div class="changed-at">
      <strong>{{ $changedAt }}</strong>
      Thời gian thay đổi
    </div>

    <div class="alert">
      <p>⚠️ Nếu bạn không thực hiện thay đổi này:</p>
      <ul>
        <li>Liên hệ ngay: <strong>{{ config('mail.from.address') }}</strong></li>
        <li>Gọi hotline hỗ trợ (giờ hành chính)</li>
        <li>Đội bảo mật sẽ khóa và khôi phục tài khoản cho bạn</li>
      </ul>
    </div>

    <a href="{{ config('app.url') }}/profile" class="cta">Vào trang tài khoản →</a>
  </div>

  <div class="footer">
    <p><strong>StayGo Security</strong> — Email tự động bảo mật tài khoản</p>
    <p>Hỗ trợ: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    <p style="margin-top:6px;">© {{ date('Y') }} StayGo</p>
  </div>

</div>
</body>
</html>
