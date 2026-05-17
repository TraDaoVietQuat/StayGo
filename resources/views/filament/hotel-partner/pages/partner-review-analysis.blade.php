<x-filament-panels::page>
@php
    $stats  = $this->getStats();
    $cur    = $stats['current'];
    $prev   = $stats['previous'];
    $hotel  = $stats['hotel'];

    $pct = fn($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100 : 0);
    $dir = fn($c, $p) => $c > $p ? 'up' : ($c < $p ? 'down' : 'flat');

    $periodOptions = [
        'week'    => '7 ngày gần đây',
        'month'   => 'Tháng này',
        'quarter' => '3 tháng gần đây',
    ];

    $starColors = ['#22c55e','#84cc16','#f59e0b','#f97316','#ef4444'];
@endphp

<style>
.ra-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.25rem 1.5rem; }
.ra-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#94a3b8; margin-bottom:.75rem; }
.ra-kpi { font-size:24px; font-weight:700; color:#111827; line-height:1.2; }
.ra-label { font-size:12px; color:#6b7280; margin-bottom:.2rem; font-weight:500; }
.ra-badge { display:inline-flex; align-items:center; gap:2px; font-size:11px; font-weight:600; padding:2px 8px; border-radius:999px; margin-top:.3rem; }
.ra-badge-green { background:#dcfce7; color:#15803d; }
.ra-badge-red   { background:#fee2e2; color:#dc2626; }
.ra-badge-gray  { background:#f3f4f6; color:#6b7280; }
.ra-star-bar { display:flex; align-items:center; gap:.5rem; margin-bottom:.4rem; font-size:13px; }
.ra-star-track { flex:1; background:#f3f4f6; border-radius:999px; height:8px; overflow:hidden; }
.ra-star-fill  { height:100%; border-radius:999px; transition:width .4s; }
.ra-kw-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .7rem; border-radius:999px; font-size:12px; font-weight:600; margin:.2rem; }
.ra-kw-pos { background:#dcfce7; color:#15803d; }
.ra-kw-neg { background:#fee2e2; color:#dc2626; }
.ra-urgent-row { padding:.6rem .75rem; border-bottom:1px solid #f3f4f6; font-size:13px; color:#374151; }
.ra-urgent-row:last-child { border-bottom:none; }
.ra-ai-box { background:#f8faff; border:1px solid #c7d2fe; border-radius:12px; padding:1.5rem; font-size:14px; line-height:1.8; color:#1e293b; white-space:pre-wrap; word-break:break-word; }
.ra-period-btn { padding:.4rem 1rem; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; border:1px solid #e5e7eb; background:#fff; color:#374151; transition:all .15s; }
.ra-period-btn.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }
.ra-period-btn:hover:not(.active) { background:#eef2ff; border-color:#a5b4fc; }
</style>

{{-- Period selector + AI button --}}
<div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; margin-bottom:1.5rem;">
    <span style="font-size:13px;color:#6b7280;font-weight:600;">Kỳ phân tích:</span>
    @foreach($periodOptions as $val => $label)
        <button wire:click="$set('period', '{{ $val }}')"
                class="ra-period-btn {{ $this->period === $val ? 'active' : '' }}">
            {{ $label }}
        </button>
    @endforeach
    <div style="margin-left:auto;">
        <button wire:click="generateAiReport"
                style="padding:.4rem 1.2rem; border-radius:8px; font-size:13px; font-weight:500; border:none; background:#10b981; color:#fff; cursor:pointer; display:flex; align-items:center; gap:.5rem;">
            @if($this->aiLoading)
                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"></path>
                </svg>
                Đang phân tích...
            @else
                ✨ AI Phân tích xu hướng
            @endif
        </button>
    </div>
</div>

@if(!$hotel)
    <div class="ra-card" style="text-align:center;color:#6b7280;">Chưa có thông tin khách sạn được gán.</div>
@else

{{-- ===== ROW 1: KPI tổng quan ===== --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $ratingDir = $dir($cur['avgRating'], $prev['avgRating']);
        $ratingPct = abs($pct($cur['avgRating'], $prev['avgRating']));
        $ratingColor = $ratingDir === 'up' ? 'green' : ($ratingDir === 'down' ? 'red' : 'gray');
        $ratingArrow = $ratingDir === 'up' ? '↑' : ($ratingDir === 'down' ? '↓' : '→');

        $totalDir = $dir($cur['total'], $prev['total']);
        $totalPct = abs($pct($cur['total'], $prev['total']));
        $totalArrow = $totalDir === 'up' ? '↑' : ($totalDir === 'down' ? '↓' : '→');
    @endphp

    <div class="ra-card">
        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $cur['avgRating'] >= 4.2 ? '#10b981' : ($cur['avgRating'] >= 4.0 ? '#f59e0b' : '#ef4444') }};border-radius:12px 0 0 12px;"></div>
        <div class="ra-label">Rating trung bình</div>
        <div class="ra-kpi">{{ $cur['avgRating'] }} <span style="font-size:14px;color:#9ca3af;">/ 5</span></div>
        <div class="ra-badge ra-badge-{{ $ratingColor }}">{{ $ratingArrow }} {{ $ratingPct }}% so kỳ trước</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:.3rem;">{{ $cur['avgRating'] >= 4.2 ? '✅ Đạt mục tiêu ≥4.2' : '⚠️ Mục tiêu ≥4.2' }}</div>
    </div>

    <div class="ra-card" style="position:relative;">
        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:#6366f1;border-radius:12px 0 0 12px;"></div>
        <div class="ra-label">Tổng đánh giá</div>
        <div class="ra-kpi">{{ $cur['total'] }}</div>
        <div class="ra-badge ra-badge-{{ $totalDir === 'up' ? 'green' : ($totalDir === 'down' ? 'red' : 'gray') }}">{{ $totalArrow }} {{ $totalPct }}%</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:.3rem;">Kỳ trước: {{ $prev['total'] }}</div>
    </div>

    <div class="ra-card" style="position:relative;">
        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $cur['positiveRate'] >= 80 ? '#10b981' : '#f59e0b' }};border-radius:12px 0 0 12px;"></div>
        <div class="ra-label">Tỷ lệ tích cực (4-5 sao)</div>
        <div class="ra-kpi">{{ $cur['positiveRate'] }}%</div>
        <div style="font-size:11px;color:{{ $cur['positiveRate'] >= 80 ? '#10b981' : '#f59e0b' }};margin-top:.3rem;">{{ $cur['positiveCount'] }} / {{ $cur['total'] }} đánh giá</div>
    </div>

    <div class="ra-card" style="position:relative;">
        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $cur['unresponded'] > 0 ? '#ef4444' : '#10b981' }};border-radius:12px 0 0 12px;"></div>
        <div class="ra-label">Chưa phản hồi</div>
        <div class="ra-kpi" style="color:{{ $cur['unresponded'] > 0 ? '#ef4444' : '#10b981' }};">{{ $cur['unresponded'] }}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:.3rem;">Tỷ lệ phản hồi: {{ $cur['responseRate'] }}%</div>
    </div>
</div>

{{-- ===== ROW 2: Biểu đồ sao + Keywords ===== --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem;">

    {{-- Phân bổ sao --}}
    <div class="ra-card">
        <div class="ra-section-title">⭐ Phân bổ điểm đánh giá</div>
        @php
            $starData = [
                ['label' => '5 sao', 'count' => $cur['fiveStars'],  'color' => '#22c55e'],
                ['label' => '4 sao', 'count' => $cur['fourStars'],  'color' => '#84cc16'],
                ['label' => '3 sao', 'count' => $cur['threeStars'], 'color' => '#f59e0b'],
                ['label' => '2 sao', 'count' => $cur['twoStars'],   'color' => '#f97316'],
                ['label' => '1 sao', 'count' => $cur['oneStar'],    'color' => '#ef4444'],
            ];
            $maxStar = max(array_column($starData, 'count'), 1);
        @endphp
        @foreach($starData as $s)
        <div class="ra-star-bar">
            <span style="width:42px;font-size:12px;color:#6b7280;font-weight:600;">{{ $s['label'] }}</span>
            <div class="ra-star-track">
                <div class="ra-star-fill" style="width:{{ $cur['total'] > 0 ? round($s['count'] / $maxStar * 100) : 0 }}%; background:{{ $s['color'] }};"></div>
            </div>
            <span style="width:28px;text-align:right;font-size:12px;font-weight:700;color:{{ $s['color'] }};">{{ $s['count'] }}</span>
        </div>
        @endforeach

        {{-- So sánh kỳ trước --}}
        <div style="margin-top:1rem; padding-top:.75rem; border-top:1px solid #f3f4f6; font-size:12px; color:#6b7280;">
            Kỳ trước: Rating TB {{ $prev['avgRating'] }}/5 ({{ $prev['total'] }} đánh giá)
        </div>
    </div>

    {{-- Keywords --}}
    <div class="ra-card">
        <div class="ra-section-title">🔍 Từ khóa phổ biến trong nhận xét</div>

        @if(!empty($cur['keywords']['positive']))
        <div style="margin-bottom:.75rem;">
            <div style="font-size:12px;font-weight:600;color:#15803d;margin-bottom:.4rem;">👍 Điểm mạnh khách hay nhắc</div>
            @foreach($cur['keywords']['positive'] as $kw => $count)
                <span class="ra-kw-chip ra-kw-pos">{{ $kw }} <span style="opacity:.7;">({{ $count }})</span></span>
            @endforeach
        </div>
        @endif

        @if(!empty($cur['keywords']['negative']))
        <div>
            <div style="font-size:12px;font-weight:600;color:#dc2626;margin-bottom:.4rem;">👎 Điểm cần cải thiện</div>
            @foreach($cur['keywords']['negative'] as $kw => $count)
                <span class="ra-kw-chip ra-kw-neg">{{ $kw }} <span style="opacity:.7;">({{ $count }})</span></span>
            @endforeach
        </div>
        @else
        <div style="font-size:13px;color:#9ca3af;font-style:italic;">Không phát hiện từ khóa tiêu cực — rất tốt!</div>
        @endif

        @if(empty($cur['keywords']['positive']) && empty($cur['keywords']['negative']))
        <div style="font-size:13px;color:#9ca3af;">Chưa đủ dữ liệu để phân tích từ khóa trong kỳ này.</div>
        @endif
    </div>
</div>

{{-- ===== Đánh giá thấp cần phản hồi ===== --}}
@if($cur['urgentReviews']->count() > 0)
<div class="ra-card" style="margin-bottom:1.5rem; border-color:#fca5a5;">
    <div class="ra-section-title" style="color:#dc2626;">🔴 ĐÁNH GIÁ THẤP CHƯA PHẢN HỒI — CẦN XỬ LÝ NGAY</div>
    @foreach($cur['urgentReviews'] as $review)
    <div class="ra-urgent-row">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem;">
            <div>
                <span style="font-weight:600;">{{ $review->user?->full_name ?? 'Khách vãng lai' }}</span>
                <span style="margin:0 .4rem;color:#9ca3af;">·</span>
                <span style="color:#ef4444;font-weight:700;">{{ str_repeat('⭐', (int)$review->rating) }} ({{ $review->rating }}/5)</span>
                <span style="margin:0 .4rem;color:#9ca3af;">·</span>
                <span style="color:#9ca3af;font-size:12px;">{{ $review->created_at?->format('d/m/Y') }}</span>
            </div>
            <a href="{{ route('filament.hotel-partner.resources.partner-reviews.index') }}"
               style="font-size:12px;color:#6366f1;white-space:nowrap;">
                → Phản hồi ngay
            </a>
        </div>
        <div style="margin-top:.3rem;font-size:13px;color:#4b5563;">"{{ $review->comment }}"</div>
    </div>
    @endforeach
</div>
@endif

{{-- ===== AI Report ===== --}}
@if($this->aiReport)
<div style="margin-bottom:1.5rem;">
    <div class="ra-section-title" style="color:#4f46e5;">✨ BÁO CÁO PHÂN TÍCH AI — {{ $periodOptions[$this->period] }}</div>
    <div class="ra-ai-box">{!! nl2br(e($this->aiReport)) !!}</div>
    <div style="margin-top:.5rem;text-align:right;">
        <button wire:click="generateAiReport"
                style="font-size:12px;color:#6366f1;background:none;border:none;cursor:pointer;">
            🔄 Tạo lại báo cáo
        </button>
    </div>
</div>
@endif

@endif {{-- end if hotel --}}

</x-filament-panels::page>
