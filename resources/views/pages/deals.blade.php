@extends('layouts.app')
@section('title', 'Ưu đãi hôm nay')

@section('content')

{{-- ======================================================
     HERO BANNER — navy/blue + real photo background
====================================================== --}}
<div class="dh-hero">
    <div class="dh-hero-overlay"></div>
    <img class="dh-hero-photo" src="{{ asset('assets/images/pexels-photo-258154.jpeg') }}" alt="Resort background" loading="eager" fetchpriority="high">
    <div class="container dh-hero-inner">

        {{-- Left text --}}
        <div class="dh-hero-left">
            <div class="dh-hero-badge">🔥 Ưu đãi có hạn</div>
            <h1 class="dh-hero-title">Giảm giá đặc biệt<br><span class="dh-hero-accent">chỉ có tại StayGo</span></h1>
            <p class="dh-hero-sub">Khuyến mãi độc quyền mỗi ngày — không có ở nơi khác.<br>Đặt phòng sớm, tiết kiệm nhiều hơn.</p>
            <a href="{{ route('hotels.index') }}" class="dh-hero-btn">Tìm khách sạn ngay →</a>
        </div>

        {{-- Right: floating coupon card --}}
        <div class="dh-coupon-card">
            <div class="dh-coupon-top">
                <div class="dh-coupon-icon">🏷️</div>
                <div>
                    <div class="dh-coupon-label">Ưu đãi nổi bật hôm nay</div>
                    <div class="dh-coupon-code">WEEKEND20</div>
                </div>
                <button class="dh-coupon-copy" onclick="copyDealCode('WEEKEND20', this)" title="Sao chép mã">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                </button>
            </div>
            <div class="dh-coupon-discount">Giảm đến <strong>20%</strong> đặt phòng cuối tuần</div>
            <div class="dh-coupon-progress-wrap">
                <div class="dh-coupon-progress-label">
                    <span>Đã sử dụng</span>
                    <span class="dh-coupon-pct">82%</span>
                </div>
                <div class="dh-coupon-bar"><div class="dh-coupon-bar-fill" style="width:82%"></div></div>
                <div class="dh-coupon-slots">Còn <strong>36</strong> lượt sử dụng</div>
            </div>
            <div class="dh-coupon-footer">
                <div class="dh-coupon-expires">Hết hạn sau:</div>
                <div class="dh-coupon-countdown" data-deadline="weekend" id="countdown-hero">
                    <div class="dh-cd-block"><span class="dh-cd-num" id="ch-h">--</span><span class="dh-cd-lbl">giờ</span></div>
                    <div class="dh-cd-sep">:</div>
                    <div class="dh-cd-block"><span class="dh-cd-num" id="ch-m">--</span><span class="dh-cd-lbl">phút</span></div>
                    <div class="dh-cd-sep">:</div>
                    <div class="dh-cd-block"><span class="dh-cd-num" id="ch-s">--</span><span class="dh-cd-lbl">giây</span></div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ======================================================
     BỘ LỌC ĐỊA ĐIỂM
====================================================== --}}
<div class="dh-filter-bar">
    <div class="container">
        <div class="dh-filter-inner">
            <span class="dh-filter-label">Lọc theo địa điểm:</span>
            <div class="dh-filter-tabs">
                <a href="{{ route('deals.index') }}"
                   class="dh-ftab {{ !request('location') ? 'active' : '' }}">Tất cả</a>
                @foreach($locations as $loc)
                <a href="{{ route('deals.index', ['location' => $loc->id]) }}"
                   class="dh-ftab {{ request('location') == $loc->id ? 'active' : '' }}">
                    {{ $loc->name }}
                    <span class="dh-ftab-count">{{ $loc->hotels_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     DEAL CARDS
====================================================== --}}
<div class="container dh-cards-section">
    <div class="dh-section-head">
        <div>
            <div class="dh-section-tag">Mã giảm giá</div>
            <h2 class="dh-section-title">Ưu đãi đang có hiệu lực</h2>
        </div>
    </div>
    <div class="dh-cards-grid">

        {{-- Card 1: Cuối tuần --}}
        <div class="dh-card">
            <div class="dh-card-header dh-card-header--blue">
                <div class="dh-card-badge-row">
                    <span class="dh-card-hot-badge">🔥 HOT</span>
                </div>
                <div class="dh-card-discount">20<small>%</small></div>
                <div class="dh-card-discount-label">Giảm cuối tuần</div>
            </div>
            <div class="dh-card-body">
                <div class="dh-card-icon">🌅</div>
                <div class="dh-card-title">Ưu đãi Cuối Tuần</div>
                <div class="dh-card-desc">Giảm đến 20% cho mọi đặt phòng. Tối đa 500.000đ. Đơn tối thiểu 300.000đ.</div>
                <div class="dh-code-row">
                    <span class="dh-code-text" id="code-weekend">WEEKEND20</span>
                    <button class="dh-code-copy" onclick="copyDealCode('WEEKEND20', this)" title="Sao chép">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <div class="dh-progress-wrap">
                    <div class="dh-progress-info">
                        <span>Đã dùng</span><span class="dh-pct">82%</span>
                    </div>
                    <div class="dh-progress-bar"><div class="dh-progress-fill dh-fill--hot" style="width:82%"></div></div>
                </div>
                <div class="dh-countdown-row" data-deadline="weekend">
                    <span class="dh-cd-label">⏱ Còn:</span>
                    <span class="dh-cd-timer" id="cd-weekend">--:--:--</span>
                </div>
                <a href="{{ route('hotels.index', ['weekend' => 1]) }}" class="dh-card-btn dh-card-btn--blue">Xem khách sạn áp dụng</a>
            </div>
        </div>

        {{-- Card 2: Khách mới --}}
        <div class="dh-card">
            <div class="dh-card-header dh-card-header--green">
                <div class="dh-card-badge-row">
                    <span class="dh-card-new-badge">✨ MỚI</span>
                </div>
                <div class="dh-card-discount">10<small>%</small></div>
                <div class="dh-card-discount-label">Khách hàng mới</div>
            </div>
            <div class="dh-card-body">
                <div class="dh-card-icon">🎉</div>
                <div class="dh-card-title">Khách Hàng Mới</div>
                <div class="dh-card-desc">Giảm 10% đơn đặt phòng đầu tiên. Chỉ áp dụng 1 lần cho tài khoản mới.</div>
                <div class="dh-code-row">
                    <span class="dh-code-text" id="code-newuser">NEWUSER10</span>
                    <button class="dh-code-copy" onclick="copyDealCode('NEWUSER10', this)" title="Sao chép">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <div class="dh-progress-wrap">
                    <div class="dh-progress-info">
                        <span>Đã dùng</span><span class="dh-pct">45%</span>
                    </div>
                    <div class="dh-progress-bar"><div class="dh-progress-fill dh-fill--ok" style="width:45%"></div></div>
                </div>
                <div class="dh-countdown-row" data-deadline="month">
                    <span class="dh-cd-label">⏱ Còn:</span>
                    <span class="dh-cd-timer" id="cd-newuser">--:--:--</span>
                </div>
                @auth
                    @if(\App\Models\Booking::where('user_id', Auth::id())->whereNotIn('status',['cancelled'])->count() === 0)
                    <a href="{{ route('promo.new_user') }}" class="dh-card-btn dh-card-btn--green">Nhận ưu đãi ngay</a>
                    @else
                    <div class="dh-used-note">Bạn đã sử dụng ưu đãi này rồi</div>
                    @endif
                @else
                <a href="{{ route('login') }}" class="dh-card-btn dh-card-btn--green">Đăng nhập để nhận</a>
                @endauth
            </div>
        </div>

        {{-- Card 3: Đặt sớm --}}
        <div class="dh-card">
            <div class="dh-card-header dh-card-header--orange">
                <div class="dh-card-badge-row">
                    <span class="dh-card-flash-badge">⚡ FLASH SALE</span>
                </div>
                <div class="dh-card-discount">15<small>%</small></div>
                <div class="dh-card-discount-label">Đặt phòng sớm</div>
            </div>
            <div class="dh-card-body">
                <div class="dh-card-icon">⚡</div>
                <div class="dh-card-title">Đặt Phòng Sớm</div>
                <div class="dh-card-desc">Giảm 15% khi đặt trước 7 ngày. Tối đa 300.000đ. Đơn tối thiểu 500.000đ.</div>
                <div class="dh-code-row">
                    <span class="dh-code-text" id="code-early">EARLY15</span>
                    <button class="dh-code-copy" onclick="copyDealCode('EARLY15', this)" title="Sao chép">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <div class="dh-progress-wrap">
                    <div class="dh-progress-info">
                        <span>Đã dùng</span><span class="dh-pct">68%</span>
                    </div>
                    <div class="dh-progress-bar"><div class="dh-progress-fill dh-fill--warn" style="width:68%"></div></div>
                </div>
                <div class="dh-countdown-row" data-deadline="day">
                    <span class="dh-cd-label">⏱ Còn:</span>
                    <span class="dh-cd-timer" id="cd-early">--:--:--</span>
                </div>
                <a href="{{ route('hotels.index') }}" class="dh-card-btn dh-card-btn--orange">Xem khách sạn áp dụng</a>
            </div>
        </div>

    </div>
</div>

{{-- ======================================================
     CÁCH NHẬN ƯU ĐÃI — 4 BƯỚC (nền trắng)
====================================================== --}}
<div class="dh-how-section">
    <div class="container">
        <div class="dh-section-head dh-section-head--center">
            <div class="dh-section-tag">Hướng dẫn</div>
            <h2 class="dh-section-title">Cách Áp Dụng Ưu Đãi</h2>
            <p class="dh-section-sub">4 bước đơn giản để nhận ngay khuyến mãi tốt nhất</p>
        </div>
        <div class="dh-how-steps">

            <div class="dh-how-step">
                <div class="dh-how-num">1</div>
                <div class="dh-how-icon dh-how-icon--blue">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M9 7h6M9 11h6M9 15h4"/></svg>
                </div>
                <div class="dh-how-step-title">Chọn mã ưu đãi</div>
                <div class="dh-how-step-desc">Tìm mã giảm giá phù hợp với chuyến đi của bạn. Nhấn sao chép để lưu mã.</div>
            </div>

            <div class="dh-how-arrow">→</div>

            <div class="dh-how-step">
                <div class="dh-how-num">2</div>
                <div class="dh-how-icon dh-how-icon--green">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="dh-how-step-title">Chọn khách sạn</div>
                <div class="dh-how-step-desc">Tìm chỗ nghỉ ưng ý trên StayGo. Chọn phòng và ngày nhận phòng.</div>
            </div>

            <div class="dh-how-arrow">→</div>

            <div class="dh-how-step">
                <div class="dh-how-num">3</div>
                <div class="dh-how-icon dh-how-icon--purple">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                </div>
                <div class="dh-how-step-title">Nhập mã giảm giá</div>
                <div class="dh-how-step-desc">Dán mã vào ô "Áp dụng phiếu giảm giá" khi đặt phòng để nhận ưu đãi.</div>
            </div>

            <div class="dh-how-arrow">→</div>

            <div class="dh-how-step">
                <div class="dh-how-num">4</div>
                <div class="dh-how-icon dh-how-icon--orange">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4M15 15h3" stroke-linecap="round"/></svg>
                </div>
                <div class="dh-how-step-title">Thanh toán &amp; tận hưởng</div>
                <div class="dh-how-step-desc">Hoàn tất thanh toán và chuẩn bị cho kỳ nghỉ tuyệt vời cùng gia đình.</div>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================
     VÌ SAO CHỌN STAYGO — TRUST STATS
====================================================== --}}
<div class="dh-trust-section">
    <div class="container">
        <div class="dh-section-head dh-section-head--center">
            <div class="dh-section-tag">Lý do tin tưởng</div>
            <h2 class="dh-section-title">Vì sao chọn StayGo?</h2>
            <p class="dh-section-sub">Hàng nghìn khách hàng đã tin tưởng đặt phòng qua StayGo</p>
        </div>
        <div class="dh-trust-grid">
            <div class="dh-trust-card">
                <div class="dh-trust-icon">🏨</div>
                <div class="dh-trust-number">5.000+</div>
                <div class="dh-trust-label">Lượt đặt phòng thành công</div>
            </div>
            <div class="dh-trust-card">
                <div class="dh-trust-icon">⭐</div>
                <div class="dh-trust-number">4.8/5</div>
                <div class="dh-trust-label">Điểm đánh giá trung bình</div>
            </div>
            <div class="dh-trust-card">
                <div class="dh-trust-icon">🏩</div>
                <div class="dh-trust-number">200+</div>
                <div class="dh-trust-label">Khách sạn &amp; resort đối tác</div>
            </div>
            <div class="dh-trust-card">
                <div class="dh-trust-icon">🛎️</div>
                <div class="dh-trust-number">24/7</div>
                <div class="dh-trust-label">Hỗ trợ khách hàng</div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================
     NEWSLETTER + APP DOWNLOAD
====================================================== --}}
<div class="dh-bottom-section">
    <div class="container dh-bottom-grid">

        {{-- Newsletter --}}
        <div class="dh-newsletter">
            <div class="dh-newsletter-icon">📬</div>
            <h3 class="dh-newsletter-title">Nhận ưu đãi qua email</h3>
            <p class="dh-newsletter-sub">Đăng ký để không bỏ lỡ bất kỳ khuyến mãi độc quyền nào từ StayGo.</p>
            <form class="dh-newsletter-form" onsubmit="handleNewsletter(event)">
                <input type="email" class="dh-newsletter-input" placeholder="Nhập email của bạn..." required>
                <button type="submit" class="dh-newsletter-btn">Đăng ký</button>
            </form>
            <div class="dh-newsletter-note">Không spam. Có thể hủy bất cứ lúc nào.</div>
        </div>

        {{-- App download --}}
        <div class="dh-app-download">
            <div class="dh-app-icon">📱</div>
            <h3 class="dh-app-title">Đặt phòng dễ dàng hơn</h3>
            <p class="dh-app-sub">Trải nghiệm StayGo trên điện thoại — tìm phòng, đặt ngay, nhận ưu đãi riêng dành cho app.</p>
            <div class="dh-app-badges">
                <div class="dh-app-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                    <div><div class="dh-app-badge-sub">Tải trên</div><div class="dh-app-badge-name">App Store</div></div>
                </div>
                <div class="dh-app-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5c.6.37.6 1.23 0 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/></svg>
                    <div><div class="dh-app-badge-sub">Tải trên</div><div class="dh-app-badge-name">Google Play</div></div>
                </div>
            </div>
            <div class="dh-app-note">✓ Miễn phí &nbsp;·&nbsp; ✓ Không quảng cáo &nbsp;·&nbsp; ✓ Bảo mật</div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
/* ============================================================
   DEALS PAGE — Redesign 2026
   ============================================================ */

/* ---- Hero ---- */
.dh-hero {
    position: relative;
    min-height: 380px;
    display: flex;
    align-items: center;
    overflow: hidden;
}
.dh-hero-photo {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center 40%;
    z-index: 0;
}
.dh-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(100deg, rgba(0,30,80,.82) 0%, rgba(0,64,180,.65) 55%, rgba(0,20,60,.45) 100%);
    z-index: 1;
}
.dh-hero-inner {
    position: relative; z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
    padding-top: 56px; padding-bottom: 56px;
}
.dh-hero-left { flex: 1; min-width: 0; }
.dh-hero-badge {
    display: inline-block;
    background: rgba(255,255,255,.18);
    color: #fff; font-size: 12px; font-weight: 700;
    border-radius: 20px; padding: 4px 14px;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.25);
    margin-bottom: 14px;
}
.dh-hero-title {
    font-size: 40px; font-weight: 800;
    color: #fff; line-height: 1.15;
    margin-bottom: 12px;
}
.dh-hero-accent { color: #FFD700; }
.dh-hero-sub {
    font-size: 15px; color: rgba(255,255,255,.85);
    line-height: 1.65; margin-bottom: 28px;
}
.dh-hero-btn {
    display: inline-block;
    background: #fff; color: #0064D2;
    font-weight: 700; font-size: 14px;
    padding: 13px 30px; border-radius: 30px;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
    transition: transform .2s, box-shadow .2s;
}
.dh-hero-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.3); }

/* ---- Floating coupon card ---- */
.dh-coupon-card {
    flex-shrink: 0;
    width: 288px;
    background: rgba(255,255,255,.96);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    border: 1px solid rgba(255,255,255,.6);
}
.dh-coupon-top {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 10px;
}
.dh-coupon-icon { font-size: 24px; }
.dh-coupon-label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.dh-coupon-code { font-size: 18px; font-weight: 900; color: #0064D2; letter-spacing: 2px; font-family: monospace; }
.dh-coupon-copy {
    margin-left: auto; background: #f1f5f9; border: none;
    border-radius: 8px; padding: 6px 10px; cursor: pointer;
    color: #0064D2; display: flex; align-items: center;
    transition: background .2s;
}
.dh-coupon-copy:hover { background: #dbeafe; }
.dh-coupon-discount { font-size: 13px; color: #374151; margin-bottom: 12px; }
.dh-coupon-discount strong { color: #0064D2; }
.dh-coupon-progress-wrap { margin-bottom: 14px; }
.dh-coupon-progress-label {
    display: flex; justify-content: space-between;
    font-size: 11px; color: #64748b; margin-bottom: 5px;
}
.dh-coupon-pct { font-weight: 700; color: #ef4444; }
.dh-coupon-bar {
    height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden;
}
.dh-coupon-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #0064D2, #ef4444);
    border-radius: 99px;
    transition: width .8s ease;
}
.dh-coupon-slots { font-size: 11px; color: #64748b; margin-top: 5px; }
.dh-coupon-slots strong { color: #ef4444; }
.dh-coupon-footer { border-top: 1px dashed #e2e8f0; padding-top: 12px; }
.dh-coupon-expires { font-size: 11px; color: #64748b; margin-bottom: 6px; }
.dh-coupon-countdown {
    display: flex; align-items: center; gap: 4px;
}
.dh-cd-block { text-align: center; }
.dh-cd-num {
    display: block;
    font-size: 22px; font-weight: 800; color: #0f172a;
    background: #f1f5f9; border-radius: 8px;
    padding: 4px 10px; min-width: 44px;
    font-family: monospace;
}
.dh-cd-lbl { font-size: 9px; color: #94a3b8; display: block; margin-top: 2px; }
.dh-cd-sep { font-size: 20px; font-weight: 700; color: #64748b; padding-bottom: 14px; }

/* ---- Filter bar ---- */
.dh-filter-bar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 0;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.dh-filter-inner {
    display: flex; align-items: center; gap: 12px;
    overflow-x: auto; scrollbar-width: none;
}
.dh-filter-inner::-webkit-scrollbar { display: none; }
.dh-filter-label { font-size: 13px; font-weight: 600; color: #64748b; white-space: nowrap; flex-shrink: 0; }
.dh-filter-tabs { display: flex; gap: 8px; flex-shrink: 0; }
.dh-ftab {
    padding: 6px 16px; border-radius: 20px;
    font-size: 13px; font-weight: 500; color: #374151;
    background: #f1f5f9; text-decoration: none;
    border: 1.5px solid transparent; transition: all .18s; white-space: nowrap;
}
.dh-ftab:hover { background: #dbeafe; color: #0064D2; }
.dh-ftab.active { background: #0064D2; color: #fff; border-color: #0064D2; }
.dh-ftab-count {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px; font-size: 10px; font-weight: 700;
    padding: 1px 6px; margin-left: 4px;
    background: rgba(255,255,255,.3);
}
.dh-ftab:not(.active) .dh-ftab-count { background: #e2e8f0; color: #64748b; }

/* ---- Section headers ---- */
.dh-cards-section { padding: 48px 0 16px; }
.dh-section-head { margin-bottom: 28px; }
.dh-section-head--center { text-align: center; }
.dh-section-tag {
    display: inline-block;
    background: #dbeafe; color: #1e40af;
    font-size: 11.5px; font-weight: 700;
    border-radius: 20px; padding: 3px 12px;
    margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px;
}
.dh-section-title {
    font-size: 26px; font-weight: 800;
    color: #0f172a; margin: 0 0 4px;
}
.dh-section-sub { font-size: 14px; color: #64748b; }

/* ---- Deal cards grid ---- */
.dh-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.dh-card {
    border-radius: 24px; overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    border: 1px solid #e2e8f0;
    display: flex; flex-direction: column;
    transition: transform .22s, box-shadow .22s;
}
.dh-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(0,0,0,.14); }

.dh-card-header {
    padding: 22px 20px 16px;
    text-align: center; color: #fff;
    position: relative;
}
.dh-card-header--blue   { background: linear-gradient(135deg, #1e40af, #0064D2); }
.dh-card-header--green  { background: linear-gradient(135deg, #065f46, #059669); }
.dh-card-header--orange { background: linear-gradient(135deg, #b45309, #f59e0b); }

.dh-card-badge-row { margin-bottom: 8px; }
.dh-card-hot-badge, .dh-card-new-badge, .dh-card-flash-badge {
    font-size: 10.5px; font-weight: 800; letter-spacing: .5px;
    background: rgba(255,255,255,.22); border-radius: 20px; padding: 3px 10px;
}
.dh-card-discount { font-size: 48px; font-weight: 900; line-height: 1; }
.dh-card-discount small { font-size: 22px; }
.dh-card-discount-label { font-size: 11px; opacity: .88; margin-top: 2px; font-weight: 600; }

.dh-card-body {
    padding: 18px 18px 20px;
    display: flex; flex-direction: column; flex: 1;
}
.dh-card-icon  { font-size: 24px; margin-bottom: 8px; }
.dh-card-title { font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.dh-card-desc  { font-size: 12.5px; color: #4b5563; line-height: 1.55; margin-bottom: 14px; }

/* ---- Code row ---- */
.dh-code-row {
    display: flex; align-items: center; justify-content: space-between;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px; padding: 8px 12px;
    margin-bottom: 12px;
}
.dh-code-text { font-family: monospace; font-size: 17px; font-weight: 900; letter-spacing: 2px; color: #0f172a; }
.dh-code-copy {
    background: #e2e8f0; border: none; border-radius: 7px;
    padding: 5px 9px; cursor: pointer; color: #0064D2;
    display: flex; align-items: center; transition: background .2s;
}
.dh-code-copy:hover { background: #bfdbfe; }

/* ---- Progress bar ---- */
.dh-progress-wrap { margin-bottom: 10px; }
.dh-progress-info {
    display: flex; justify-content: space-between;
    font-size: 11px; color: #64748b; margin-bottom: 5px;
}
.dh-pct { font-weight: 700; }
.dh-progress-bar { height: 7px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.dh-progress-fill { height: 100%; border-radius: 99px; transition: width .8s ease; }
.dh-fill--hot  { background: linear-gradient(90deg, #0064D2, #ef4444); }
.dh-fill--ok   { background: linear-gradient(90deg, #059669, #34d399); }
.dh-fill--warn { background: linear-gradient(90deg, #d97706, #f59e0b); }

/* ---- Countdown inline ---- */
.dh-countdown-row {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #64748b;
    margin-bottom: 14px;
}
.dh-cd-label { font-weight: 600; }
.dh-cd-timer { font-family: monospace; font-weight: 800; color: #ef4444; font-size: 13px; }

/* ---- Card btn ---- */
.dh-card-btn {
    display: block; text-align: center;
    font-size: 12.5px; font-weight: 700; letter-spacing: .3px;
    padding: 11px 14px; border-radius: 12px;
    text-decoration: none; margin-top: auto;
    transition: opacity .15s, transform .15s;
}
.dh-card-btn:hover { opacity: .88; transform: translateY(-1px); }
.dh-card-btn--blue   { background: #0064D2; color: #fff; }
.dh-card-btn--green  { background: #059669; color: #fff; }
.dh-card-btn--orange { background: #d97706; color: #fff; }
.dh-used-note { text-align: center; font-size: 12.5px; color: #94a3b8; font-style: italic; padding: 10px 0; margin-top: auto; }

/* ---- How to section ---- */
.dh-how-section {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    padding: 60px 0;
}
.dh-how-steps {
    display: flex; align-items: flex-start; justify-content: center; gap: 0;
    margin-top: 40px;
}
.dh-how-step {
    flex: 1; max-width: 210px;
    display: flex; flex-direction: column; align-items: center; text-align: center;
}
.dh-how-arrow {
    font-size: 22px; color: #cbd5e1;
    flex-shrink: 0; padding: 0 8px; padding-top: 54px;
}
.dh-how-num {
    width: 32px; height: 32px; border-radius: 50%;
    background: #0064D2; color: #fff;
    font-size: 14px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
    box-shadow: 0 4px 14px rgba(0,100,210,.35);
}
.dh-how-icon {
    width: 84px; height: 84px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
    transition: transform .22s;
}
.dh-how-step:hover .dh-how-icon { transform: scale(1.08); }
.dh-how-icon--blue   { background: #dbeafe; color: #1e40af; }
.dh-how-icon--green  { background: #dcfce7; color: #065f46; }
.dh-how-icon--purple { background: #f3e8ff; color: #6d28d9; }
.dh-how-icon--orange { background: #fef3c7; color: #92400e; }
.dh-how-step-title { font-size: 14.5px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.dh-how-step-desc  { font-size: 12.5px; color: #64748b; line-height: 1.6; max-width: 185px; }

/* ---- Trust stats ---- */
.dh-trust-section {
    background: linear-gradient(135deg, #eff6ff, #f0f9ff);
    padding: 60px 0;
}
.dh-trust-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 20px; margin-top: 36px;
}
.dh-trust-card {
    background: #fff; border-radius: 20px;
    padding: 28px 20px; text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    border: 1px solid #e2e8f0;
    transition: transform .2s, box-shadow .2s;
}
.dh-trust-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,.1); }
.dh-trust-icon  { font-size: 36px; margin-bottom: 12px; }
.dh-trust-number { font-size: 30px; font-weight: 900; color: #0064D2; margin-bottom: 4px; }
.dh-trust-label { font-size: 13px; color: #64748b; line-height: 1.4; }

/* ---- Bottom section ---- */
.dh-bottom-section {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 60px 0;
}
.dh-bottom-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 48px;
    align-items: start;
}
.dh-newsletter { color: #0f172a; }
.dh-newsletter-icon { font-size: 36px; margin-bottom: 12px; }
.dh-newsletter-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.dh-newsletter-sub { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 20px; }
.dh-newsletter-form { display: flex; gap: 8px; }
.dh-newsletter-input {
    flex: 1; padding: 12px 16px;
    border-radius: 12px; border: 1px solid #cbd5e1;
    background: #f8fafc; color: #0f172a; font-size: 14px;
    outline: none; transition: border-color .2s;
}
.dh-newsletter-input::placeholder { color: #94a3b8; }
.dh-newsletter-input:focus { border-color: #0064D2; background: #fff; }
.dh-newsletter-btn {
    padding: 12px 22px; border-radius: 12px;
    background: #0064D2; color: #fff;
    border: none; cursor: pointer; font-weight: 700; font-size: 14px;
    transition: background .2s;
    white-space: nowrap;
}
.dh-newsletter-btn:hover { background: #004fb3; }
.dh-newsletter-note { font-size: 11.5px; color: #94a3b8; margin-top: 10px; }

.dh-app-download { color: #0f172a; }
.dh-app-icon { font-size: 36px; margin-bottom: 12px; }
.dh-app-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.dh-app-sub { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 20px; }
.dh-app-badges { display: flex; gap: 12px; flex-wrap: wrap; }
.dh-app-badge {
    display: flex; align-items: center; gap: 10px;
    background: #f8fafc; border: 1.5px solid #e2e8f0;
    border-radius: 12px; padding: 11px 18px;
    color: #0f172a; cursor: pointer;
    transition: background .2s, border-color .2s, color .2s;
}
.dh-app-badge:hover { background: #0064D2; border-color: #0064D2; color: #fff; }
.dh-app-badge-sub { font-size: 9px; color: #64748b; }
.dh-app-badge:hover .dh-app-badge-sub { color: rgba(255,255,255,.75); }
.dh-app-badge-name { font-size: 15px; font-weight: 700; }
.dh-app-note { font-size: 11.5px; color: #94a3b8; margin-top: 16px; }

/* ---- Responsive ---- */
@media (max-width: 1024px) {
    .dh-cards-grid  { grid-template-columns: repeat(2, 1fr); }
    .dh-trust-grid  { grid-template-columns: repeat(2, 1fr); }
    .dh-how-steps   { flex-wrap: wrap; gap: 24px; }
    .dh-how-arrow   { display: none; }
    .dh-how-step    { max-width: 200px; flex: 0 0 auto; }
}
@media (max-width: 768px) {
    .dh-hero-title  { font-size: 28px; }
    .dh-coupon-card { display: none; }
    .dh-cards-grid  { grid-template-columns: 1fr 1fr; gap: 14px; }
    .dh-trust-grid  { grid-template-columns: repeat(2, 1fr); }
    .dh-bottom-grid { grid-template-columns: 1fr; gap: 36px; }
    .dh-how-steps   { justify-content: center; }
}
@media (max-width: 480px) {
    .dh-hero-inner  { flex-direction: column; align-items: flex-start; }
    .dh-cards-grid  { grid-template-columns: 1fr; }
    .dh-trust-grid  { grid-template-columns: 1fr 1fr; }
    .dh-newsletter-form { flex-direction: column; }
    .dh-newsletter-btn { width: 100%; }
}
</style>
@endpush

@push('scripts')
<script>
/* ---- Copy deal code ---- */
function copyDealCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
        btn.style.background = '#bbf7d0';
        btn.style.color = '#065f46';
        setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.style.color = ''; }, 1800);
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = code; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    });
}

/* ---- Newsletter ---- */
function handleNewsletter(e) {
    e.preventDefault();
    const btn = e.target.querySelector('.dh-newsletter-btn');
    btn.textContent = 'Đã đăng ký ✓';
    btn.style.background = '#059669';
    e.target.querySelector('input').value = '';
    setTimeout(() => { btn.textContent = 'Đăng ký'; btn.style.background = ''; }, 3000);
}

/* ---- Countdown timers ---- */
(function() {
    const now = new Date();

    function nextWeekend() {
        const d = new Date(now);
        const day = d.getDay(); // 0=Sun, 6=Sat
        const daysToSun = (7 - day) % 7 || 7;
        d.setDate(d.getDate() + daysToSun);
        d.setHours(23, 59, 59, 0);
        return d;
    }

    function endOfDay() {
        const d = new Date(now);
        d.setHours(23, 59, 59, 0);
        return d;
    }

    function endOfMonth() {
        const d = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
        return d;
    }

    function pad(n) { return String(n).padStart(2, '0'); }

    function formatHMS(ms) {
        if (ms <= 0) return '00:00:00';
        const s = Math.floor(ms / 1000);
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        return pad(h) + ':' + pad(m) + ':' + pad(sec);
    }

    const targets = {
        'weekend': nextWeekend(),
        'day':     endOfDay(),
        'month':   endOfMonth(),
    };

    /* Hero countdown (separate elements) */
    function updateHero() {
        const diff = targets['weekend'] - new Date();
        if (diff <= 0) { document.getElementById('ch-h').textContent = '00'; document.getElementById('ch-m').textContent = '00'; document.getElementById('ch-s').textContent = '00'; return; }
        const s = Math.floor(diff / 1000);
        document.getElementById('ch-h').textContent = pad(Math.floor(s / 3600));
        document.getElementById('ch-m').textContent = pad(Math.floor((s % 3600) / 60));
        document.getElementById('ch-s').textContent = pad(s % 60);
    }

    const inlineMap = {
        'cd-weekend': 'weekend',
        'cd-newuser': 'month',
        'cd-early':   'day',
    };

    function tick() {
        updateHero();
        for (const [id, key] of Object.entries(inlineMap)) {
            const el = document.getElementById(id);
            if (el) el.textContent = formatHMS(targets[key] - new Date());
        }
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush
