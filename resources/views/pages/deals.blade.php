@extends('layouts.app')
@section('title', 'Ưu đãi hôm nay')

@section('content')

{{-- ======================================================
     HERO BANNER
====================================================== --}}
<div class="deals-hero">
    <div class="deals-hero-bg"></div>
    <div class="deals-hero-content container">
        <div class="deals-hero-text">
            <div class="deals-hero-label">🔥 Ưu đãi có hạn</div>
            <h1 class="deals-hero-title">Ưu đãi cho hôm nay</h1>
            <p class="deals-hero-sub">Khuyến mãi đặc biệt. Không có ở nơi khác.<br>Hãy lưu trang này để nhận ưu đãi hằng ngày.</p>
            <a href="{{ route('hotels.index') }}" class="deals-hero-btn">Tìm khách sạn ngay →</a>
        </div>
        <div class="deals-hero-chars">
            <div class="deals-char deals-char-1">
                <div class="deals-char-circle">🏨</div>
            </div>
            <div class="deals-char deals-char-2">
                <div class="deals-char-circle">🌴</div>
            </div>
            <div class="deals-char deals-char-3">
                <div class="deals-char-circle">✈️</div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     BỘ LỌC ĐỊA ĐIỂM
====================================================== --}}
<div class="deals-filter-bar">
    <div class="container">
        <div class="deals-filter-inner">
            <span class="deals-filter-label">Lọc theo địa điểm:</span>
            <div class="deals-filter-tabs">
                <a href="{{ route('deals.index') }}"
                   class="deals-ftab {{ !request('location') ? 'active' : '' }}">Tất cả</a>
                @foreach($locations as $loc)
                <a href="{{ route('deals.index', ['location' => $loc->id]) }}"
                   class="deals-ftab {{ request('location') == $loc->id ? 'active' : '' }}">
                    {{ $loc->name }}
                    <span class="deals-ftab-count">{{ $loc->hotels_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     DEAL CARDS — CÁC LOẠI ƯU ĐÃI
====================================================== --}}
<div class="container deals-cards-section">
    <div class="deals-cards-grid">

        {{-- Card 1: Ưu đãi cuối tuần --}}
        <div class="deals-card deals-card-weekend">
            <div class="deals-card-badge">
                <span class="dcb-up">Giảm giá đến</span>
                <span class="dcb-pct">20<small>%</small></span>
            </div>
            <div class="deals-card-body">
                <div class="deals-card-icon">🌅</div>
                <div class="deals-card-title">Ưu đãi Cuối Tuần</div>
                <div class="deals-card-desc">Giảm đến 20% cho mọi đặt phòng. Tối đa 500.000đ. Đơn tối thiểu 300.000đ.</div>
                <div class="deals-code-box">
                    <span class="deals-code-text" id="code-weekend">WEEKEND20</span>
                    <button class="deals-code-copy" onclick="copyCode('code-weekend', this)" title="Sao chép mã">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <a href="{{ route('hotels.index', ['weekend' => 1]) }}" class="deals-card-btn">XEM KHÁCH SẠN ÁP DỤNG</a>
            </div>
        </div>

        {{-- Card 2: Khách mới 10% --}}
        <div class="deals-card deals-card-newuser">
            <div class="deals-card-badge deals-card-badge-green">
                <span class="dcb-up">Giảm giá đến</span>
                <span class="dcb-pct">10<small>%</small></span>
            </div>
            <div class="deals-card-body">
                <div class="deals-card-icon">🎉</div>
                <div class="deals-card-title">Khách Hàng Mới</div>
                <div class="deals-card-desc">Giảm 10% đơn đặt phòng đầu tiên. Chỉ áp dụng 1 lần cho tài khoản mới.</div>
                <div class="deals-code-box">
                    <span class="deals-code-text" id="code-newuser">NEWUSER10</span>
                    <button class="deals-code-copy" onclick="copyCode('code-newuser', this)" title="Sao chép mã">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                @auth
                    @if(\App\Models\Booking::where('user_id', Auth::id())->whereNotIn('status',['cancelled'])->count() === 0)
                    <a href="{{ route('promo.new_user') }}" class="deals-card-btn deals-card-btn-green">NHẬN ƯU ĐÃI NGAY</a>
                    @else
                    <div class="deals-code-used">Bạn đã sử dụng ưu đãi này rồi</div>
                    @endif
                @else
                <a href="{{ route('login') }}" class="deals-card-btn deals-card-btn-green">ĐĂNG NHẬP ĐỂ NHẬN</a>
                @endauth
            </div>
        </div>

        {{-- Card 3: Đặt phòng sớm --}}
        <div class="deals-card deals-card-early">
            <div class="deals-card-badge deals-card-badge-orange">
                <span class="dcb-up">Giảm giá đến</span>
                <span class="dcb-pct">15<small>%</small></span>
            </div>
            <div class="deals-card-body">
                <div class="deals-card-icon">⚡</div>
                <div class="deals-card-title">Đặt Phòng Sớm</div>
                <div class="deals-card-desc">Giảm 15% khi đặt trước. Tối đa 300.000đ. Đơn tối thiểu 500.000đ.</div>
                <div class="deals-code-box deals-code-box--orange">
                    <span class="deals-code-text" id="code-early">EARLY15</span>
                    <button class="deals-code-copy deals-code-copy--orange" onclick="copyCode('code-early', this)" title="Sao chép mã">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <a href="{{ route('hotels.index') }}" class="deals-card-btn deals-card-btn-orange">XEM KHÁCH SẠN</a>
            </div>
        </div>

    </div>
</div>

{{-- ======================================================
     CÁCH NHẬN ƯU ĐÃI — 3 BƯỚC
====================================================== --}}
<div class="deals-how-section">
    <div class="container">
        <h2 class="deals-how-title">Cách Áp Dụng Ưu Đãi</h2>
        <p class="deals-how-sub">3 bước đơn giản để nhận ngay khuyến mãi tốt nhất</p>
        <div class="deals-how-steps">

            <div class="deals-how-step">
                <div class="deals-how-num">1</div>
                <div class="deals-how-img">
                    <div class="deals-how-icon-wrap deals-how-icon-1">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M9 7h6M9 11h6M9 15h4"/></svg>
                    </div>
                </div>
                <div class="deals-how-step-title">Tìm & Thu thập Phiếu</div>
                <div class="deals-how-step-desc">Tìm và thu thập mã giảm giá bạn muốn. Nhấn nút "Nhận ngay" để sao chép mã.</div>
            </div>

            <div class="deals-how-arrow">→</div>

            <div class="deals-how-step">
                <div class="deals-how-num">2</div>
                <div class="deals-how-img">
                    <div class="deals-how-icon-wrap deals-how-icon-2">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                </div>
                <div class="deals-how-step-title">Chọn Khách Sạn</div>
                <div class="deals-how-step-desc">Tìm chỗ nghỉ ưng ý. Nhập mã khuyến mãi vào ô "Áp dụng phiếu giảm giá" khi đặt phòng.</div>
            </div>

            <div class="deals-how-arrow">→</div>

            <div class="deals-how-step">
                <div class="deals-how-num">3</div>
                <div class="deals-how-img">
                    <div class="deals-how-icon-wrap deals-how-icon-3">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4M15 15h3" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <div class="deals-how-step-title">Thanh Toán & Tận Hưởng</div>
                <div class="deals-how-step-desc">Đảm bảo áp dụng phiếu giảm giá / mã khuyến mãi của bạn trước khi hoàn tất thanh toán.</div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ============================================================
   DEALS PAGE
   ============================================================ */

/* --- Hero --- */
.deals-hero {
    position: relative;
    background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 40%, #db2777 80%, #f472b6 100%);
    overflow: hidden;
    padding: 56px 0 48px;
    min-height: 220px;
}
.deals-hero-bg {
    position: absolute; inset: 0;
    background:
        radial-gradient(circle at 20% 50%, rgba(255,255,255,.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.06) 0%, transparent 40%);
    pointer-events: none;
}
.deals-hero-content {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
}
.deals-hero-label {
    display: inline-block;
    background: rgba(255,255,255,.2);
    color: #fff;
    font-size: 12px; font-weight: 700;
    border-radius: 20px;
    padding: 4px 14px;
    margin-bottom: 12px;
    backdrop-filter: blur(6px);
}
.deals-hero-title {
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif;
    font-size: 38px; font-weight: 700;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 10px;
}
.deals-hero-sub {
    font-size: 15px;
    color: rgba(255,255,255,.85);
    line-height: 1.6;
    margin-bottom: 24px;
}
.deals-hero-btn {
    display: inline-block;
    background: #fff;
    color: #7c3aed;
    font-weight: 700; font-size: 14px;
    padding: 12px 28px;
    border-radius: 30px;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    transition: transform .2s, box-shadow .2s;
}
.deals-hero-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.25); }

/* decorative chars */
.deals-hero-chars { display: flex; gap: 16px; flex-shrink: 0; }
.deals-char-circle {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
    border: 1.5px solid rgba(255,255,255,.25);
    animation: float-char 3s ease-in-out infinite;
}
.deals-char-2 .deals-char-circle { animation-delay: .8s; }
.deals-char-3 .deals-char-circle { animation-delay: 1.6s; }
@keyframes float-char {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-8px); }
}

/* --- Filter bar --- */
.deals-filter-bar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 0;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.deals-filter-inner {
    display: flex; align-items: center; gap: 12px;
    overflow-x: auto; overflow-y: hidden;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;
}
.deals-filter-inner::-webkit-scrollbar { display: none; }
.deals-filter-label { font-size: 13px; font-weight: 600; color: #64748b; white-space: nowrap; flex-shrink: 0; }
.deals-filter-tabs  { display: flex; gap: 8px; flex-wrap: nowrap; flex-shrink: 0; }
.deals-ftab {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px; font-weight: 500;
    color: #374151;
    background: #f1f5f9;
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: all .18s;
    white-space: nowrap;
}
.deals-ftab:hover { background: #e0e7ff; color: #4338ca; }
.deals-ftab.active { background: #7c3aed; color: #fff; border-color: #7c3aed; }
.deals-ftab-count {
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.25);
    border-radius: 10px;
    font-size: 10px; font-weight: 700;
    padding: 1px 6px;
    margin-left: 4px;
}
.deals-ftab.active .deals-ftab-count { background: rgba(255,255,255,.3); }
.deals-ftab:not(.active) .deals-ftab-count { background: #e2e8f0; color: #64748b; }

/* --- Deal Cards Grid --- */
.deals-cards-section { padding: 40px 0 8px; }
.deals-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 900px;
    margin: 0 auto;
}
.deals-card {
    border-radius: 18px;
    overflow: hidden;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1.5px solid #bfdbfe;
    transition: transform .2s, box-shadow .2s;
    display: flex; flex-direction: column;
}
.deals-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }

.deals-card-weekend  { background: linear-gradient(135deg, #fff7ed, #fed7aa); border-color: #fdba74; }
.deals-card-newuser  { background: linear-gradient(135deg, #f0fdf4, #bbf7d0); border-color: #6ee7b7; }
.deals-card-early    { background: linear-gradient(135deg, #fefce8, #fde68a); border-color: #fcd34d; }
.deals-card-local    { background: linear-gradient(135deg, #faf5ff, #e9d5ff); border-color: #c4b5fd; }

.deals-card-badge {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 18px 12px 14px;
    text-align: center;
}
.deals-card-badge-green  { background: linear-gradient(135deg, #065f46, #059669); }
.deals-card-badge-orange { background: linear-gradient(135deg, #92400e, #f59e0b); }
.deals-card-badge-purple { background: linear-gradient(135deg, #4c1d95, #7c3aed); }

.dcb-up  { font-size: 9.5px; font-weight: 700; letter-spacing: .3px; opacity: .9; white-space: nowrap; }
.dcb-pct { font-size: 36px; font-weight: 900; line-height: 1; }
.dcb-pct small { font-size: 18px; }

.deals-card-body {
    padding: 18px 16px 20px;
    display: flex; flex-direction: column; flex: 1;
}
.deals-card-icon  { font-size: 26px; margin-bottom: 8px; }
.deals-card-title { font-size: 15px; font-weight: 800; color: #1a202c; margin-bottom: 6px; }
.deals-card-desc  { font-size: 12.5px; color: #4b5563; line-height: 1.5; margin-bottom: 14px; }

.deals-card-btn {
    display: block; text-align: center;
    background: #1e40af; color: #fff;
    font-size: 11.5px; font-weight: 700; letter-spacing: .5px;
    padding: 9px 14px; border-radius: 8px;
    text-decoration: none;
    transition: opacity .15s, transform .15s;
    margin-top: auto;
}
.deals-card-btn:hover { opacity: .88; transform: translateY(-1px); }
.deals-card-btn-green  { background: #059669; }
.deals-card-btn-orange { background: #d97706; }
.deals-card-btn-purple { background: #7c3aed; }

/* --- Promo code box --- */
.deals-code-box {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,.6);
    border: 1.5px dashed rgba(0,0,0,.2);
    border-radius: 8px; padding: 8px 12px;
    margin-bottom: 10px;
}
.deals-code-box--orange {
    background: rgba(255,255,255,.6);
    border-color: rgba(0,0,0,.2);
}
.deals-code-text {
    font-family: monospace; font-size: 18px; font-weight: 900;
    letter-spacing: 2px; color: #1a202c;
}
.deals-code-copy {
    background: rgba(0,0,0,.08); border: none; border-radius: 6px;
    padding: 5px 8px; cursor: pointer; color: #374151;
    display: flex; align-items: center;
    transition: background .2s;
}
.deals-code-copy:hover { background: rgba(0,0,0,.15); }
.deals-code-copy--orange { background: rgba(0,0,0,.08); }
.deals-code-used {
    text-align: center; font-size: 13px;
    color: rgba(255,255,255,.7);
    padding: 10px 0;
    font-style: italic;
}

/* --- Section common --- */
.deals-section { padding: 40px 0; }
.deals-section-alt { background: #f8fafc; }
.deals-section-head {
    display: flex; justify-content: space-between; align-items: flex-end;
    margin-bottom: 24px;
}
.deals-section-tag {
    display: inline-block;
    background: #fff3cd; color: #92400e;
    font-size: 12px; font-weight: 700;
    border-radius: 20px; padding: 3px 12px;
    margin-bottom: 6px;
}
.deals-section-title {
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif;
    font-size: 24px; font-weight: 700;
    color: #1a202c; margin: 0;
}
.deals-section-sub { font-size: 13.5px; color: #64748b; margin-top: 3px; }
.deals-see-all {
    font-size: 13.5px; font-weight: 700;
    color: #7c3aed; text-decoration: none;
    white-space: nowrap;
}
.deals-see-all:hover { text-decoration: underline; }

/* --- Hotel grid --- */
.deals-hotel-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.deals-hotel-grid-4 { grid-template-columns: repeat(4, 1fr); }

.deals-hotel-card {
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    border: 1px solid #e2e8f0;
    text-decoration: none; color: inherit;
    transition: transform .2s, box-shadow .2s;
    display: block;
}
.deals-hotel-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,.12); }

.deals-hotel-img-wrap {
    position: relative;
    height: 160px; overflow: hidden;
}
.deals-hotel-img-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s;
}
.deals-hotel-card:hover .deals-hotel-img-wrap img { transform: scale(1.05); }

.deals-hotel-badge-weekend {
    position: absolute; top: 10px; left: 10px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: #fff; font-size: 10.5px; font-weight: 700;
    padding: 3px 9px; border-radius: 20px;
}
.deals-hotel-disc {
    position: absolute; top: 10px; right: 10px;
    background: #0066cc; color: #fff;
    font-size: 11px; font-weight: 800;
    padding: 3px 8px; border-radius: 6px;
}

.deals-hotel-body { padding: 12px 14px 14px; }
.deals-hotel-loc  { font-size: 11px; color: #64748b; margin-bottom: 4px; }
.deals-hotel-name { font-size: 14px; font-weight: 700; color: #1a202c; line-height: 1.35; margin-bottom: 6px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.deals-hotel-rating { display: flex; align-items: center; gap: 5px; margin-bottom: 8px; }
.deals-hotel-score {
    background: #7c3aed; color: #fff;
    font-size: 11px; font-weight: 800;
    padding: 2px 7px; border-radius: 5px;
}
.deals-hotel-review-text  { font-size: 11.5px; color: #374151; font-weight: 600; }
.deals-hotel-review-count { font-size: 11px; color: #94a3b8; }

.deals-hotel-price-wrap { display: flex; align-items: baseline; gap: 5px; }
.deals-hotel-old-price {
    font-size: 11.5px; color: #94a3b8;
    text-decoration: line-through;
}
.deals-hotel-price {
    font-size: 17px; font-weight: 800;
    color: #0066cc;
}
.deals-hotel-per { font-size: 11px; color: #94a3b8; }

/* --- How to steps --- */
.deals-how-section {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    padding: 56px 0;
    text-align: center;
}
.deals-how-title {
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif;
    font-size: 28px; font-weight: 700;
    color: #1a202c; margin-bottom: 8px;
}
.deals-how-sub { font-size: 14px; color: #64748b; margin-bottom: 40px; }

.deals-how-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
}
.deals-how-step {
    flex: 1; max-width: 240px;
    display: flex; flex-direction: column; align-items: center;
    text-align: center;
}
.deals-how-arrow {
    font-size: 24px; color: #c4b5fd;
    flex-shrink: 0; padding: 0 8px;
    padding-top: 8px;
}
.deals-how-num {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff; font-size: 16px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
    box-shadow: 0 4px 14px rgba(124,58,237,.4);
}
.deals-how-icon-wrap {
    width: 88px; height: 88px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
    transition: transform .2s;
}
.deals-how-step:hover .deals-how-icon-wrap { transform: scale(1.08); }
.deals-how-icon-1 { background: linear-gradient(135deg, #dbeafe, #93c5fd); color: #1e40af; }
.deals-how-icon-2 { background: linear-gradient(135deg, #dcfce7, #86efac); color: #065f46; }
.deals-how-icon-3 { background: linear-gradient(135deg, #fce7f3, #f9a8d4); color: #9d174d; }

.deals-how-step-title { font-size: 15px; font-weight: 700; color: #1a202c; margin-bottom: 8px; }
.deals-how-step-desc  { font-size: 13px; color: #64748b; line-height: 1.6; max-width: 200px; }

/* --- Responsive --- */
@media (max-width: 1024px) {
    .deals-cards-grid { grid-template-columns: repeat(2, 1fr); }
    .deals-hotel-grid-4 { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .deals-hero-title   { font-size: 26px; }
    .deals-hero-chars   { display: none; }
    .deals-cards-grid   { grid-template-columns: 1fr 1fr; gap: 12px; }
    .deals-hotel-grid   { grid-template-columns: 1fr 1fr; }
    .deals-hotel-grid-4 { grid-template-columns: 1fr 1fr; }
    .deals-how-steps    { flex-direction: column; gap: 24px; }
    .deals-how-arrow    { transform: rotate(90deg); }
}
@media (max-width: 480px) {
    .deals-cards-grid   { grid-template-columns: 1fr; }
    .deals-hotel-grid,
    .deals-hotel-grid-4 { grid-template-columns: 1fr; }
    .deals-section-head { flex-direction: column; align-items: flex-start; gap: 8px; }
}
</style>
@endpush

@push('scripts')
<script>
function copyCode(elId, btn) {
    const text = document.getElementById(elId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
        btn.style.background = 'rgba(255,255,255,.5)';
        setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; }, 1800);
    }).catch(() => {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    });
}
</script>
@endpush
