<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Cảnh báo đăng nhập mới - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#dc2626,#b91c1c);padding:32px 36px;text-align:center;}
.hdr h1{margin:0 0 6px;color:#fff;font-size:22px;font-weight:800;}
.hdr p{margin:0;color:rgba(255,255,255,.9);font-size:14px;}
.body{padding:30px 36px;}
.greeting{font-size:17px;font-weight:700;color:#1a202c;margin:0 0 8px;}
.intro{color:#64748b;font-size:14px;line-height:1.65;margin:0 0 22px;}
.login-info{background:#fef2f2;border:1.5px solid #fca5a5;border-radius:12px;padding:20px 22px;margin-bottom:22px;}
.login-info h3{margin:0 0 14px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#94a3b8;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #fee2e2;font-size:14px;}
.row:last-child{border-bottom:none;padding-bottom:0;}
.lbl{color:#718096;} .val{font-weight:600;color:#1a202c;text-align:right;}
.actions{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;}
.btn-confirm{flex:1;display:block;text-align:center;background:#16a34a;color:#fff!important;text-decoration:none;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;min-width:140px;}
.btn-lock{flex:1;display:block;text-align:center;background:#dc2626;color:#fff!important;text-decoration:none;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;min-width:140px;}
.notice{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;line-height:1.65;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}
.footer a{color:#dc2626;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>🔔 StayGo — Cảnh báo bảo mật</h1>
    <p>Phát hiện đăng nhập từ thiết bị/địa điểm mới</p>
  </div>

  <div class="body">
    <div class="greeting">Xin chào, {{ $user->full_name ?? $user->name }}!</div>
    <div class="intro">
      Chúng tôi phát hiện một đăng nhập mới vào tài khoản của bạn. Nếu đây là bạn, không cần làm gì thêm. Nếu không phải, hãy khóa tài khoản ngay.
    </div>

    <div class="login-info">
      <h3>Chi tiết đăng nhập</h3>
      <div class="row">
        <span class="lbl">Thiết bị</span>
        <span class="val">{{ $device }}</span>
      </div>
      <div class="row">
        <span class="lbl">Trình duyệt</span>
        <span class="val">{{ $browser }}</span>
      </div>
      @if($city || $country)
      <div class="row">
        <span class="lbl">Địa điểm</span>
        <span class="val">{{ trim("$city, $country", ', ') }}</span>
      </div>
      @endif
      <div class="row">
        <span class="lbl">Địa chỉ IP</span>
        <span class="val" style="font-family:monospace;">{{ $ip }}</span>
      </div>
      <div class="row">
        <span class="lbl">Thời gian</span>
        <span class="val">{{ $loginAt }}</span>
      </div>
    </div>

    <div style="font-size:13px;font-weight:700;color:#1a202c;margin-bottom:10px;text-align:center;">Đây có phải bạn không?</div>
    <div class="actions">
      <a href="{{ config('app.url') }}/profile" class="btn-confirm">✅ Đúng, tôi đã đăng nhập</a>
      <a href="mailto:{{ config('mail.from.address') }}?subject=Khóa tài khoản khẩn cấp - {{ $user->email }}" class="btn-lock">🔒 Không phải tôi — Báo cáo ngay</a>
    </div>

    <div class="notice">
      ⚠️ Nếu đây không phải bạn:<br>
      1. Đổi mật khẩu ngay tại <strong>{{ config('app.url') }}/profile</strong><br>
      2. Liên hệ hỗ trợ: <strong>{{ config('mail.from.address') }}</strong><br>
      3. Kiểm tra lịch sử đăng nhập và thu hồi quyền truy cập thiết bị lạ
    </div>
  </div>

  <div class="footer">
    <p><strong>StayGo Security</strong> — Email tự động bảo mật tài khoản</p>
    <p>Hỗ trợ khẩn cấp: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    <p style="margin-top:6px;">© {{ date('Y') }} StayGo. Không reply email này.</p>
  </div>

</div>
</body>
</html>
