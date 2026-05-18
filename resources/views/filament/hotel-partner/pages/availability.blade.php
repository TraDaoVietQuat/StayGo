<x-filament-panels::page>
<div class="av-wrap">

    {{-- ── Toolbar: Chọn phòng + điều hướng tháng ── --}}
    <div class="av-toolbar">
        <div class="av-room-col">
            <label class="av-room-label">Chọn phòng</label>
            <select wire:model.live="selectedRoomId" class="av-room-select">
                @foreach($this->getRooms() as $room)
                    <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="av-nav">
            <button wire:click="prevMonth" class="av-nav-btn">← Tháng trước</button>
            <span class="av-month-label">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $viewMonth)->format('m/Y') }}
            </span>
            <button wire:click="nextMonth" class="av-nav-btn">Tháng sau →</button>
        </div>
    </div>

    {{-- ── Legend ── --}}
    <div class="av-legend">
        <span class="av-leg-item">
            <span class="av-dot av-dot-free"></span> Còn phòng
        </span>
        <span class="av-leg-item">
            <span class="av-dot av-dot-booked"></span> Đã có đặt phòng
        </span>
        <span class="av-leg-item">
            <span class="av-dot av-dot-blocked"></span> Đã khóa
        </span>
        <span class="av-leg-note">Click vào ngày để khóa/mở khóa (chỉ khi chưa có đặt phòng)</span>
    </div>

    {{-- ── Calendar ── --}}
    <div class="av-cal-card">

        {{-- Day-of-week headers --}}
        <div class="av-cal-header">
            @foreach(['T2','T3','T4','T5','T6','T7','CN'] as $h)
                <div class="av-head-cell {{ $h === 'CN' ? 'av-sunday' : '' }}">{{ $h }}</div>
            @endforeach
        </div>

        {{-- Date grid --}}
        @php
            $calData  = $this->getCalendarData();
            $firstDay = !empty($calData) ? \Carbon\Carbon::parse($calData[0]['date'])->dayOfWeekIso : 1;
            $offset   = $firstDay - 1;
        @endphp

        <div class="av-cal-body">

            {{-- Empty offset cells --}}
            @for($i = 0; $i < $offset; $i++)
                <div class="av-day-cell av-cell-empty"></div>
            @endfor

            {{-- Day cells --}}
            @foreach($calData as $day)
                @php
                    $isPast    = \Carbon\Carbon::parse($day['date'])->isPast() && $day['date'] !== now()->toDateString();
                    $isToday   = $day['date'] === now()->toDateString();
                    $isWeekend = in_array(\Carbon\Carbon::parse($day['date'])->dayOfWeekIso, [6,7]);
                    $canToggle = !$day['booked'] && !$isPast;

                    if ($day['blocked'])        $stateClass = 'av-cell-blocked';
                    elseif ($day['booked'])     $stateClass = 'av-cell-booked';
                    elseif ($isPast)            $stateClass = 'av-cell-past';
                    elseif ($isToday)           $stateClass = 'av-cell-today';
                    else                        $stateClass = 'av-cell-free';
                @endphp

                <div class="av-day-cell {{ $stateClass }} {{ $canToggle ? 'av-clickable' : '' }} {{ $isWeekend ? 'av-weekend' : '' }}"
                     @if($canToggle) wire:click="blockDate('{{ $day['date'] }}')" @endif>

                    <div class="av-day-top">
                        <span class="av-day-num {{ $isToday ? 'av-today-num' : '' }}">{{ $day['day'] }}</span>
                        @if($day['blocked'])
                            <span class="av-tag av-tag-blocked">Khóa</span>
                        @elseif($day['booked'])
                            <span class="av-tag av-tag-booked">Đặt</span>
                        @endif
                    </div>

                    @if($day['blocked'] && !empty($day['reason']))
                        <div class="av-reason">{{ $day['reason'] }}</div>
                    @endif
                </div>
            @endforeach

        </div>{{-- end av-cal-body --}}
    </div>{{-- end av-cal-card --}}

    <p class="av-footnote">* Ngày đã có đặt phòng không thể khóa thủ công. Liên hệ Admin để xử lý ngoại lệ.</p>

</div>

<style>
/* ══ AVAILABILITY PAGE ══════════════════════════════════════════════════ */
.av-wrap { display: flex; flex-direction: column; gap: 14px; }

/* ── Toolbar ── */
.av-toolbar {
    display: flex;
    align-items: flex-end;
    gap: 14px;
    flex-wrap: wrap;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
}
.av-room-col { flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 5px; }
.av-room-label { font-size: 11.5px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .4px; }
.av-room-select {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    color: #1e293b;
    background: #fff;
    outline: none;
    cursor: pointer;
    transition: border-color .2s;
}
.av-room-select:focus { border-color: #3b82f6; }

.av-nav { display: flex; align-items: center; gap: 10px; flex-shrink: 0; margin-left: auto; }
.av-nav-btn {
    padding: 8px 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    white-space: nowrap;
}
.av-nav-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
.av-month-label {
    min-width: 80px;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

/* ── Legend ── */
.av-legend {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 8px 4px;
}
.av-leg-item { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #4b5563; }
.av-dot {
    width: 11px; height: 11px; border-radius: 3px; flex-shrink: 0;
}
.av-dot-free    { background: #fff; border: 1.5px solid #cbd5e1; }
.av-dot-booked  { background: #3b82f6; }
.av-dot-blocked { background: #f87171; }
.av-leg-note    { font-size: 12px; color: #94a3b8; font-style: italic; margin-left: 4px; }

/* ── Calendar card ── */
.av-cal-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}

/* Day-of-week header row */
.av-cal-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
}
.av-head-cell {
    padding: 10px 0;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.av-head-cell.av-sunday { color: #ef4444; }

/* Date grid */
.av-cal-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.av-day-cell {
    min-height: 72px;
    padding: 8px 10px;
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    position: relative;
    transition: background .15s;
    box-sizing: border-box;
}
.av-day-cell:nth-child(7n) { border-right: none; }

/* State backgrounds */
.av-cell-free    { background: #fff; }
.av-cell-free.av-clickable:hover { background: #f0f7ff; }
.av-cell-booked  { background: #eff6ff; }
.av-cell-blocked { background: #fef2f2; }
.av-cell-past    { background: #f8fafc; opacity: .65; }
.av-cell-today   { background: #fffbeb; }
.av-cell-empty   { background: #fafafa; }
.av-weekend.av-cell-free { background: #fafbff; }

/* Clickable cursor */
.av-clickable { cursor: pointer; }

/* Inner layout */
.av-day-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 4px;
}
.av-day-num {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    line-height: 1;
}
.av-today-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: #f59e0b;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
    margin: -4px 0 0 -2px;
}

/* Status tags */
.av-tag {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}
.av-tag-booked  { background: #3b82f6; color: #fff; }
.av-tag-blocked { background: #f87171; color: #fff; }

/* Reason text */
.av-reason {
    margin-top: 5px;
    font-size: 10px;
    color: #dc2626;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Footnote */
.av-footnote { font-size: 11.5px; color: #94a3b8; padding: 0 2px; }

/* ── Responsive ── */
@media (max-width: 600px) {
    .av-toolbar { padding: 12px 14px; }
    .av-nav { margin-left: 0; width: 100%; justify-content: space-between; }
    .av-day-cell { min-height: 52px; padding: 5px 6px; }
    .av-day-num  { font-size: 12px; }
    .av-tag      { font-size: 9px; padding: 1px 4px; }
    .av-today-num { width: 22px; height: 22px; font-size: 11px; }
}
</style>

</x-filament-panels::page>
