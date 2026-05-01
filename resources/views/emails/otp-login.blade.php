<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family:'Be Vietnam Pro',Arial,sans-serif;background:#f4f6f9;margin:0;padding:20px;">
<div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:36px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
    <h2 style="color:#e91e8c;margin-top:0;">Xin chào {{ $user->full_name }},</h2>
    <p style="color:#444;line-height:1.6;">Bạn đã yêu cầu đăng nhập bằng OTP vào tài khoản <strong>StayGo</strong>. Dưới đây là mã OTP của bạn:</p>
    <div style="text-align:center;margin:28px 0;">
        <div style="font-size:40px;font-weight:700;letter-spacing:12px;color:#e91e8c;background:#fff0f8;border-radius:10px;padding:18px;">{{ $otp }}</div>
    </div>
    <p style="color:#666;font-size:14px;">Mã này có hiệu lực trong <strong>10 phút</strong>. Không chia sẻ mã này cho bất kỳ ai.</p>
    <p style="color:#999;font-size:13px;">Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email và kiểm tra lại tài khoản.</p>
    <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
    <p style="color:#bbb;font-size:12px;text-align:center;">© 2026 StayGo - Hệ thống đặt phòng khách sạn</p>
</div>
</body>
</html>
