<x-filament-panels::page>

<style>
/* ── Layout ── */
.mr-toolbar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.mr-select   { padding:.4rem .8rem; border-radius:8px; border:1px solid #d1d5db; font-size:13px; color:#374151; background:#fff; }
.mr-btn      { padding:.45rem 1.25rem; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; transition:all .15s; }
.mr-btn-primary { background:#1d4ed8; color:#fff; }
.mr-btn-primary:hover { background:#1e40af; }
.mr-btn-print  { background:#f3f4f6; color:#374151; border:1px solid #d1d5db; }
.mr-btn-print:hover { background:#e5e7eb; }
.mr-btn:disabled { opacity:.5; cursor:not-allowed; }

/* ── Report container ── */
#report-body { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:2rem 2.5rem; font-family:'Inter',sans-serif; max-width:900px; margin:0 auto; }

/* ── Typography ── */
.rpt-header   { text-align:center; border-bottom:2px solid #1d4ed8; padding-bottom:1rem; margin-bottom:1.5rem; }
.rpt-hotel    { font-size:20px; font-weight:800; color:#1e3a8a; }
.rpt-meta     { font-size:12px; color:#6b7280; margin-top:.3rem; }
.rpt-section  { margin-bottom:1.75rem; }
.rpt-h        { font-size:13px; font-weight:700; color:#1d4ed8; text-transform:uppercase; letter-spacing:.08em;
                border-left:3px solid #1d4ed8; padding-left:.6rem; margin-bottom:.75rem; }
.rpt-summary  { background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:.9rem 1.1rem; font-size:13.5px; line-height:1.75; color:#1e3a8a; }

/* ── KPI table ── */
.rpt-table { width:100%; border-collapse:collapse; font-size:13px; }
.rpt-table th { background:#1d4ed8; color:#fff; padding:.5rem .75rem; text-align:center; font-weight:600; }
.rpt-table th:first-child { text-align:left; }
.rpt-table td { padding:.5rem .75rem; border-bottom:1px solid #f3f4f6; color:#374151; text-align:center; }
.rpt-table td:first-child { text-align:left; font-weight:500; color:#1e3a8a; }
.rpt-table tr:last-child td { border-bottom:none; font-weight:700; }
.rpt-table tr:hover td { background:#f8faff; }
.rpt-up   { color:#15803d; font-weight:700; }
.rpt-down { color:#dc2626; font-weight:700; }
.rpt-flat { color:#6b7280; }

/* ── Lists ── */
.rpt-list { list-style:none; padding:0; margin:0; }
.rpt-list li { padding:.4rem 0; border-bottom:1px solid #f3f4f6; font-size:13.5px; color:#374151; display:flex; align-items:flex-start; gap:.5rem; }
.rpt-list li:last-child { border-bottom:none; }
.rpt-improve-issue { font-weight:600; color:#b45309; }
.rpt-improve-sug   { color:#374151; font-size:13px; }
.rpt-action-num    { font-weight:700; color:#1d4ed8; min-width:20px; }
.rpt-deadline      { font-size:11px; background:#eff6ff; color:#1d4ed8; border-radius:999px; padding:1px 7px; margin-left:.5rem; white-space:nowrap; }
.rpt-event-dot     { color:#7c3aed; font-size:16px; }

/* ── Divider ── */
.rpt-divider { border:none; border-top:1px solid #e5e7eb; margin:1.5rem 0; }

/* ── Raw AI fallback ── */
.rpt-raw { white-space:pre-wrap; font-size:13px; line-height:1.75; color:#374151; background:#f9fafb; border-radius:8px; padding:1rem; }

/* ── Print ── */
@media print {
    .mr-toolbar, .fi-sidebar, .fi-topbar, .fi-header, [data-tippy-content] { display:none !important; }
    #report-body { border:none; padding:0; max-width:100%; box-shadow:none; }
    .rpt-table th { background:#1d4ed8 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    body { background:#fff !important; }
}
</style>

{{-- ══ Toolbar ══ --}}
<div class="mr-toolbar">
    <select wire:model="reportMonth" class="mr-select">
        @foreach($this->getMonthOptions() as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>
    <select wire:model="reportYear" class="mr-select">
        @foreach($this->getYearOptions() as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>

    <button wire:click="generateReport"
            wire:loading.attr="disabled"
            class="mr-btn mr-btn-primary"
            @if($this->generating) disabled @endif>
        <span wire:loading.remove wire:target="generateReport">
            ✨ Tạo báo cáo AI
        </span>
        <span wire:loading wire:target="generateReport">
            ⏳ Đang tạo báo cáo...
        </span>
    </button>

    @if($this->reportReady)
    <button onclick="window.print()" class="mr-btn mr-btn-print">🖨 In báo cáo</button>
    @endif
</div>

@if($this->generating && !$this->reportReady)
<div style="text-align:center;padding:3rem;color:#6b7280;font-size:14px;">
    <div style="font-size:24px;margin-bottom:.75rem;">⏳</div>
    <div style="font-weight:600;">Đang thu thập dữ liệu và phân tích AI...</div>
    <div style="font-size:13px;margin-top:.3rem;">Quá trình này mất khoảng 10-30 giây</div>
</div>
@endif

@if($this->reportReady)
<div id="report-body">

    {{-- ══ HEADER ══ --}}
    <div class="rpt-header">
        <div style="font-size:12px;font-weight:700;color:#9ca3af;letter-spacing:.15em;margin-bottom:.5rem;">
            ━━━ BÁO CÁO HIỆU SUẤT THÁNG {{ $this->getReportTitle() }} ━━━
        </div>
        <div class="rpt-hotel">{{ $this->hotelName }}</div>
        <div class="rpt-meta">
            Cập nhật: {{ now()->format('d/m/Y H:i') }}
            &nbsp;|&nbsp; Hoa hồng OTA: {{ $this->commissionRate }}%
        </div>
    </div>

    {{-- ══ EXECUTIVE SUMMARY ══ --}}
    @if(!empty($this->aiSections['executive_summary']))
    <div class="rpt-section">
        <div class="rpt-h">▌ TÓM TẮT THÁNG (Executive Summary)</div>
        <div class="rpt-summary">{{ $this->aiSections['executive_summary'] }}</div>
    </div>
    @endif

    {{-- ══ KPI TABLE ══ --}}
    <div class="rpt-section">
        <div class="rpt-h">▌ CHỈ SỐ KINH DOANH CHÍNH</div>
        @php
            $c = $this->kpis;
            $p = $this->prevKpis;
            $diff = function($cur, $prev, bool $highGood = true) {
                if ($prev == 0) return ['label' => '—', 'cls' => 'rpt-flat'];
                $pct = round(($cur - $prev) / $prev * 100, 1);
                $up  = $cur > $prev;
                $cls = $up === $highGood ? 'rpt-up' : 'rpt-down';
                $arrow = $up ? '↑' : '↓';
                return ['label' => "$arrow " . abs($pct) . "%", 'cls' => $cls];
            };

            $rows = [
                ['Tổng booking',          $c['totalBookings'],                    $p['totalBookings'],   $diff($c['totalBookings'],    $p['totalBookings'])],
                ['Phòng-đêm bán',         $c['roomNightsSold'],                   $p['roomNightsSold'],  $diff($c['roomNightsSold'],    $p['roomNightsSold'])],
                ['Occupancy Rate',        $c['occupancyRate'] . '%',              $p['occupancyRate'].'%', $diff($c['occupancyRate'],  $p['occupancyRate'])],
                ['ADR (Giá TB/đêm)',      number_format($c['adr']) . ' ₫',       number_format($p['adr']) . ' ₫', $diff($c['adr'], $p['adr'])],
                ['RevPAR',                number_format($c['revpar']) . ' ₫',     number_format($p['revpar']) . ' ₫', $diff($c['revpar'], $p['revpar'])],
                ['Doanh thu gross',       number_format($c['grossRevenue']) . ' ₫', number_format($p['grossRevenue']) . ' ₫', $diff($c['grossRevenue'], $p['grossRevenue'])],
                ['Hoa hồng OTA (' . $this->commissionRate . '%)', number_format($c['otaCommission']) . ' ₫', number_format($p['otaCommission']) . ' ₫', ['label' => '—', 'cls' => 'rpt-flat']],
                ['Doanh thu thực nhận',   number_format($c['netRevenue']) . ' ₫', number_format($p['netRevenue']) . ' ₫', $diff($c['netRevenue'], $p['netRevenue'])],
                ['Tỷ lệ hủy phòng',      $c['cancellationRate'] . '%',           $p['cancellationRate'].'%', $diff($c['cancellationRate'], $p['cancellationRate'], false)],
                ['Điểm đánh giá TB',     $c['avgRating'] > 0 ? $c['avgRating'].'/5' : '—', $p['avgRating'] > 0 ? $p['avgRating'].'/5' : '—', $diff($c['avgRating'], $p['avgRating'])],
                ['Lead time đặt trước',   $c['avgLeadTime'] . ' ngày',           $p['avgLeadTime'] . ' ngày', ['label' => '—', 'cls' => 'rpt-flat']],
            ];
        @endphp
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>Chỉ số</th>
                    <th>Tháng {{ sprintf('%02d/%d', $this->reportMonth, $this->reportYear) }}</th>
                    <th>Tháng trước</th>
                    <th>Thay đổi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td style="color:#9ca3af;">{{ $row[2] }}</td>
                    <td class="{{ $row[3]['cls'] }}">{{ $row[3]['label'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr class="rpt-divider">

    {{-- ══ ROOM BREAKDOWN ══ --}}
    @if(!empty($this->roomRows))
    <div class="rpt-section">
        <div class="rpt-h">▌ PHÂN TÍCH THEO LOẠI PHÒNG</div>
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>Loại phòng</th>
                    <th>Đêm bán</th>
                    <th>Occupancy</th>
                    <th>Giá TB/đêm</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->roomRows as $room)
                <tr>
                    <td>{{ $room['name'] }}</td>
                    <td>{{ $room['nights_sold'] }}</td>
                    <td>
                        <span style="color:{{ $room['occupancy'] >= 70 ? '#15803d' : ($room['occupancy'] >= 50 ? '#b45309' : '#dc2626') }}; font-weight:600;">
                            {{ $room['occupancy'] }}%
                        </span>
                    </td>
                    <td>{{ number_format($room['avg_price']) }} ₫</td>
                    <td style="font-weight:700;">{{ number_format($room['revenue']) }} ₫</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ══ GUEST SOURCE ══ --}}
    <div class="rpt-section">
        <div class="rpt-h">▌ NGUỒN KHÁCH</div>
        <div style="font-size:13.5px;line-height:2;color:#374151;">
            • Khách trong nước: <strong>{{ $this->guestSource['domesticPct'] }}%</strong>
              &nbsp;|&nbsp; Khách quốc tế (ước tính): <strong>{{ $this->guestSource['internationalPct'] }}%</strong>
              <span style="font-size:11px;color:#9ca3af;">(phân loại theo đầu số điện thoại)</span>
            <br>
            • Khách đặt không có tài khoản: <strong>{{ $this->guestSource['guestCount'] }}</strong> đơn
            <br>
            • Booking lead time trung bình: <strong>{{ $this->kpis['avgLeadTime'] }} ngày</strong>
        </div>
    </div>

    {{-- ══ REVIEW & REPUTATION ══ --}}
    <div class="rpt-section">
        <div class="rpt-h">▌ ĐÁNH GIÁ & UY TÍN</div>
        @php $rv = $this->reviewStats; @endphp
        <div style="font-size:13.5px;line-height:2;color:#374151;">
            • Tổng đánh giá nhận: <strong>{{ $rv['total'] }}</strong>
              &nbsp;|&nbsp; Điểm TB: <strong style="color:{{ $rv['avgRating'] >= 4.2 ? '#15803d' : ($rv['avgRating'] >= 4.0 ? '#b45309' : '#dc2626') }};">{{ $rv['avgRating'] > 0 ? $rv['avgRating'].'/5' : 'Chưa có' }}</strong>
            <br>
            • Đánh giá 5 sao: <strong style="color:#15803d;">{{ $rv['fivePct'] }}%</strong>
              &nbsp;|&nbsp; Đánh giá 1-2 sao: <strong style="color:{{ $rv['lowPct'] > 10 ? '#dc2626' : '#374151' }};">{{ $rv['lowPct'] }}%</strong>
              &nbsp;|&nbsp; Chưa phản hồi: <strong style="color:{{ $rv['unresponded'] > 0 ? '#dc2626' : '#15803d' }};">{{ $rv['unresponded'] }}</strong>
        </div>
        @if($rv['bestComment'])
        <div style="margin-top:.75rem;padding:.75rem 1rem;background:#f0fdf4;border-left:3px solid #22c55e;border-radius:0 8px 8px 0;font-size:13px;color:#166534;font-style:italic;">
            ⭐⭐⭐⭐⭐ "{{ Str::limit($rv['bestComment'], 200) }}"
        </div>
        @endif
        @if($rv['worstComment'])
        <div style="margin-top:.5rem;padding:.75rem 1rem;background:#fef2f2;border-left:3px solid #ef4444;border-radius:0 8px 8px 0;font-size:13px;color:#991b1b;font-style:italic;">
            ⭐ "{{ Str::limit($rv['worstComment'], 200) }}"
        </div>
        @endif
    </div>

    <hr class="rpt-divider">

    {{-- ══ AI SECTIONS ══ --}}
    @if(!empty($this->aiSections))

        {{-- Strengths --}}
        @if(!empty($this->aiSections['strengths']))
        <div class="rpt-section">
            <div class="rpt-h">▌ ĐIỂM MẠNH THÁNG NÀY ✓</div>
            <ul class="rpt-list">
                @foreach($this->aiSections['strengths'] as $s)
                <li><span style="color:#22c55e;font-size:18px;">•</span> {{ $s }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Improvements --}}
        @if(!empty($this->aiSections['improvements']))
        <div class="rpt-section">
            <div class="rpt-h">▌ ĐIỂM CẦN CẢI THIỆN ⚠</div>
            <ul class="rpt-list">
                @foreach($this->aiSections['improvements'] as $item)
                <li>
                    <span style="color:#f59e0b;font-size:18px;">•</span>
                    <div>
                        <div class="rpt-improve-issue">{{ $item['issue'] ?? '' }}</div>
                        @if(!empty($item['suggestion']))
                        <div class="rpt-improve-sug">→ {{ $item['suggestion'] }}</div>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Action Plan --}}
        @if(!empty($this->aiSections['action_plan']))
        <div class="rpt-section">
            <div class="rpt-h">▌ KẾ HOẠCH THÁNG TỚI</div>
            <ul class="rpt-list" style="counter-reset:action;">
                @foreach($this->aiSections['action_plan'] as $i => $item)
                <li>
                    <span class="rpt-action-num">{{ $i + 1 }}.</span>
                    <div>
                        {{ $item['action'] ?? '' }}
                        @if(!empty($item['deadline']))
                        <span class="rpt-deadline">Deadline: {{ $item['deadline'] }}</span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Next Month Events --}}
        @if(!empty($this->aiSections['next_month_events']))
        <div class="rpt-section">
            <div class="rpt-h">▌ SỰ KIỆN & DỊP ĐẶC BIỆT THÁNG TỚI</div>
            <ul class="rpt-list">
                @foreach($this->aiSections['next_month_events'] as $ev)
                <li><span class="rpt-event-dot">◆</span> {{ $ev }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    @elseif($this->aiRaw)
        {{-- Fallback: show raw AI text --}}
        <div class="rpt-section">
            <div class="rpt-h">▌ PHÂN TÍCH AI</div>
            <div class="rpt-raw">{{ $this->aiRaw }}</div>
        </div>
    @endif

    {{-- ══ FOOTER ══ --}}
    <div style="margin-top:2rem;padding-top:1rem;border-top:2px solid #e5e7eb;text-align:center;font-size:11px;color:#9ca3af;">
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        <br>Báo cáo được tạo tự động bởi StayGo Partner AI &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i') }}
        <br>Dữ liệu từ hệ thống StayGo — Chỉ dành cho nội bộ
    </div>

</div>{{-- /#report-body --}}
@endif

</x-filament-panels::page>
