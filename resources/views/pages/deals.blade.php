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
     BỘ LỌC ĐỊA ĐIỂM — floating card
====================================================== --}}
<div class="dh-filter-wrap">
    <div class="container">
        <div class="dh-filter-card">
            <div class="dh-filter-label-col">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>Lọc ưu đãi theo<br>địa điểm</span>
            </div>
            <div class="dh-filter-divider"></div>
            <div class="dh-filter-tabs">
                @php
                $locIcons = ['Đà Lạt'=>'🌲','Nha Trang'=>'🌊','Vũng Tàu'=>'⛱️','Đà Nẵng'=>'🏖️'];
                @endphp
                <a href="{{ route('deals.index') }}"
                   class="dh-ftab {{ !request('location') ? 'active' : '' }}">
                    <span class="dh-ftab-icon">🎯</span> Tất cả
                </a>
                @foreach($locations as $loc)
                <a href="{{ route('deals.index', ['location' => $loc->id]) }}"
                   class="dh-ftab {{ request('location') == $loc->id ? 'active' : '' }}">
                    <span class="dh-ftab-icon">{{ $locIcons[$loc->name] ?? '📍' }}</span>
                    {{ $loc->name }}
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
     VÌ SAO CHỌN STAYGO — TRUST STATS (horizontal inline)
====================================================== --}}
<div class="dh-trust-section">
    <div class="container">
        <h2 class="dh-trust-title">Vì sao chọn StayGo?</h2>
        <div class="dh-trust-row">

            <div class="dh-trust-item">
                <div class="dh-trust-icon-wrap dh-trust-icon-wrap--blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="dh-trust-info">
                    <div class="dh-trust-number">5.000+</div>
                    <div class="dh-trust-label">Lượt đặt phòng<br>mỗi tháng</div>
                </div>
            </div>

            <div class="dh-trust-divider"></div>

            <div class="dh-trust-item">
                <div class="dh-trust-icon-wrap dh-trust-icon-wrap--yellow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="#f59e0b"/></svg>
                </div>
                <div class="dh-trust-info">
                    <div class="dh-trust-number">4.8/5</div>
                    <div class="dh-trust-label">Đánh giá trung bình<br>từ khách hàng</div>
                </div>
            </div>

            <div class="dh-trust-divider"></div>

            <div class="dh-trust-item">
                <div class="dh-trust-icon-wrap dh-trust-icon-wrap--green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                </div>
                <div class="dh-trust-info">
                    <div class="dh-trust-number">200+</div>
                    <div class="dh-trust-label">Khách sạn &amp; resort<br>đối tác</div>
                </div>
            </div>

            <div class="dh-trust-divider"></div>

            <div class="dh-trust-item">
                <div class="dh-trust-icon-wrap dh-trust-icon-wrap--purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.64A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                </div>
                <div class="dh-trust-info">
                    <div class="dh-trust-number">Hỗ trợ 24/7</div>
                    <div class="dh-trust-label">Đội ngũ chăm sóc<br>khách hàng tận tâm</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================
     NEWSLETTER + APP DOWNLOAD
====================================================== --}}
<div class="dh-bottom-section">
    <div class="container dh-bottom-grid">

        {{-- Newsletter card --}}
        <div class="dh-bottom-card dh-bottom-card--blue">
            <div class="dh-bottom-card-header">
                <div class="dh-bottom-card-ico">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0064D2" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <h3 class="dh-bottom-card-title">Nhận ưu đãi mới mỗi tuần!</h3>
                    <p class="dh-bottom-card-sub">Đăng ký nhận newsletter để không bỏ lỡ khuyến mãi hấp dẫn</p>
                </div>
            </div>
            <form class="dh-nl-form" onsubmit="handleNewsletter(event)">
                <div class="dh-nl-row">
                    <input type="email" class="dh-nl-input" placeholder="Nhập email của bạn..." required>
                    <button type="submit" class="dh-nl-btn">Đăng ký ngay</button>
                </div>
            </form>
            <div class="dh-nl-note">Không spam. Hủy đăng ký bất cứ lúc nào.</div>
        </div>

        {{-- App download card --}}
        <div class="dh-bottom-card dh-bottom-card--app">
            <div class="dh-app-content">
                <div class="dh-app-text">
                    <h3 class="dh-app-card-title">Tải ứng dụng StayGo</h3>
                    <p class="dh-app-card-sub">Đặt phòng nhanh hơn – Nhận ưu đãi độc quyền trên app</p>
                    <div class="dh-store-badges">
                        <div class="dh-store-badge">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            <div>
                                <div class="dh-store-sub">Tải về trên</div>
                                <div class="dh-store-name">App Store</div>
                            </div>
                        </div>
                        <div class="dh-store-badge">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M3.5 2.5L13 12L3.5 21.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M3.5 2.5L20 10.5L13 12" fill="white" stroke="white" stroke-width="0.5"/>
                                <path d="M3.5 21.5L20 13.5L13 12" fill="white" stroke="white" stroke-width="0.5"/>
                            </svg>
                            <div>
                                <div class="dh-store-sub">TẢI TRÊN</div>
                                <div class="dh-store-name">Google Play</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dh-phone-mockup">
                    <div class="dh-phone-frame">
                        <div class="dh-phone-notch"></div>
                        <div class="dh-phone-screen">
                            <div class="dh-phone-status">
                                <span>9:44</span>
                                <div class="dh-phone-icons">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="white"><path d="M1 6s4-4 11-4 11 4 11 4M5 10s3-3 7-3 7 3 7 3M9 14s1-1 3-1 3 1 3 1M11 18h2"/></svg>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="white"><rect x="2" y="7" width="18" height="11" rx="2"/><path d="M22 11v4" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                                </div>
                            </div>
                            <div class="dh-phone-app-header">
                                <div class="dh-phone-app-title">Ưu đãi dành riêng<br>cho bạn</div>
                            </div>
                            <div class="dh-phone-hotel-card">
                                <img src="{{ asset('assets/images/pexels-caronoir-36758154.jpg') }}" alt="Vinpearl Resort" class="dh-phone-hotel-img">
                                <div class="dh-phone-hotel-info">
                                    <div class="dh-phone-hotel-name">Vinpearl Resort<br>Nha Trang</div>
                                    <div class="dh-phone-hotel-price">Free</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

/* ---- Filter floating card ---- */
.dh-filter-wrap {
    padding: 0 0 0;
    margin-top: -28px;
    position: relative; z-index: 20;
}
.dh-filter-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,.12);
    padding: 18px 24px;
    display: flex; align-items: center; gap: 20px;
    overflow-x: auto; scrollbar-width: none;
}
.dh-filter-card::-webkit-scrollbar { display: none; }
.dh-filter-label-col {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700; color: #374151;
    line-height: 1.4; white-space: nowrap; flex-shrink: 0;
    min-width: 120px;
}
.dh-filter-label-col svg { color: #0064D2; flex-shrink: 0; }
.dh-filter-divider {
    width: 1px; height: 32px; background: #e2e8f0; flex-shrink: 0;
}
.dh-filter-tabs {
    display: flex; gap: 10px; flex-shrink: 0;
    overflow-x: auto; scrollbar-width: none;
}
.dh-filter-tabs::-webkit-scrollbar { display: none; }
.dh-ftab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 99px;
    font-size: 13.5px; font-weight: 600; color: #374151;
    background: #f8fafc; text-decoration: none;
    border: 1.5px solid #e2e8f0;
    transition: all .2s; white-space: nowrap;
}
.dh-ftab:hover {
    background: #ede9fe; color: #6d28d9; border-color: #c4b5fd;
}
.dh-ftab.active {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff; border-color: transparent;
    box-shadow: 0 4px 14px rgba(124,58,237,.35);
}
.dh-ftab-icon { font-size: 15px; line-height: 1; }

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

/* ---- Trust stats — horizontal row ---- */
.dh-trust-section {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    padding: 48px 0;
}
.dh-trust-title {
    font-size: 22px; font-weight: 800; color: #0f172a;
    text-align: center; margin-bottom: 32px;
    text-transform: uppercase; letter-spacing: .5px;
}
.dh-trust-row {
    display: flex; align-items: center; justify-content: space-between;
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 24px rgba(0,0,0,.05);
    overflow: hidden;
}
.dh-trust-item {
    flex: 1; display: flex; align-items: center; gap: 14px;
    padding: 28px 24px;
    transition: background .2s;
}
.dh-trust-item:hover { background: #f8fafc; }
.dh-trust-divider {
    width: 1px; height: 60px;
    background: #e2e8f0; flex-shrink: 0;
}
.dh-trust-icon-wrap {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.dh-trust-icon-wrap--blue   { background: #dbeafe; color: #1e40af; }
.dh-trust-icon-wrap--yellow { background: #fef3c7; color: #d97706; }
.dh-trust-icon-wrap--green  { background: #dcfce7; color: #065f46; }
.dh-trust-icon-wrap--purple { background: #f3e8ff; color: #6d28d9; }
.dh-trust-info { min-width: 0; }
.dh-trust-number { font-size: 22px; font-weight: 900; color: #0064D2; line-height: 1.1; }
.dh-trust-label  { font-size: 12px; color: #64748b; line-height: 1.5; margin-top: 3px; }

/* ---- Bottom section ---- */
.dh-bottom-section {
    background: #f8fafc;
    padding: 56px 0;
}
.dh-bottom-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
    align-items: stretch;
}

/* ---- Bottom cards ---- */
.dh-bottom-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    border: 1px solid #e2e8f0;
    display: flex; flex-direction: column;
}
.dh-bottom-card--blue { border: 1px solid #e2e8f0; }
.dh-bottom-card--app  { border: 1px solid #e2e8f0; overflow: hidden; }
.dh-bottom-card-header {
    display: flex; align-items: flex-start; gap: 14px;
    margin-bottom: 20px;
}
.dh-bottom-card-ico {
    width: 48px; height: 48px; border-radius: 12px;
    background: #dbeafe; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.dh-bottom-card-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.dh-bottom-card-sub   { font-size: 13px; color: #64748b; line-height: 1.55; }

/* ---- Newsletter form ---- */
.dh-nl-form { display: flex; flex-direction: column; gap: 10px; }
.dh-nl-row  { display: flex; gap: 8px; }
.dh-nl-input {
    flex: 1; padding: 12px 16px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc; color: #0f172a; font-size: 14px;
    outline: none; transition: border-color .2s, box-shadow .2s;
}
.dh-nl-input::placeholder { color: #94a3b8; }
.dh-nl-input:focus {
    border-color: #0064D2; background: #fff;
    box-shadow: 0 0 0 3px rgba(0,100,210,.1);
}
.dh-nl-btn {
    padding: 12px 22px; border-radius: 10px;
    background: #0064D2; color: #fff;
    border: none; cursor: pointer; font-weight: 700; font-size: 14px;
    white-space: nowrap;
    transition: background .18s, transform .18s;
}
.dh-nl-btn:hover { background: #004fb3; transform: translateY(-1px); }
.dh-nl-note { font-size: 12px; color: #94a3b8; margin-top: 8px; }

/* ---- App card ---- */
.dh-bottom-card--app {
    background: #f0f4ff;
    border: 1px solid #e0e8ff;
    overflow: visible;
}
.dh-app-content {
    display: flex; align-items: center; gap: 0; height: 100%;
}
.dh-app-text { flex: 1; min-width: 0; padding-right: 8px; }
.dh-app-card-title {
    font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;
}
.dh-app-card-sub {
    font-size: 13.5px; color: #64748b; line-height: 1.55; margin-bottom: 20px;
}

/* ---- Store badges ---- */
.dh-store-badges { display: flex; gap: 10px; flex-wrap: wrap; }
.dh-store-badge {
    display: flex; align-items: center; gap: 10px;
    background: #0f172a; border-radius: 12px;
    padding: 10px 16px; color: #fff; cursor: pointer;
    transition: background .18s, transform .18s, box-shadow .18s;
    min-width: 130px;
}
.dh-store-badge:hover {
    background: #1e293b; transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.2);
}
.dh-store-sub  { font-size: 9px; color: rgba(255,255,255,.65); letter-spacing: .3px; }
.dh-store-name { font-size: 15px; font-weight: 800; color: #fff; }

/* ---- Phone mockup ---- */
.dh-phone-mockup {
    flex-shrink: 0;
    transform: rotate(5deg) translateY(-10px);
    filter: drop-shadow(0 20px 40px rgba(0,0,0,.22));
}
.dh-phone-frame {
    width: 130px; height: 240px;
    background: #111827;
    border-radius: 28px;
    padding: 10px 7px;
    position: relative;
    border: 2px solid #374151;
}
.dh-phone-notch {
    position: absolute; top: 10px; left: 50%; transform: translateX(-50%);
    width: 40px; height: 8px;
    background: #111827; border-radius: 99px; z-index: 10;
}
.dh-phone-screen {
    width: 100%; height: 100%;
    border-radius: 20px; overflow: hidden;
    background: #eff6ff;
    display: flex; flex-direction: column;
}
.dh-phone-status {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 10px 4px;
    font-size: 9px; font-weight: 700; color: #0f172a;
    background: #fff;
}
.dh-phone-icons { display: flex; gap: 3px; }
.dh-phone-icons svg { color: #0f172a; }
.dh-phone-app-header {
    background: #0064D2;
    padding: 10px 10px 14px;
}
.dh-phone-app-title {
    font-size: 10px; font-weight: 800; color: #fff; line-height: 1.4;
}
.dh-phone-hotel-card {
    margin: -8px 8px 0;
    background: #fff; border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
    flex: 1;
}
.dh-phone-hotel-img {
    width: 100%; height: 80px; object-fit: cover; display: block;
}
.dh-phone-hotel-info {
    display: flex; align-items: center; justify-content: space-between;
    padding: 6px 8px;
}
.dh-phone-hotel-name { font-size: 8px; font-weight: 700; color: #0f172a; line-height: 1.3; }
.dh-phone-hotel-price { font-size: 9px; font-weight: 800; color: #0064D2; }

/* ---- Responsive ---- */
@media (max-width: 1024px) {
    .dh-cards-grid  { grid-template-columns: repeat(2, 1fr); }
    .dh-how-steps   { flex-wrap: wrap; gap: 24px; }
    .dh-how-arrow   { display: none; }
    .dh-how-step    { max-width: 200px; flex: 0 0 auto; }
    .dh-trust-row   { flex-wrap: wrap; }
    .dh-trust-item  { flex: 0 0 calc(50% - 1px); }
    .dh-trust-divider:nth-child(4) { display: none; }
}
@media (max-width: 768px) {
    .dh-hero-title   { font-size: 28px; }
    .dh-coupon-card  { display: none; }
    .dh-cards-grid   { grid-template-columns: 1fr 1fr; gap: 14px; }
    .dh-bottom-grid  { grid-template-columns: 1fr; gap: 20px; }
    .dh-how-steps    { justify-content: center; }
    .dh-phone-mockup { display: none; }
    .dh-trust-item   { padding: 20px 16px; }
}
@media (max-width: 480px) {
    .dh-hero-inner   { flex-direction: column; align-items: flex-start; }
    .dh-cards-grid   { grid-template-columns: 1fr; }
    .dh-trust-row    { flex-direction: column; }
    .dh-trust-item   { flex: 1 1 100%; }
    .dh-trust-divider { width: 100%; height: 1px; }
    .dh-bottom-card  { padding: 24px 18px; }
    .dh-nl-row       { flex-direction: column; }
    .dh-nl-btn       { width: 100%; }
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
