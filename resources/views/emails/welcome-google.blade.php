<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Chào mừng đến StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#1B3A6B,#2D5BE3);padding:40px 36px;text-align:center;}
.hdr h1{margin:0;color:#fff;font-size:28px;font-weight:800;}
.hdr p{margin:10px 0 0;color:rgba(255,255,255,.85);font-size:15px;}
.body{padding:36px;}
.greeting{font-size:18px;font-weight:700;color:#1a202c;margin-bottom:8px;}
.sub{color:#4b5563;font-size:14px;line-height:1.7;margin-bottom:28px;}
.features{display:flex;flex-direction:column;gap:14px;margin-bottom:28px;}
.feat{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;}
.feat-icon{font-size:22px;flex-shrink:0;margin-top:1px;}
.feat-text{font-size:13.5px;color:#374151;line-height:1.5;}
.feat-title{font-weight:700;color:#1a202c;display:block;margin-bottom:2px;}
.cta{display:block;text-align:center;background:linear-gradient(135deg,#1B3A6B,#2D5BE3);color:#fff !important;text-decoration:none;padding:14px 32px;border-radius:10px;font-size:15px;font-weight:700;margin-bottom:20px;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:4px 0;font-size:12px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🏨 StayGo</h1>
    <p>Chào mừng bạn đến với StayGo!</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $user->full_name }}! 👋</div>
    <div class="sub">
      Tài khoản của bạn đã được tạo thành công qua Google. Giờ đây bạn có thể đặt phòng khách sạn &amp; resort tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng với ưu đãi tốt nhất!
    </div>

    <div class="features">
      <div class="feat">
        <div class="feat-icon">🔍</div>
        <div class="feat-text">
          <span class="feat-title">Tìm kiếm thông minh</span>
          Lọc theo giá, đánh giá, loại phòng — tìm ngay khách sạn phù hợp nhất.
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">⚡</div>
        <div class="feat-text">
          <span class="feat-title">Đặt phòng tức thì</span>
          Xác nhận ngay, thanh toán linh hoạt qua VNPay, MoMo hoặc chuyển khoản.
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">🎁</div>
        <div class="feat-text">
          <span class="feat-title">Ưu đãi độc quyền</span>
          Thành viên StayGo nhận giảm giá đến 51% mỗi ngày tại trang Ưu đãi.
        </div>
      </div>
    </div>

    <a href="{{ config('app.url') }}/hotels" class="cta">Khám phá khách sạn ngay →</a>
  </div>
  <div class="footer">
    <p><strong>StayGo</strong> — Nghỉ dưỡng thượng hạng, giá tốt nhất</p>
    <p>Hỗ trợ: support@staygo.vn | © {{ date('Y') }} StayGo</p>
  </div>
</div>
</body>
</html>
