<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Nhắc nhở check-in - StayGo</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.body{padding:30px 36px;}
.greeting{font-size:17px;font-weight:700;color:#1a202c;margin:0 0 6px;}
.intro{color:#64748b;font-size:14px;line-height:1.65;margin:0 0 22px;}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.info-row:last-child{border-bottom:none;}
.lbl{color:#718096;} .val{font-weight:600;color:#1a202c;text-align:right;}
.cta{display:inline-block;text-decoration:none;padding:13px 36px;border-radius:10px;font-size:14px;font-weight:700;}
.footer{background:#f8fafc;padding:20px 36px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:3px 0;font-size:12px;color:#94a3b8;}

/* 7day styles */
.hdr-7day{background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:32px 36px;text-align:center;}
.hdr-7day h1{margin:0 0 6px;color:#fff;font-size:22px;font-weight:800;}
.hdr-7day p{margin:0;color:rgba(255,255,255,.85);font-size:14px;}
.countdown-box{background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:2px solid #c4b5fd;border-radius:14px;padding:22px;text-align:center;margin-bottom:22px;}
.countdown-num{font-size:52px;font-weight:900;color:#7c3aed;line-height:1;}
.countdown-label{font-size:14px;color:#6d28d9;margin-top:6px;}
.upsell{background:#faf5ff;border:1px solid #e9d5ff;border-radius:12px;padding:18px 20px;margin-bottom:20px;}
.upsell p{margin:0 0 12px;font-size:13px;font-weight:700;color:#7c3aed;}
.upsell ul{margin:0;padding-left:18px;}
.upsell li{font-size:13px;color:#5b21b6;line-height:2;}

/* 1day styles */
.hdr-1day{background:linear-gradient(135deg,#0284c7,#0369a1);padding:32px 36px;text-align:center;}
.hdr-1day h1{margin:0 0 6px;color:#fff;font-size:22px;font-weight:800;}
.hdr-1day p{margin:0;color:rgba(255,255,255,.85);font-size:14px;}
.checklist{background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;padding:18px 20px;margin-bottom:20px;}
.checklist p{margin:0 0 12px;font-size:13px;font-weight:700;color:#0369a1;}
.checklist-item{display:flex;align-items:flex-start;gap:10px;padding:7px 0;border-bottom:1px solid #e0f2fe;font-size:14px;color:#1e40af;}
.checklist-item:last-child{border-bottom:none;padding-bottom:0;}
.check-icon{font-size:18px;flex-shrink:0;margin-top:1px;}
.map-btn{display:inline-block;background:#0284c7;color:#fff!important;text-decoration:none;padding:11px 28px;border-radius:10px;font-size:13px;font-weight:700;}

/* morning styles */
.hdr-morning{background:linear-gradient(135deg,#f59e0b,#d97706);padding:36px;text-align:center;}
.hdr-morning h1{margin:0 0 6px;color:#fff;font-size:24px;font-weight:800;}
.hdr-morning p{margin:0;color:rgba(255,255,255,.9);font-size:14px;}
.morning-box{background:linear-gradient(135deg,#fffbeb,#fef3c7);border:2px solid #fcd34d;border-radius:14px;padding:24px;text-align:center;margin-bottom:22px;}
.morning-hotel{font-size:20px;font-weight:800;color:#92400e;margin:0 0 8px;}
.morning-meta{font-size:14px;color:#b45309;}
.contact-btn{display:inline-block;background:#f59e0b;color:#fff!important;text-decoration:none;padding:12px 32px;border-radius:10px;font-size:14px;font-weight:700;}
</style>
</head>
<body>
<div class="wrap">

@if($wave === '7day')
{{-- ═══════════════════════ WAVE 1: 7 NGÀY TRƯỚC ═══════════════════════ --}}
  <div class="hdr-7day">
    <h1>🗓️ 7 ngày nữa bạn check-in!</h1>
    <p>StayGo nhắc bạn chuẩn bị cho chuyến đi sắp tới</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">Chỉ còn 7 ngày nữa thôi — hãy chuẩn bị để có một kỳ nghỉ thật hoàn hảo!</div>

    <div class="countdown-box">
      <div class="countdown-num">7</div>
      <div class="countdown-label">ngày nữa đến check-in 🎉</div>
    </div>

    <div class="info-row"><span class="lbl">Mã đặt phòng</span><span class="val" style="color:#7c3aed;">{{ $booking->order_code }}</span></div>
    <div class="info-row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name }}</span></div>
    <div class="info-row"><span class="lbl">Ngày nhận phòng</span><span class="val">{{ $booking->check_in?->format('d/m/Y') }} từ {{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</span></div>
    <div class="info-row"><span class="lbl">Địa chỉ</span><span class="val">{{ $booking->room?->hotel?->address }}</span></div>

    <br>
    <div class="upsell">
      <p>✨ Nâng cấp trải nghiệm của bạn:</p>
      <ul>
        <li>🍳 Bữa sáng buffet — đặt trước tiết kiệm hơn</li>
        <li>💆 Spa & massage — ưu đãi khi đặt sớm</li>
        <li>🚗 Đưa đón sân bay — tiện lợi, không lo trễ giờ</li>
      </ul>
    </div>

    <div style="text-align:center;">
      <a href="{{ config('app.url') }}/dat-phong-cua-toi" class="cta" style="background:#7c3aed;color:#fff;">Xem đặt phòng của tôi →</a>
    </div>
  </div>

@elseif($wave === '1day')
{{-- ═══════════════════════ WAVE 2: 1 NGÀY TRƯỚC ═══════════════════════ --}}
  <div class="hdr-1day">
    <h1>📋 Ngày mai bạn check-in rồi!</h1>
    <p>Checklist nhanh để không bỏ sót gì</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">Ngày mai là ngày check-in của bạn rồi! Hãy đảm bảo bạn đã chuẩn bị đầy đủ.</div>

    <div class="checklist">
      <p>✅ Checklist trước khi lên đường:</p>
      <div class="checklist-item">
        <span class="check-icon">☑</span>
        <span>CCCD / Hộ chiếu (yêu cầu bắt buộc khi đăng ký lưu trú)</span>
      </div>
      <div class="checklist-item">
        <span class="check-icon">☑</span>
        <span>Mã đặt phòng: <strong style="color:#0369a1;">{{ $booking->order_code }}</strong> (screenshot hoặc in ra)</span>
      </div>
      <div class="checklist-item">
        <span class="check-icon">☑</span>
        <span>Đến trước <strong>{{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</strong> — nếu đến muộn hãy báo khách sạn</span>
      </div>
      <div class="checklist-item">
        <span class="check-icon">☑</span>
        <span>Số điện thoại khách sạn: <strong>{{ $booking->room?->hotel?->phone ?? 'xem trong ứng dụng' }}</strong></span>
      </div>
    </div>

    <div class="info-row"><span class="lbl">Khách sạn</span><span class="val">{{ $booking->room?->hotel?->name }}</span></div>
    <div class="info-row"><span class="lbl">Địa chỉ</span><span class="val">{{ $booking->room?->hotel?->address }}</span></div>
    <div class="info-row"><span class="lbl">Nhận phòng</span><span class="val">{{ $booking->check_in?->format('d/m/Y') }} từ {{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</span></div>
    <div class="info-row"><span class="lbl">Trả phòng</span><span class="val">{{ $booking->check_out?->format('d/m/Y') }} trước {{ $booking->room?->hotel?->checkout_time ?? '12:00' }}</span></div>

    <br>
    <div style="text-align:center;">
      <a href="https://maps.google.com/?q={{ urlencode($booking->room?->hotel?->address ?? '') }}" class="map-btn">🗺️ Xem bản đồ đường đi</a>
    </div>
  </div>

@else
{{-- ═══════════════════════ WAVE 3: SÁNG NGÀY CHECK-IN ═══════════════════════ --}}
  <div class="hdr-morning">
    <h1>🌅 Chào buổi sáng!</h1>
    <p>Hôm nay là ngày check-in của bạn</p>
  </div>
  <div class="body">
    <div class="greeting">Xin chào, {{ $booking->full_name }}!</div>
    <div class="intro">Hôm nay là ngày đặc biệt! Chúc bạn có một hành trình thuận lợi và kỳ nghỉ thật tuyệt vời. ☀️</div>

    <div class="morning-box">
      <div class="morning-hotel">{{ $booking->room?->hotel?->name }}</div>
      <div class="morning-meta">
        🕐 Nhận phòng từ <strong>{{ $booking->room?->hotel?->checkin_time ?? '14:00' }}</strong><br>
        🔑 Mã booking: <strong>{{ $booking->order_code }}</strong>
      </div>
    </div>

    <div class="info-row"><span class="lbl">Địa chỉ</span><span class="val">{{ $booking->room?->hotel?->address }}</span></div>
    <div class="info-row"><span class="lbl">Điện thoại KS</span><span class="val">{{ $booking->room?->hotel?->phone ?? '—' }}</span></div>
    <div class="info-row"><span class="lbl">Loại phòng</span><span class="val">{{ $booking->room?->room_name }}</span></div>

    <br>
    <div style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#78350f;margin-bottom:20px;">
      💡 Cần gửi hành lý sớm hoặc check-in trước giờ? Hãy liên hệ thẳng với khách sạn để được hỗ trợ!
    </div>

    <div style="text-align:center;">
      <a href="tel:{{ $booking->room?->hotel?->phone }}" class="contact-btn">📞 Liên hệ khách sạn ngay</a>
    </div>
  </div>
@endif

  <div class="footer">
    <p><strong>StayGo</strong> — Chúc bạn có kỳ nghỉ tuyệt vời!</p>
    <p>© {{ date('Y') }} StayGo. Email tự động, vui lòng không reply.</p>
  </div>

</div>
</body>
</html>
