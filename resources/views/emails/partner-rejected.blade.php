<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Cập nhật hồ sơ đăng ký đối tác — StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:linear-gradient(135deg,#4b5563,#6b7280);padding:24px 28px;}
.hdr h1{margin:0;color:#fff;font-size:20px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:13px;}
.body{padding:28px;}
.greeting{font-size:14px;color:#374151;line-height:1.7;margin:0 0 20px;}
/* Reason box */
.reason-box{background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:18px 20px;margin:0 0 24px;}
.reason-box .title{font-size:11px;font-weight:800;color:#dc2626;text-transform:uppercase;letter-spacing:.8px;margin:0 0 12px;}
.reason-list{margin:0;padding:0 0 0 18px;color:#7f1d1d;font-size:14px;line-height:1.8;}
.reason-list li{margin-bottom:6px;}
/* Next steps */
.next-box{background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px 20px;margin:0 0 24px;}
.next-box .title{font-size:11px;font-weight:800;color:#0369a1;text-transform:uppercase;letter-spacing:.8px;margin:0 0 10px;}
.next-box p{margin:0;font-size:13px;color:#374151;line-height:1.6;}
/* CTA */
.btn-cta{display:block;background:#1B3A6B;color:#fff;padding:13px 0;border-radius:7px;text-align:center;font-weight:700;font-size:14px;text-decoration:none;margin:20px 0;}
.contact{font-size:13px;color:#6b7280;text-align:center;}
.contact a{color:#2563eb;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <h1>Cập nhật hồ sơ đăng ký đối tác</h1>
    <p>{{ $profile->hotel?->name ?? $profile->business_name }} — Kết quả xét duyệt</p>
  </div>

  <div class="body">

    <p class="greeting">
      Kính gửi <strong>{{ $profile->contact_name ?? $profile->user?->full_name }}</strong>,<br><br>
      Cảm ơn quý vị đã đăng ký trở thành đối tác của StayGo.<br><br>
      Sau khi xem xét kỹ hồ sơ, chúng tôi rất tiếc khi chưa thể phê duyệt hồ sơ trong lần này. Dưới đây là những lý do cụ thể và hướng dẫn để quý vị bổ sung, hoàn thiện hồ sơ.
    </p>

    {{-- Rejection reasons --}}
    <div class="reason-box">
      <p class="title">Lý do chưa phê duyệt</p>
      @php
        $reasons = array_filter(array_map('trim', explode("\n", $rejectionReason)));
      @endphp
      @if(count($reasons) > 1)
        <ul class="reason-list">
          @foreach($reasons as $reason)
            @if($reason)
            <li>{{ $reason }}</li>
            @endif
          @endforeach
        </ul>
      @else
        <p style="margin:0;font-size:14px;color:#7f1d1d;">{{ $rejectionReason }}</p>
      @endif
    </div>

    {{-- Next steps --}}
    <div class="next-box">
      <p class="title">Bước tiếp theo</p>
      <p>
        Quý vị có thể <strong>nộp lại hồ sơ</strong> sau khi đã bổ sung đầy đủ những mục còn thiếu ở trên.
        Hồ sơ bổ sung sẽ được xem xét trong <strong>3–5 ngày làm việc</strong>.<br><br>
        Nếu có câu hỏi về yêu cầu cụ thể, đội ngũ Partner Support của chúng tôi sẵn sàng hỗ trợ.
      </p>
    </div>

    <a href="{{ url('/partner/dang-ky') }}" class="btn-cta">Cập nhật và nộp lại hồ sơ →</a>

    <p class="contact">
      Câu hỏi? Liên hệ: <a href="mailto:partner@staygo.vn">partner@staygo.vn</a><br>
      <span style="font-size:12px;color:#9ca3af;">Thời gian phản hồi: 1–2 ngày làm việc</span>
    </p>

  </div>

  <div class="footer">
    <p>StayGo — Nền tảng đặt phòng khách sạn & resort Việt Nam</p>
  </div>
</div>
</body>
</html>
