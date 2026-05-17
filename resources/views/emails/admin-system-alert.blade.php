<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>System Alert — StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{padding:22px 28px;}
.hdr-critical{background:linear-gradient(135deg,#7f1d1d,#dc2626);}
.hdr-high{background:linear-gradient(135deg,#b91c1c,#ef4444);}
.hdr-medium{background:linear-gradient(135deg,#92400e,#d97706);}
.hdr-low{background:linear-gradient(135deg,#1e40af,#3b82f6);}
.hdr h1{margin:0;color:#fff;font-size:19px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:13px;}
.body{padding:24px 28px;}
/* Meta table */
.meta{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;margin-bottom:20px;}
.meta-row{display:flex;padding:6px 0;border-bottom:1px solid #edf2f7;font-size:13px;}
.meta-row:last-child{border-bottom:none;}
.meta-lbl{color:#718096;min-width:130px;flex-shrink:0;}
.meta-val{font-weight:600;color:#1a202c;}
.severity-badge{display:inline-block;border-radius:4px;padding:2px 9px;font-size:12px;font-weight:700;color:#fff;}
.badge-critical{background:#dc2626;}
.badge-high{background:#ef4444;}
.badge-medium{background:#d97706;}
.badge-low{background:#3b82f6;}
/* Sections */
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:20px 0 8px;}
.content-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 16px;font-size:13px;color:#374151;line-height:1.6;white-space:pre-wrap;word-break:break-word;}
.tech-box{background:#0f172a;border-radius:6px;padding:14px 16px;font-size:12px;color:#94a3b8;font-family:monospace;line-height:1.7;white-space:pre-wrap;word-break:break-word;overflow-x:auto;}
/* Actions */
.action-box{background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;padding:12px 16px;font-size:13px;color:#78350f;line-height:1.7;}
/* CTAs */
.btn-row{display:flex;gap:12px;margin:20px 0;}
.btn{display:inline-block;padding:11px 20px;border-radius:7px;font-weight:700;font-size:13px;text-decoration:none;text-align:center;flex:1;}
.btn-primary{background:#1B3A6B;color:#fff;}
.btn-secondary{background:#e2e8f0;color:#334155;}
.escalate{font-size:13px;color:#6b7280;text-align:center;margin-top:4px;}
.escalate a{color:#dc2626;font-weight:700;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  @php
    $hdrClass = match($severity) {
      'CRITICAL' => 'hdr-critical',
      'HIGH'     => 'hdr-high',
      'MEDIUM'   => 'hdr-medium',
      default    => 'hdr-low',
    };
    $badgeClass = 'badge-' . strtolower($severity);
    $icon = match($severity) {
      'CRITICAL' => '🚨',
      'HIGH'     => '🔴',
      'MEDIUM'   => '🟡',
      default    => '🔵',
    };
  @endphp

  <div class="hdr {{ $hdrClass }}">
    <h1>{{ $icon }} System Alert — {{ $alertType }}</h1>
    <p>Phát hiện lúc {{ $alertTime }} · Mức độ: {{ $severity }}</p>
  </div>

  <div class="body">

    <div class="meta">
      <div class="meta-row">
        <span class="meta-lbl">Loại cảnh báo</span>
        <span class="meta-val">{{ $alertType }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">Mức độ nghiêm trọng</span>
        <span class="meta-val">
          <span class="severity-badge {{ $badgeClass }}">{{ $severity }}</span>
        </span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">Thời gian phát hiện</span>
        <span class="meta-val">{{ $alertTime }}</span>
      </div>
    </div>

    <p class="section-title">Mô tả sự cố</p>
    <div class="content-box">{{ $description }}</div>

    @if($technicalDetails)
    <p class="section-title">Thông số kỹ thuật</p>
    <div class="tech-box">{{ $technicalDetails }}</div>
    @endif

    @if($recommendedActions)
    <p class="section-title">Hành động đề xuất</p>
    <div class="action-box">{{ $recommendedActions }}</div>
    @endif

    <div class="btn-row">
      <a href="{{ url('/admin') }}" class="btn btn-primary">Vào hệ thống xử lý →</a>
      <a href="{{ url('/admin/audit-logs') }}" class="btn btn-secondary">Xem log chi tiết</a>
    </div>

    @if($severity === 'CRITICAL')
    <p class="escalate">
      Cần hỗ trợ khẩn cấp? Báo cáo ngay: <a href="mailto:ops@staygo.vn">ops@staygo.vn</a>
    </p>
    @endif

  </div>

  <div class="footer">
    <p>StayGo System Monitor — Email tự động từ hệ thống giám sát · Không reply email này</p>
  </div>
</div>
</body>
</html>
