@extends('layouts.app')
@section('title', 'Liên hệ & Hỗ trợ')

@section('content')

{{-- Hero --}}
<div class="ct-hero">
    <div class="container">
        <h1 class="ct-hero-title">Liên hệ & Hỗ trợ</h1>
        <p class="ct-hero-sub">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
    </div>
</div>

<div class="ct-wrap">

    {{-- ── 4 thẻ thông tin ngang ── --}}
    <div class="ct-stats-row">
        <div class="ct-stat-card">
            <div class="ct-stat-icon ct-si-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.22 2.2 2 2 0 012.22 0h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.56-.56a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 14.92z"/></svg>
            </div>
            <div>
                <div class="ct-stat-label">Hotline</div>
                <div class="ct-stat-value ct-blue">1800 1234</div>
                <div class="ct-stat-note">Miễn phí · 8:00 – 22:00</div>
            </div>
        </div>

        <div class="ct-stat-card">
            <div class="ct-stat-icon ct-si-green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
                <div class="ct-stat-label">Email</div>
                <div class="ct-stat-value">support@staygo.vn</div>
                <div class="ct-stat-note">Phản hồi trong 2 giờ</div>
            </div>
        </div>

        <div class="ct-stat-card">
            <div class="ct-stat-icon ct-si-orange">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="ct-stat-label">Giờ làm việc</div>
                <div class="ct-stat-value">T2–T7: 8:00–21:00</div>
                <div class="ct-stat-note">CN: 9:00–18:00</div>
            </div>
        </div>

        <div class="ct-stat-card">
            <div class="ct-stat-icon ct-si-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
                <div class="ct-stat-label">Địa chỉ</div>
                <div class="ct-stat-value">146 Nguyễn Văn Cừ</div>
                <div class="ct-stat-note">TP. Kon Tum</div>
            </div>
        </div>
    </div>

    {{-- ── Form + chat 2 cột ── --}}
    <div class="ct-layout">

        {{-- Form --}}
        <div class="ct-form-box">
            <div class="ct-box-head">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Gửi yêu cầu hỗ trợ
            </div>

            @if(session('success'))
            <div class="ct-msg ct-msg-ok">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="ct-msg ct-msg-err">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('support.store') }}">
                @csrf
                <div style="display:none;" aria-hidden="true">
                    <input type="text" name="_hp_name" tabindex="-1" autocomplete="off" value="">
                    <input type="email" name="_hp_email" tabindex="-1" autocomplete="off" value="">
                </div>

                <div class="ct-grid2">
                    <div class="ct-field">
                        <label class="ct-lbl">Họ tên <span class="ct-req">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()?->full_name) }}" required class="ct-in" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="ct-field">
                        <label class="ct-lbl">Số điện thoại <span class="ct-req">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required class="ct-in" placeholder="0901 234 567">
                    </div>
                </div>
                <div class="ct-grid2">
                    <div class="ct-field">
                        <label class="ct-lbl">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="ct-in" placeholder="email@example.com">
                    </div>
                    <div class="ct-field">
                        <label class="ct-lbl">Chủ đề</label>
                        <select name="subject" class="ct-in">
                            <option value="">Chọn chủ đề...</option>
                            <option value="Hỗ trợ đặt phòng" {{ old('subject') === 'Hỗ trợ đặt phòng' ? 'selected' : '' }}>Hỗ trợ đặt phòng</option>
                            <option value="Thanh toán" {{ old('subject') === 'Thanh toán' ? 'selected' : '' }}>Thanh toán</option>
                            <option value="Hủy phòng & hoàn tiền" {{ old('subject') === 'Hủy phòng & hoàn tiền' ? 'selected' : '' }}>Hủy phòng & hoàn tiền</option>
                            <option value="Khiếu nại" {{ old('subject') === 'Khiếu nại' ? 'selected' : '' }}>Khiếu nại</option>
                            <option value="Khác" {{ old('subject') === 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="ct-field" style="margin-top:14px;">
                    <label class="ct-lbl">Nội dung</label>
                    <textarea name="note" rows="4" class="ct-in" placeholder="Mô tả chi tiết vấn đề của bạn...">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="ct-btn-submit">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Gửi yêu cầu
                </button>
            </form>
        </div>

        {{-- Chat + gợi ý --}}
        <div class="ct-side">
            <div class="ct-chat-box">
                <div class="ct-chat-emoji">💬</div>
                <div class="ct-chat-title">Chat trực tiếp</div>
                <p class="ct-chat-desc">Nhấn vào chatbot ở góc phải màn hình — chúng tôi phản hồi trong vòng 1 phút!</p>
                <div class="ct-chat-pill">⚡ Phản hồi ngay</div>
            </div>

            <div class="ct-tip-box">
                <div class="ct-tip-title">💡 Mẹo hữu ích</div>
                <ul class="ct-tip-list">
                    <li>Chuẩn bị mã đặt phòng để được hỗ trợ nhanh hơn</li>
                    <li>Chụp màn hình lỗi nếu có vấn đề kỹ thuật</li>
                    <li>Hotline hoạt động cả ngày lễ & cuối tuần</li>
                </ul>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
/* ── Hero ── */
.ct-hero {
    background: linear-gradient(135deg, #0066cc 0%, #1976d2 60%, #42a5f5 100%) !important;
    padding: 52px 0 44px !important;
    text-align: center !important;
    min-height: 140px !important;
    display: flex !important;
    align-items: center !important;
}
.ct-hero .container { width: 100%; }
.ct-hero-title {
    font-size: 34px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    margin: 0 0 8px !important;
    letter-spacing: -.3px !important;
    text-shadow: none !important;
}
.ct-hero-sub {
    font-size: 15px;
    color: rgba(255,255,255,.85) !important;
    margin: 0;
}

/* ── Wrap ── */
.ct-wrap {
    max-width: 1000px;
    margin: 0 auto;
    padding: 36px 20px 72px;
}

/* ── 4 stat cards ── */
.ct-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
    align-items: stretch;
}
.ct-stat-card {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    transition: box-shadow .18s, transform .18s;
    min-height: 90px;
}
.ct-stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); transform: translateY(-2px); }
.ct-stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ct-si-blue   { background: #eff6ff; color: #0066cc; }
.ct-si-green  { background: #f0fdf4; color: #059669; }
.ct-si-orange { background: #fff7ed; color: #ea580c; }
.ct-si-purple { background: #f5f3ff; color: #7c3aed; }
.ct-stat-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.ct-stat-value { font-size: 14px; font-weight: 700; color: #1a202c; margin-bottom: 2px; }
.ct-blue { color: #0066cc !important; }
.ct-stat-note  { font-size: 12px; color: #94a3b8; }

/* ── 2-col layout ── */
.ct-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 20px;
    align-items: start;
}

/* ── Form box ── */
.ct-form-box {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    padding: 28px 32px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.ct-box-head {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 15px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 22px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}
.ct-grid2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
.ct-field { display: flex; flex-direction: column; gap: 5px; }
.ct-lbl { font-size: 13px; font-weight: 600; color: #374151; }
.ct-req { color: #ef4444; }
input.ct-in,
select.ct-in,
textarea.ct-in {
    width: 100%;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif !important;
    color: #1a202c !important;
    background: #fafbfc !important;
    box-sizing: border-box !important;
    outline: none !important;
    transition: border-color .18s, box-shadow .18s;
    box-shadow: none !important;
    height: 42px !important;
    line-height: 1.4 !important;
    margin: 0 !important;
    vertical-align: top !important;
}
textarea.ct-in { height: auto !important; }
select.ct-in {
    -webkit-appearance: none !important;
    appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 14px center !important;
    padding-right: 36px !important;
    cursor: pointer;
}
input.ct-in:focus,
select.ct-in:focus,
textarea.ct-in:focus {
    border-color: #0066cc !important;
    box-shadow: 0 0 0 3px rgba(0,102,204,.12) !important;
    background: #fff !important;
}
textarea.ct-in { resize: vertical; min-height: 110px; }
.ct-btn-submit {
    margin-top: 20px;
    width: 100%;
    background: linear-gradient(135deg, #0066cc, #1e88e5);
    color: #fff !important;
    border: none;
    border-radius: 12px;
    padding: 13px 20px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity .18s, transform .18s;
    font-family: inherit;
}
.ct-btn-submit:hover { opacity: .9; transform: translateY(-1px); }

/* ── Messages ── */
.ct-msg { border-radius: 12px; padding: 12px 16px; font-size: 13.5px; margin-bottom: 16px; }
.ct-msg-ok { background: #f0fdf4; border: 1px solid #6ee7b7; color: #065f46; }
.ct-msg-err { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
.ct-msg-err ul { margin: 0; padding-left: 16px; }

/* ── Sidebar ── */
.ct-side { display: flex; flex-direction: column; gap: 16px; }

.ct-chat-box {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1.5px solid #bfdbfe;
    border-radius: 20px;
    padding: 24px 20px;
    text-align: center;
}
.ct-chat-emoji  { font-size: 36px; margin-bottom: 10px; }
.ct-chat-title  { font-size: 15px; font-weight: 800; color: #1a202c; margin-bottom: 8px; }
.ct-chat-desc   { font-size: 13px; color: #4b5563; line-height: 1.55; margin: 0 0 14px; }
.ct-chat-pill {
    display: inline-block;
    background: #0066cc;
    color: #fff !important;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 16px;
    border-radius: 20px;
}

.ct-tip-box {
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 20px;
    padding: 20px;
}
.ct-tip-title { font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 12px; }
.ct-tip-list  { margin: 0; padding-left: 18px; display: flex; flex-direction: column; gap: 8px; }
.ct-tip-list li { font-size: 13px; color: #78350f; line-height: 1.5; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .ct-stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .ct-layout    { grid-template-columns: 1fr; }
    .ct-grid2     { grid-template-columns: 1fr; }
    .ct-stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
    .ct-wrap      { padding: 20px 14px 56px; }
    .ct-form-box  { padding: 18px 16px; }
    .ct-stat-card { padding: 14px 12px; gap: 10px; }
    .ct-stat-icon { width: 36px; height: 36px; border-radius: 10px; }
    .ct-stat-icon svg { width: 18px; height: 18px; }
    .ct-stat-value { font-size: 12.5px; word-break: break-all; }
    .ct-stat-note  { font-size: 11px; }
    .ct-hero-title { font-size: 26px !important; }
}
@media (max-width: 420px) {
    /* Ở màn <420px: 4 card thành 1 cột cho dễ đọc */
    .ct-stats-row { grid-template-columns: 1fr; }
    .ct-stat-card {
        flex-direction: row;
        align-items: center;
        padding: 14px 16px;
    }
}
</style>
@endpush

@endsection
