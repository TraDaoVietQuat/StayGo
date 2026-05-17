<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Daily Digest — StayGo Admin</title>
<style>
body{margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:640px;margin:20px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#1e3a5f,#1B3A6B);padding:22px 28px;display:flex;justify-content:space-between;align-items:center;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.hdr .date{color:rgba(255,255,255,.75);font-size:13px;margin-top:4px;}
.hdr-badge{background:rgba(255,255,255,.15);color:#fff;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700;white-space:nowrap;}
.body{padding:24px 28px;}
/* Stat grid */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;}
.stat-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 12px;text-align:center;}
.stat-label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;}
.stat-value{font-size:20px;font-weight:800;color:#1a202c;}
.stat-change{font-size:11px;margin-top:4px;}
.up{color:#059669;} .down{color:#dc2626;} .neutral{color:#6b7280;}
/* Alerts */
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:20px 0 10px;}
.alert-urgent{background:#fef2f2;border-left:4px solid #ef4444;border-radius:0 6px 6px 0;padding:10px 14px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#7f1d1d;}
.alert-urgent a{color:#dc2626;font-weight:700;text-decoration:none;white-space:nowrap;margin-left:12px;}
.alert-watch{background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 6px 6px 0;padding:10px 14px;margin-bottom:8px;font-size:13px;color:#78350f;}
.no-alerts{font-size:13px;color:#059669;background:#f0fdf4;border-radius:6px;padding:10px 14px;}
/* Top hotels table */
table.hotels{width:100%;border-collapse:collapse;font-size:13px;margin-top:4px;}
table.hotels th{background:#f1f5f9;text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;border-bottom:2px solid #e2e8f0;}
table.hotels td{padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#374151;}
table.hotels td.rank{font-weight:800;color:#1B3A6B;width:28px;}
table.hotels td.rev{text-align:right;font-weight:700;color:#059669;}
/* CTA */
.btn-dashboard{display:block;background:#1B3A6B;color:#fff;padding:12px 0;border-radius:7px;text-align:center;font-weight:700;font-size:14px;text-decoration:none;margin:20px 0;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <div>
      <h1>📊 Daily Digest</h1>
      <div class="date">{{ now()->subDay()->format('l, d/m/Y') }} (số liệu ngày hôm qua)</div>
    </div>
    @if(count($stats['urgentAlerts'] ?? []) > 0)
      <span class="hdr-badge">🔴 {{ count($stats['urgentAlerts']) }} alert</span>
    @else
      <span class="hdr-badge" style="background:rgba(16,185,129,.25);">✅ Không có alert</span>
    @endif
  </div>

  <div class="body">

    {{-- Stats grid --}}
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">GMV hôm qua</div>
        <div class="stat-value" style="font-size:16px;">{{ number_format($stats['gmvYesterday'] ?? 0, 0, ',', '.') }}đ</div>
        @php $gc = $stats['gmvChange'] ?? 0; @endphp
        <div class="stat-change {{ $gc > 0 ? 'up' : ($gc < 0 ? 'down' : 'neutral') }}">
          {{ $gc >= 0 ? '↑' : '↓' }} {{ abs($gc) }}% so hôm trước
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Booking mới</div>
        <div class="stat-value">{{ $stats['bookingsCount'] ?? 0 }}</div>
        @php $bc = $stats['bookingsChange'] ?? 0; @endphp
        <div class="stat-change {{ $bc > 0 ? 'up' : ($bc < 0 ? 'down' : 'neutral') }}">
          {{ $bc >= 0 ? '↑' : '↓' }} {{ abs($bc) }}% so hôm trước
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Tỷ lệ hủy</div>
        @php $cr = $stats['cancelRate'] ?? 0; @endphp
        <div class="stat-value" style="color:{{ $cr > 15 ? '#dc2626' : '#1a202c' }}">{{ $cr }}%</div>
        <div class="stat-change {{ $cr > 15 ? 'down' : 'neutral' }}">
          {{ $cr > 15 ? '⚠️ Vượt ngưỡng 15%' : 'Ngưỡng: ≤ 15%' }}
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Booking chờ xử lý</div>
        <div class="stat-value">{{ $stats['pendingCount'] ?? 0 }}</div>
        <div class="stat-change neutral">Đang ở trạng thái pending</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Khiếu nại mới</div>
        <div class="stat-value">{{ $stats['complaintsNew'] ?? 0 }}</div>
        <div class="stat-change neutral">Trong ngày hôm qua</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Đối tác chờ duyệt</div>
        <div class="stat-value" style="color:{{ ($stats['pendingPartners'] ?? 0) > 0 ? '#d97706' : '#1a202c' }}">
          {{ $stats['pendingPartners'] ?? 0 }}
        </div>
        <div class="stat-change {{ ($stats['pendingPartners'] ?? 0) > 0 ? 'down' : 'neutral' }}">
          {{ ($stats['pendingPartners'] ?? 0) > 0 ? 'Cần xét duyệt' : 'Không có' }}
        </div>
      </div>
    </div>

    {{-- Urgent alerts --}}
    <p class="section-title">🔴 Cần xử lý ngay</p>
    @forelse($stats['urgentAlerts'] ?? [] as $alert)
      <div class="alert-urgent">
        <span>{{ $alert['text'] }}</span>
        <a href="{{ $alert['url'] }}">Xử lý →</a>
      </div>
    @empty
      <div class="no-alerts">✅ Không có cảnh báo khẩn cấp — hệ thống hoạt động bình thường.</div>
    @endforelse

    {{-- Watch items --}}
    @if(!empty($stats['watchItems']))
    <p class="section-title">🟡 Cần theo dõi</p>
    @foreach($stats['watchItems'] as $item)
      <div class="alert-watch">{{ $item }}</div>
    @endforeach
    @endif

    {{-- Top 5 hotels --}}
    @if(!empty($stats['topHotels']))
    <p class="section-title">🏆 Top 5 khách sạn doanh thu cao (hôm qua)</p>
    <table class="hotels">
      <thead>
        <tr><th>#</th><th>Khách sạn</th><th style="text-align:right;">Doanh thu</th></tr>
      </thead>
      <tbody>
        @foreach($stats['topHotels'] as $i => $hotel)
        <tr>
          <td class="rank">{{ $i + 1 }}</td>
          <td>{{ $hotel['name'] }}</td>
          <td class="rev">{{ number_format($hotel['revenue'], 0, ',', '.') }}đ</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    <a href="{{ url('/admin') }}" class="btn-dashboard">Mở Admin Dashboard đầy đủ →</a>

  </div>

  <div class="footer">
    <p>StayGo Admin Digest — Tự động gửi lúc 07:00 mỗi ngày · Không reply email này</p>
  </div>
</div>
</body>
</html>
