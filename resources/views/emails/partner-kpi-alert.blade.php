<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>{{ $level === 'red' ? 'Cảnh báo nghiêm trọng' : 'Cảnh báo KPI' }} — StayGo Partner</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:580px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{padding:22px 28px;}
.hdr-yellow{background:linear-gradient(135deg,#d97706,#f59e0b);}
.hdr-red{background:linear-gradient(135deg,#b91c1c,#ef4444);}
.hdr h1{margin:0;color:#fff;font-size:19px;font-weight:700;}
.hdr p{margin:6px 0 0;color:rgba(255,255,255,.88);font-size:13px;}
.body{padding:24px 28px;}
.intro{font-size:14px;color:#374151;line-height:1.6;margin:0 0 20px;}
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:0 0 12px;}
/* Issue cards */
.issue{border-radius:8px;padding:14px 16px;margin-bottom:12px;}
.issue-yellow{background:#fffbeb;border:1px solid #fcd34d;}
.issue-red{background:#fff1f2;border:1px solid #fca5a5;}
.issue-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
.issue-metric{font-weight:700;font-size:14px;color:#1a202c;}
.badge-yellow{background:#fbbf24;color:#78350f;border-radius:4px;padding:2px 8px;font-size:11px;font-weight:700;}
.badge-red{background:#ef4444;color:#fff;border-radius:4px;padding:2px 8px;font-size:11px;font-weight:700;}
.issue-vals{font-size:13px;color:#6b7280;margin-bottom:6px;}
.issue-vals strong{color:#1a202c;}
.issue-tip{font-size:12px;color:#374151;background:rgba(0,0,0,.04);border-radius:4px;padding:6px 10px;}
/* KPI summary table */
.kpi-table{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:20px;}
.kpi-table th{background:#f1f5f9;text-align:left;padding:8px 12px;font-size:11px;color:#64748b;font-weight:700;border-bottom:2px solid #e2e8f0;}
.kpi-table td{padding:8px 12px;border-bottom:1px solid #f1f5f9;color:#374151;}
.kpi-table td.ok{color:#059669;font-weight:700;}
.kpi-table td.warn{color:#d97706;font-weight:700;}
.kpi-table td.crit{color:#dc2626;font-weight:700;}
/* CTA */
.btn-dashboard{display:block;color:#fff;padding:12px 0;border-radius:7px;text-align:center;font-weight:700;font-size:14px;text-decoration:none;margin:20px 0;}
.btn-yellow{background:#d97706;}
.btn-red{background:#dc2626;}
/* Notice */
.notice{font-size:12px;color:#6b7280;text-align:center;padding:0 0 4px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr {{ $level === 'red' ? 'hdr-red' : 'hdr-yellow' }}">
    @if($level === 'red')
      <h1>🚨 Cảnh báo nghiêm trọng — Cần xử lý ngay</h1>
      <p>Một số chỉ số của {{ $hotel->name }} đang ở mức nguy hiểm và có thể ảnh hưởng đến hạng mục trên StayGo.</p>
    @else
      <h1>⚠️ Cảnh báo KPI — Cần cải thiện</h1>
      <p>Một số chỉ số của {{ $hotel->name }} đang thấp hơn ngưỡng tiêu chuẩn. Hãy hành động sớm để tránh bị đánh giá thấp.</p>
    @endif
  </div>

  <div class="body">

    <p class="intro">
      Xin chào,<br>
      Hệ thống StayGo đã phát hiện một số chỉ số KPI của <strong>{{ $hotel->name }}</strong> cần được cải thiện trong 30 ngày qua. Dưới đây là chi tiết và gợi ý xử lý.
    </p>

    {{-- KPI overview table --}}
    @if(!empty($kpis))
    <p class="section-title">Tổng quan KPI (30 ngày qua)</p>
    <table class="kpi-table">
      <thead>
        <tr><th>Chỉ số</th><th>Hiện tại</th><th>Ngưỡng tốt</th></tr>
      </thead>
      <tbody>
        @isset($kpis['cancellation_rate'])
        @php $cr = $kpis['cancellation_rate']; @endphp
        <tr>
          <td>Tỷ lệ hủy</td>
          <td class="{{ $cr > 30 ? 'crit' : ($cr > 15 ? 'warn' : 'ok') }}">{{ number_format($cr, 1) }}%</td>
          <td>≤ 15%</td>
        </tr>
        @endisset
        @isset($kpis['rating'])
        @php $rt = $kpis['rating']; @endphp
        <tr>
          <td>Điểm đánh giá</td>
          <td class="{{ $rt < 3.0 ? 'crit' : ($rt < 3.5 ? 'warn' : 'ok') }}">{{ number_format($rt, 1) }} ★</td>
          <td>≥ 3.5 ★</td>
        </tr>
        @endisset
        @isset($kpis['open_disputes'])
        @php $dp = $kpis['open_disputes']; @endphp
        <tr>
          <td>Tranh chấp mở</td>
          <td class="{{ $dp > 5 ? 'crit' : ($dp > 2 ? 'warn' : 'ok') }}">{{ $dp }} vụ</td>
          <td>≤ 2 vụ</td>
        </tr>
        @endisset
      </tbody>
    </table>
    @endif

    {{-- Issue cards --}}
    <p class="section-title">Vấn đề cụ thể & Gợi ý</p>
    @foreach($issues as $issue)
    <div class="issue {{ $level === 'red' ? 'issue-red' : 'issue-yellow' }}">
      <div class="issue-header">
        <span class="issue-metric">{{ $issue['metric'] }}</span>
        <span class="{{ $level === 'red' ? 'badge-red' : 'badge-yellow' }}">
          {{ $level === 'red' ? 'Nghiêm trọng' : 'Cảnh báo' }}
        </span>
      </div>
      <div class="issue-vals">
        Hiện tại: <strong>{{ $issue['value'] }}</strong> &nbsp;·&nbsp; Ngưỡng: {{ $issue['threshold'] }}
      </div>
      <div class="issue-tip">💡 {{ $issue['tip'] }}</div>
    </div>
    @endforeach

    {{-- CTA --}}
    <a href="{{ url('/partner/dashboard') }}" class="btn-dashboard {{ $level === 'red' ? 'btn-red' : 'btn-yellow' }}">
      Xem báo cáo chi tiết trên Partner Dashboard →
    </a>

    @if($level === 'red')
    <p class="notice">
      ⚠️ Nếu KPI không được cải thiện trong 7 ngày tới, StayGo có thể tạm ngừng hiển thị khách sạn của bạn trên nền tảng. Liên hệ <a href="mailto:partner@staygo.vn">partner@staygo.vn</a> để được hỗ trợ.
    </p>
    @endif

  </div>

  <div class="footer">
    <p>StayGo Partner — Email tự động từ hệ thống giám sát chất lượng.</p>
    <p style="margin-top:4px;">Câu hỏi? Liên hệ <a href="mailto:partner@staygo.vn" style="color:#2563eb;">partner@staygo.vn</a></p>
  </div>
</div>
</body>
</html>
