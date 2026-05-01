<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Xác minh email - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#e91e8c,#c2185b);padding:32px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:24px;font-weight:700;}
.hdr p{margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;}
.body{padding:32px 36px;}
.greeting{font-size:17px;font-weight:600;color:#1a202c;margin-bottom:8px;}
.sub{color:#718096;font-size:14px;margin-bottom:28px;line-height:1.6;}
.btn-wrap{text-align:center;margin:28px 0;}
.btn{display:inline-block;background:linear-gradient(135deg,#e91e8c,#c2185b);color:#fff;text-decoration:none;
     padding:14px 36px;border-radius:10px;font-size:15px;font-weight:700;
     box-shadow:0 4px 16px rgba(233,30,140,.35);}
.note{background:#fff8f0;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;
      padding:12px 16px;font-size:13px;color:#78350f;margin-top:24px;line-height:1.5;}
.link-fallback{word-break:break-all;font-size:12px;color:#94a3b8;margin-top:16px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Xác minh địa chỉ email của bạn</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $user->full_name }}!</div>
    <div class="sub">
      Cảm ơn bạn đã đăng ký tài khoản tại StayGo.<br>
      Vui lòng nhấn nút bên dưới để xác minh email và kích hoạt tài khoản.
    </div>
    <div class="btn-wrap">
      <a href="{{ $verifyUrl }}" class="btn">✉️ Xác minh email ngay</a>
    </div>
    <div class="note">
      ⏰ Link xác minh có hiệu lực trong <strong>24 giờ</strong>.<br>
      Nếu bạn không đăng ký tài khoản này, hãy bỏ qua email này.
    </div>
    <p class="link-fallback">Hoặc copy link này vào trình duyệt:<br>{{ $verifyUrl }}</p>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Đặt phòng khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</p>
    <p>© {{ date('Y') }} StayGo. Email tự động, vui lòng không reply.</p>
  </div>
</div>
</body>
</html>
