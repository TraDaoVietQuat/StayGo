<x-filament-panels::page>
@php
    $kpis    = $this->getKpis();
    $cur     = $kpis['current'];
    $prev    = $kpis['previous'];
    $alerts  = $this->getAlerts($cur);

    $pct = fn($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100 : 0);
    $dir = fn($c, $p) => $c > $p ? 'up' : ($c < $p ? 'down' : 'flat');

    $badge = function($c, $p, bool $highGood = true) use ($pct, $dir) {
        $d   = $dir($c, $p);
        $pct = $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100 : 0);
        $color = match(true) {
            $d === 'flat'              => 'gray',
            $d === 'up' && $highGood  => 'green',
            $d === 'up' && !$highGood => 'red',
            $d === 'down' && $highGood=> 'red',
            default                   => 'green',
        };
        $arrow = $d === 'up' ? '↑' : ($d === 'down' ? '↓' : '→');
        return ['pct' => abs($pct), 'arrow' => $arrow, 'color' => $color, 'dir' => $d];
    };

    $periodOptions = [
        'this_month'  => 'Tháng này',
        'last_month'  => 'Tháng trước',
        'last_3m'     => '3 tháng gần đây',
        'last_year'   => 'Năm ngoái',
    ];
@endphp

<style>
.ba-section { margin-bottom: 2rem; }
.ba-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #94a3b8; margin-bottom: .75rem;
    display: flex; align-items: center; gap: .5rem;
}
.ba-grid { display: grid; gap: 1rem; }
.ba-grid-4 { grid-template-columns: repeat(4, 1fr); }
.ba-grid-3 { grid-template-columns: repeat(3, 1fr); }
.ba-grid-2 { grid-template-columns: repeat(2, 1fr); }
@media(max-width: 1024px) {
    .ba-grid-4 { grid-template-columns: repeat(2,1fr); }
    .ba-grid-3 { grid-template-columns: repeat(2,1fr); }
}
@media(max-width: 640px) {
    .ba-grid-4, .ba-grid-3, .ba-grid-2 { grid-template-columns: 1fr; }
}
.ba-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 1.25rem 1.5rem; position: relative; overflow: hidden;
}
.ba-card-label { font-size: 12px; color: #6b7280; margin-bottom: .25rem; font-weight: 500; }
.ba-card-value { font-size: 22px; font-weight: 700; color: #111827; line-height: 1.2; }
.ba-card-sub   { font-size: 12px; color: #9ca3af; margin-top: .2rem; }
.ba-badge {
    display: inline-flex; align-items: center; gap: 2px;
    font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 999px;
    margin-top: .4rem;
}
.ba-badge-green { background: #dcfce7; color: #15803d; }
.ba-badge-red   { background: #fee2e2; color: #dc2626; }
.ba-badge-gray  { background: #f3f4f6; color: #6b7280; }
.ba-accent-bar  {
    position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 12px 0 0 12px;
}
.ba-alert-box { border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: .625rem; font-size: 13px; }
.ba-alert-red    { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
.ba-alert-yellow { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.ba-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.ba-table th { text-align: left; color: #6b7280; font-weight: 600; font-size: 11px;
               text-transform: uppercase; padding: .5rem .75rem; border-bottom: 1px solid #e5e7eb; }
.ba-table td { padding: .625rem .75rem; border-bottom: 1px solid #f3f4f6; color: #374151; }
.ba-table tr:last-child td { border-bottom: none; }
.ba-table tr:hover td { background: #f9fafb; }
.ba-rank { font-weight: 700; color: #6366f1; width: 24px; }
.ba-ai-box {
    background: #f8faff; border: 1px solid #c7d2fe; border-radius: 12px;
    padding: 1.5rem; font-size: 14px; line-height: 1.8; color: #1e293b;
    white-space: pre-wrap; word-break: break-word;
}
.ba-ai-box h2  { font-size: 16px; font-weight: 700; color: #1e40af; margin: 1rem 0 .5rem; }
.ba-period-bar {
    display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.5rem;
    align-items: center;
}
.ba-period-btn {
    padding: .4rem 1rem; border-radius: 8px; font-size: 13px; font-weight: 500;
    cursor: pointer; border: 1px solid #e5e7eb; background: #fff; color: #374151;
    transition: all .15s;
}
.ba-period-btn.active { background: #6366f1; color: #fff; border-color: #6366f1; }
.ba-period-btn:hover:not(.active) { background: #f5f3ff; border-color: #a5b4fc; }
</style>

{{-- Period selector --}}
<div class="ba-period-bar">
    <span style="font-size:13px;color:#6b7280;font-weight:600;">Kỳ báo cáo:</span>
    @foreach($periodOptions as $val => $label)
        <button wire:click="$set('period', '{{ $val }}')"
                class="ba-period-btn {{ $this->period === $val ? 'active' : '' }}">
            {{ $label }}
        </button>
    @endforeach
    <div class="ml-auto flex gap-2">
        <button wire:click="generateAiReport"
                class="ba-period-btn active"
                style="background:#10b981;border-color:#10b981;display:flex;align-items:center;gap:.4rem;">
            @if($this->aiLoading)
                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"></path>
                </svg>
                Đang phân tích...
            @else
                ✨ AI Phân tích toàn bộ
            @endif
        </button>
    </div>
</div>

{{-- ===== ALERTS ===== --}}
@if(!empty($alerts['red']) || !empty($alerts['yellow']))
<div class="ba-section">
    <div class="ba-section-title">🚨 CẢNH BÁO</div>
    @foreach($alerts['red'] as $msg)
        <div class="ba-alert-box ba-alert-red">🔴 <strong>Cần hành động ngay:</strong> {{ $msg }}</div>
    @endforeach
    @foreach($alerts['yellow'] as $msg)
        <div class="ba-alert-box ba-alert-yellow">🟡 <strong>Cần theo dõi:</strong> {{ $msg }}</div>
    @endforeach
</div>
@endif

{{-- ===== SECTION 1: DOANH THU ===== --}}
<div class="ba-section">
    <div class="ba-section-title">💰 DOANH THU</div>
    <div class="ba-grid ba-grid-4">
        @php
            $gmvB = $badge($cur['gmv'], $prev['gmv']);
            $netB = $badge($cur['netRevenue'], $prev['netRevenue']);
        @endphp

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#6366f1;"></div>
            <div class="ba-card-label">GMV (Tổng giá trị đặt phòng)</div>
            <div class="ba-card-value">{{ number_format($cur['gmv'] / 1000000, 1) }}M đ</div>
            <div class="ba-badge ba-badge-{{ $gmvB['color'] }}">
                {{ $gmvB['arrow'] }} {{ $gmvB['pct'] }}% so kỳ trước
            </div>
            <div class="ba-card-sub">Kỳ trước: {{ number_format($prev['gmv'] / 1000000, 1) }}M đ</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#10b981;"></div>
            <div class="ba-card-label">Net Revenue</div>
            <div class="ba-card-value">{{ number_format($cur['netRevenue'] / 1000000, 1) }}M đ</div>
            <div class="ba-badge ba-badge-{{ $netB['color'] }}">
                {{ $netB['arrow'] }} {{ $netB['pct'] }}%
            </div>
            <div class="ba-card-sub">Sau hoàn tiền {{ number_format($cur['refundTotal'] / 1000000, 1) }}M đ</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:{{ $cur['takeRate'] >= 12 && $cur['takeRate'] <= 18 ? '#10b981' : '#ef4444' }};"></div>
            <div class="ba-card-label">Take Rate</div>
            <div class="ba-card-value">{{ $cur['takeRate'] }}%</div>
            <div class="ba-card-sub" style="color:{{ $cur['takeRate'] >= 12 && $cur['takeRate'] <= 18 ? '#10b981' : '#ef4444' }}">
                Mục tiêu: 12–18%
            </div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#f59e0b;"></div>
            <div class="ba-card-label">Hoa hồng đối tác</div>
            <div class="ba-card-value">{{ number_format($cur['commissionEarned'] / 1000000, 1) }}M đ</div>
            <div class="ba-card-sub">Kỳ trước: {{ number_format($prev['commissionEarned'] / 1000000, 1) }}M đ</div>
        </div>
    </div>
</div>

{{-- ===== SECTION 2: ĐẶT PHÒNG ===== --}}
<div class="ba-section">
    <div class="ba-section-title">🏨 HOẠT ĐỘNG ĐẶT PHÒNG</div>
    <div class="ba-grid ba-grid-4">
        @php
            $bkB  = $badge($cur['totalBookings'], $prev['totalBookings']);
            $cxB  = $badge($cur['cancellationRate'], $prev['cancellationRate'], false);
            $abvB = $badge($cur['abv'], $prev['abv']);
        @endphp

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#3b82f6;"></div>
            <div class="ba-card-label">Tổng đặt phòng</div>
            <div class="ba-card-value">{{ number_format($cur['totalBookings']) }}</div>
            <div class="ba-badge ba-badge-{{ $bkB['color'] }}">{{ $bkB['arrow'] }} {{ $bkB['pct'] }}%</div>
            <div class="ba-card-sub">Xác nhận: {{ number_format($cur['confirmedBookings']) }} | Hủy: {{ number_format($cur['cancelledBookings']) }}</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:{{ $cur['cancellationRate'] > 15 ? '#ef4444' : ($cur['cancellationRate'] > 10 ? '#f59e0b' : '#10b981') }};"></div>
            <div class="ba-card-label">Tỷ lệ hủy</div>
            <div class="ba-card-value">{{ $cur['cancellationRate'] }}%</div>
            <div class="ba-card-sub" style="color:{{ $cur['cancellationRate'] > 15 ? '#ef4444' : '#6b7280' }}">
                {{ $cur['cancellationRate'] > 15 ? '⚠️ Vượt ngưỡng 15%' : 'Mục tiêu: <15%' }}
            </div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#8b5cf6;"></div>
            <div class="ba-card-label">Giá trị TB / booking (ABV)</div>
            <div class="ba-card-value">{{ number_format($cur['abv'] / 1000) }}K đ</div>
            <div class="ba-badge ba-badge-{{ $abvB['color'] }}">{{ $abvB['arrow'] }} {{ $abvB['pct'] }}%</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#06b6d4;"></div>
            <div class="ba-card-label">Lead Time trung bình</div>
            <div class="ba-card-value">{{ round($cur['avgLeadTime'], 1) }} ngày</div>
            <div class="ba-card-sub">Thời gian đặt trước check-in</div>
        </div>
    </div>
</div>

{{-- ===== SECTION 3: NGƯỜI DÙNG ===== --}}
<div class="ba-section">
    <div class="ba-section-title">👤 NGƯỜI DÙNG</div>
    <div class="ba-grid ba-grid-4">
        @php
            $nuB = $badge($cur['newUsers'], $prev['newUsers']);
            $auB = $badge($cur['activeUsers'], $prev['activeUsers']);
        @endphp

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#ec4899;"></div>
            <div class="ba-card-label">Người dùng mới</div>
            <div class="ba-card-value">{{ number_format($cur['newUsers']) }}</div>
            <div class="ba-badge ba-badge-{{ $nuB['color'] }}">{{ $nuB['arrow'] }} {{ $nuB['pct'] }}%</div>
            <div class="ba-card-sub">Kỳ trước: {{ number_format($prev['newUsers']) }}</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#14b8a6;"></div>
            <div class="ba-card-label">Người dùng hoạt động</div>
            <div class="ba-card-value">{{ number_format($cur['activeUsers']) }}</div>
            <div class="ba-badge ba-badge-{{ $auB['color'] }}">{{ $auB['arrow'] }} {{ $auB['pct'] }}%</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:{{ $cur['repeatRate'] >= 40 ? '#10b981' : '#f59e0b' }};"></div>
            <div class="ba-card-label">Repeat Booking Rate</div>
            <div class="ba-card-value">{{ $cur['repeatRate'] }}%</div>
            <div class="ba-card-sub" style="color:{{ $cur['repeatRate'] >= 40 ? '#10b981' : '#f59e0b' }}">
                {{ $cur['repeatRate'] >= 40 ? '✅ Đạt mục tiêu >40%' : '🟡 Mục tiêu: >40%' }}
            </div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#a855f7;"></div>
            <div class="ba-card-label">NPS Score</div>
            <div class="ba-card-value">{{ $cur['npsScore'] }}</div>
            <div class="ba-card-sub">Rating TB: {{ $cur['avgReviewRating'] }}/5</div>
        </div>
    </div>
</div>

{{-- ===== SECTION 4: ĐỐI TÁC ===== --}}
<div class="ba-section">
    <div class="ba-section-title">🏩 ĐỐI TÁC KHÁCH SẠN</div>
    <div class="ba-grid ba-grid-4">
        @php
            $nhB = $badge($cur['newHotels'], $prev['newHotels']);
        @endphp

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#0ea5e9;"></div>
            <div class="ba-card-label">Khách sạn đang hoạt động</div>
            <div class="ba-card-value">{{ $cur['activeHotels'] }}</div>
            <div class="ba-card-sub">Mới trong kỳ: <strong>+{{ $cur['newHotels'] }}</strong></div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:{{ $cur['avgRating'] >= 4.2 ? '#10b981' : ($cur['avgRating'] >= 4.0 ? '#f59e0b' : '#ef4444') }};"></div>
            <div class="ba-card-label">Rating trung bình</div>
            <div class="ba-card-value">{{ $cur['avgRating'] }} / 5</div>
            <div class="ba-card-sub" style="color:{{ $cur['avgRating'] >= 4.2 ? '#10b981' : '#ef4444' }}">
                {{ $cur['avgRating'] >= 4.2 ? '✅ Đạt mục tiêu' : '⚠️ Mục tiêu ≥4.2' }}
            </div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:{{ $cur['hotelsAtRisk'] > 0 ? '#ef4444' : '#10b981' }};"></div>
            <div class="ba-card-label">Khách sạn cần chú ý</div>
            <div class="ba-card-value" style="color:{{ $cur['hotelsAtRisk'] > 0 ? '#ef4444' : '#10b981' }}">
                {{ $cur['hotelsAtRisk'] }}
            </div>
            <div class="ba-card-sub">Rating &lt;3.5 hoặc chưa có đánh giá</div>
        </div>

        <div class="ba-card">
            <div class="ba-accent-bar" style="background:#f97316;"></div>
            <div class="ba-card-label">Khách sạn mới onboarded</div>
            <div class="ba-card-value">{{ $cur['newHotels'] }}</div>
            <div class="ba-badge ba-badge-{{ $nhB['color'] }}">{{ $nhB['arrow'] }} {{ $nhB['pct'] }}%</div>
        </div>
    </div>
</div>

{{-- ===== SECTION 5: TOP HOTELS & DESTINATIONS ===== --}}
<div class="ba-grid ba-grid-2" style="margin-bottom:2rem;">
    <div class="ba-card" style="padding:0;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb;">
            <div class="ba-section-title" style="margin:0;">🏆 Top 5 Khách sạn doanh thu cao nhất</div>
        </div>
        <table class="ba-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Khách sạn</th>
                    <th style="text-align:right">Doanh thu</th>
                    <th style="text-align:right">Đặt phòng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cur['topHotels'] as $i => $hotel)
                <tr>
                    <td class="ba-rank">{{ $i + 1 }}</td>
                    <td>{{ $hotel->name }}
                        @if($hotel->stars) <span style="color:#f59e0b;font-size:11px;">{{ str_repeat('★', $hotel->stars) }}</span> @endif
                    </td>
                    <td style="text-align:right;font-weight:600;">{{ number_format($hotel->revenue / 1000000, 1) }}M đ</td>
                    <td style="text-align:right;color:#6366f1;font-weight:600;">{{ $hotel->bookings_count }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:1rem;">Chưa có dữ liệu trong kỳ này</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="ba-card" style="padding:0;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb;">
            <div class="ba-section-title" style="margin:0;">📍 Top 5 Điểm đến phổ biến nhất</div>
        </div>
        <table class="ba-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Điểm đến</th>
                    <th style="text-align:right">Doanh thu</th>
                    <th style="text-align:right">Đặt phòng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cur['topDestinations'] as $i => $dest)
                <tr>
                    <td class="ba-rank">{{ $i + 1 }}</td>
                    <td>{{ $dest->destination }}</td>
                    <td style="text-align:right;font-weight:600;">{{ number_format($dest->revenue / 1000000, 1) }}M đ</td>
                    <td style="text-align:right;color:#6366f1;font-weight:600;">{{ $dest->bookings_count }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:1rem;">Chưa có dữ liệu trong kỳ này</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== SECTION 6: AI REPORT ===== --}}
@if($this->aiReport)
<div class="ba-section">
    <div class="ba-section-title">✨ BÁO CÁO PHÂN TÍCH AI — {{ $periodOptions[$this->period] }}</div>
    <div class="ba-ai-box">
        {!! nl2br(e($this->aiReport)) !!}
    </div>
    <div style="margin-top:.5rem;text-align:right;">
        <button wire:click="generateAiReport" style="font-size:12px;color:#6366f1;background:none;border:none;cursor:pointer;">
            🔄 Tạo lại báo cáo
        </button>
    </div>
</div>
@endif

</x-filament-panels::page>
