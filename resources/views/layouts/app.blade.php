<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', 'StayGo – Đặt phòng khách sạn giá tốt tại Đà Lạt, Nha Trang, Vũng Tàu và Đà Nẵng. Hàng trăm khách sạn, giá tốt nhất, đặt phòng dễ dàng.')">
    <meta name="keywords"    content="@yield('meta_keywords', 'đặt phòng khách sạn, khách sạn Đà Lạt, khách sạn Nha Trang, khách sạn Vũng Tàu, khách sạn Đà Nẵng, StayGo')">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="StayGo">
    <meta property="og:title"       content="@yield('og_title', 'StayGo – Đặt phòng khách sạn')">
    <meta property="og:description" content="@yield('og_description', 'Đặt phòng khách sạn giá tốt tại Đà Lạt, Nha Trang, Vũng Tàu & Đà Nẵng. Hàng trăm lựa chọn, giá tốt nhất.')">
    <meta property="og:image"       content="@yield('og_image', asset('assets/images/StayGo.png'))">
    <meta property="og:url"         content="{{ url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', 'StayGo – Đặt phòng khách sạn')">
    <meta name="twitter:description" content="@yield('og_description', 'Đặt phòng khách sạn giá tốt tại Đà Lạt, Nha Trang, Vũng Tàu & Đà Nẵng.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('assets/images/StayGo.png'))">

    {{-- SgDark apply — chỉ áp dụng dark khi user tự chọn, không tự detect hệ thống --}}
    <script>
    (function() {
        if (localStorage.getItem('sg-theme') === 'dark') {
            document.documentElement.classList.add('sg-dark');
        }
    })();
    </script>

    <title>@yield('title', 'StayGo') - StayGo</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/StayGo.png') }}">

    {{-- Font: Inter + Be Vietnam Pro — async (non-blocking) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" media="print" onload="this.media='all'" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap"></noscript>

    {{-- Preload assets --}}
    @yield('preload_assets')

    {{-- CSS: base → design → luxury → staygo-theme → sg-system (highest) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/staygo-design.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/luxury-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/staygo-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sg-system.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">

{{-- Top bar --}}
<div class="sg-topbar">
    <div class="sg-topbar-inner">
        <div class="sg-topbar-left">
            <span class="sg-topbar-item">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                supportstaygo@gmail.com
            </span>
            <span class="sg-topbar-item">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 11.5 19.79 19.79 0 01.07 2.86 2 2 0 012.06 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91A16 16 0 0015.1 17.9l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                +84 373 848 395
            </span>
        </div>
        <div class="sg-topbar-right">
            <a href="#" class="sg-topbar-social" aria-label="Facebook">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
            <a href="#" class="sg-topbar-social" aria-label="Instagram">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="#" class="sg-topbar-social" aria-label="Twitter">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
            </a>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     HEADER / NAV
     ════════════════════════════════════════ --}}
<header class="header @yield('header_class')" id="siteHeader">
    <div class="header-flex">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="logo-link">
            <img src="{{ asset('assets/images/StayGo-nobg.png') }}" alt="StayGo" class="logo-img" width="140" height="40" fetchpriority="high">
        </a>

        {{-- Desktop Nav --}}
        <nav id="mainNav" class="nav-left d-none-mobile">
            <a href="{{ route('home') }}"                                           class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ route('hotels.index', ['type' => 'hotel']) }}"             class="{{ request()->routeIs('hotels.*') && request('type') === 'hotel' ? 'active' : '' }}">Khách sạn</a>
            <a href="{{ route('hotels.index', ['type' => 'homestay-resort']) }}"   class="{{ request()->routeIs('hotels.*') && request('type') === 'homestay-resort' ? 'active' : '' }}">Resort</a>
            <a href="{{ route('deals.index') }}"                                   class="{{ request()->routeIs('deals.*') ? 'active' : '' }}">Ưu đãi</a>
            <a href="{{ route('blog.index') }}"                                    class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Cẩm nang</a>
            <a href="{{ route('support.create') }}"                                class="{{ request()->routeIs('support.*') ? 'active' : '' }}">Liên hệ</a>
        </nav>

        {{-- Right side --}}
        <div class="header-right">

            {{-- Auth block --}}
            @auth
                @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-trigger" onclick="toggleDropdown()">
                        <span class="user-name">{{ auth()->user()->full_name }}</span>
                        @if($unreadCount > 0)
                            <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                        <svg class="chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"/>
                        </svg>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-header">
                            <div class="dh-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                            <div class="dh-info">
                                <div class="dh-name">{{ auth()->user()->full_name }}</div>
                                <div class="dh-role">{{ auth()->user()->role }}</div>
                            </div>
                        </div>
                        <div class="dropdown-items">
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <span class="item-icon">👤</span> Thông tin tài khoản
                            </a>
                            <a href="{{ route('booking.my') }}" class="dropdown-item">
                                <span class="item-icon">📋</span> Lịch sử đặt phòng
                            </a>
                            <a href="{{ route('favorite.index') }}" class="dropdown-item">
                                <span class="item-icon">❤️</span> Yêu thích
                            </a>
                            @php $recentNotifs = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                            @if($recentNotifs->isNotEmpty())
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-notif-header">
                                    <span>🔔 Thông báo</span>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="notif-read-all">Đọc tất cả</button>
                                        </form>
                                    @endif
                                </div>
                                @foreach($recentNotifs as $notif)
                                    <a href="{{ $notif->data['url'] ?? '#' }}" class="dropdown-item notif-item {{ $notif->read_at ? '' : 'notif-unread' }}">
                                        <span class="item-icon">{{ ($notif->data['type'] ?? '') === 'booking_cancelled' ? '❌' : '✅' }}</span>
                                        <span class="notif-text">{{ $notif->data['message'] }}</span>
                                    </a>
                                @endforeach
                            @endif
                            @if(auth()->user()->isAdmin())
                                <div class="dropdown-divider"></div>
                                <a href="/admin" class="dropdown-item">
                                    <span class="item-icon">⚙️</span> Admin Panel
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger" style="border:none;background:none;width:100%;text-align:left;cursor:pointer;">
                                    <span class="item-icon">🚪</span> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-login-nav">Đăng nhập</a>
            @endauth

            {{-- Dark mode toggle --}}
            <button data-sg-dark-toggle title="Đổi giao diện"
                style="background:#f1f5f9;border:none;border-radius:4px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#374151;flex-shrink:0;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

{{-- Hamburger --}}
            <button class="nav-hamburger" id="navHamburger" onclick="toggleMobileMenu()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

        </div>{{-- /.header-right --}}
    </div>{{-- /.header-flex --}}
</header>

{{-- Spacer cho trang không có hero fullscreen --}}
@hasSection('header_offset')
    @yield('header_offset')
@else
    <div class="header-spacer"></div>
@endif

{{-- Flash messages --}}
@if(session('success') && !is_bool(session('success')))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- Page content --}}
@yield('content')

@include('components.chatbot')

{{-- ════════════════════════════════════════
     FOOTER
     ════════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-container">

            {{-- Cột 1: Brand --}}
            <div>
                <div class="footer-logo">
                    <a href="{{ route('home') }}" class="footer-logo-link">
                        <img src="{{ asset('assets/images/StayGo-nobg.png') }}" alt="StayGo" class="footer-logo-img" loading="lazy" width="120" height="40">
                    </a>
                </div>
                <p class="footer-desc">Nền tảng đặt phòng khách sạn và resort hàng đầu tại Đà Lạt, Nha Trang, Vũng Tàu & Đà Nẵng. Giá tốt nhất, trải nghiệm thượng hạng.</p>
            </div>

            {{-- Cột 2: Khám phá --}}
            <div>
                <span class="footer-heading">Khám phá</span>
                <ul class="footer-links">
                    <li><a href="{{ route('hotels.index', ['location' => 1]) }}">Khách sạn Đà Lạt</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 2]) }}">Khách sạn Nha Trang</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 3]) }}">Khách sạn Vũng Tàu</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 4]) }}">Khách sạn Đà Nẵng</a></li>
                    <li><a href="{{ route('blog.index') }}">Cẩm nang du lịch</a></li>
                </ul>
            </div>

            {{-- Cột 3: Ưu đãi & Dịch vụ --}}
            <div>
                <span class="footer-heading">Ưu đãi</span>
                <ul class="footer-links">
                    <li><a href="{{ route('deals.index') }}">Ưu đãi đặc biệt</a></li>
                    <li><a href="{{ route('hotels.index') }}">Đặt phòng sớm</a></li>
                    @auth
                        <li><a href="{{ route('booking.my') }}">Lịch sử đặt phòng</a></li>
                        <li><a href="{{ route('favorite.index') }}">Danh sách yêu thích</a></li>
                    @else
                        <li><a href="{{ route('register') }}">Đăng ký thành viên</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Cột 4: Liên hệ --}}
            <div>
                <span class="footer-heading">Liên hệ</span>
                <ul class="footer-contact">
                    <li>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        146 Nguyễn Văn Cừ, TP. Kon Tum
                    </li>
                    <li>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.22 1.18 2 2 0 012.18 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.72 6.72l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                        <a href="tel:0373848395">037 384 8395</a>
                    </li>
                    <li>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:supportstaygo@gmail.com">supportstaygo@gmail.com</a>
                    </li>
                </ul>
                <div class="footer-payment">
                    <div class="fp-label">Thanh toán</div>
                    <div class="fp-icons">
                        <div class="fp-badge-logo" title="VNPay"><img src="{{ asset('assets/images/vnpay.png') }}" alt="VNPay" loading="lazy"></div>
                        <div class="fp-badge-logo" title="MoMo"><img src="{{ asset('assets/images/momo.png') }}" alt="MoMo" loading="lazy"></div>
                        <div class="fp-badge-logo" title="Visa/Mastercard"><img src="{{ asset('assets/images/thanh_toanqt.png') }}" alt="Thẻ quốc tế" loading="lazy"></div>
                        <div class="fp-badge-logo" title="Chuyển khoản ngân hàng">
                            <svg width="36" height="28" viewBox="0 0 72 48" fill="none">
                                <rect x="1" y="1" width="70" height="46" rx="5" fill="#1a56db"/>
                                <rect x="1" y="13" width="70" height="11" fill="#1338be"/>
                                <rect x="8" y="30" width="22" height="4" rx="2" fill="rgba(255,255,255,0.6)"/>
                                <rect x="8" y="36" width="14" height="4" rx="2" fill="rgba(255,255,255,0.4)"/>
                                <circle cx="54" cy="34" r="7" fill="#ef4444" opacity="0.9"/>
                                <circle cx="62" cy="34" r="7" fill="#f59e0b" opacity="0.9"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom">
        <div>
            <span>© {{ date('Y') }} StayGo. Bảo lưu mọi quyền.</span>
            <span>Thiết kế bởi StayGo Team</span>
        </div>
    </div>
</footer>

{{-- Scroll to top --}}
<button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Lên đầu trang">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
</button>

{{-- Support Popup --}}
<div class="support-popup-overlay" id="supportOverlay" onclick="closeSupportPopupOutside(event)">
    <div class="support-popup">
        <button class="sp-close" onclick="closeSupportPopup()">✕</button>
        <h3>Tư vấn miễn phí</h3>
        <p>Để lại thông tin, chúng tôi sẽ liên hệ trong vòng 15 phút!</p>
        <form id="supportForm" onsubmit="submitSupport(event)">
            @csrf
            <div style="display:none;position:absolute;left:-9999px" aria-hidden="true">
                <input type="text" name="_hp_name" tabindex="-1" autocomplete="off">
            </div>
            <label>Họ và tên *</label>
            <input type="text" name="full_name" autocomplete="name" placeholder="Nhập họ tên của bạn" required>
            <label>Số điện thoại *</label>
            <input type="tel" name="phone" autocomplete="tel" placeholder="Nhập số điện thoại" required>
            <label>Ghi chú</label>
            <textarea name="note" placeholder="Bạn cần tư vấn về điều gì?"></textarea>
            <button type="submit" class="sp-submit">Gửi yêu cầu tư vấn</button>
        </form>
        <div class="sp-msg" id="spMsg"></div>
    </div>
</div>

{{-- Mobile Drawer --}}
<div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="toggleMobileMenu()"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <a href="{{ route('home') }}" class="mobile-drawer-logo-link">
            <img src="{{ asset('assets/images/StayGo-nobg.png') }}" alt="StayGo" class="mobile-drawer-logo-img">
        </a>
        <button onclick="toggleMobileMenu()" class="mobile-drawer-close">✕</button>
    </div>
    <nav class="mobile-drawer-nav">
        <a href="{{ route('home') }}"                                          class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
        <a href="{{ route('hotels.index', ['type' => 'hotel']) }}"            class="{{ request()->routeIs('hotels.*') && request('type') === 'hotel' ? 'active' : '' }}">Khách sạn</a>
        <a href="{{ route('hotels.index', ['type' => 'homestay-resort']) }}"  class="{{ request()->routeIs('hotels.*') && request('type') === 'homestay-resort' ? 'active' : '' }}">Resort</a>
        <a href="{{ route('deals.index') }}"                                  class="{{ request()->routeIs('deals.*') ? 'active' : '' }}">Ưu đãi</a>
        <a href="{{ route('blog.index') }}"                                   class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Cẩm nang</a>
        <a href="{{ route('support.create') }}">Liên hệ</a>
    </nav>
    @auth
        <div class="mobile-drawer-footer">
            <a href="{{ route('profile.show') }}">Tài khoản</a>
            <a href="{{ route('booking.my') }}">Đặt phòng của tôi</a>
        </div>
    @else
        <div class="mobile-drawer-footer">
            <a href="{{ route('login') }}" class="mobile-drawer-login">Đăng nhập</a>
        </div>
    @endauth
</div>

{{-- ════════════════════════════════════════
     SCRIPTS
     ════════════════════════════════════════ --}}
<script>
// Scroll to Top
window.addEventListener('scroll', function () {
    document.getElementById('scrollTopBtn').classList.toggle('visible', window.scrollY > 300);
});

// Header transparent → scrolled
(function() {
    const header = document.getElementById('siteHeader');
    if (!header || !header.classList.contains('header-transparent')) return;
    function onScroll() {
        header.classList.toggle('scrolled', window.scrollY > 60);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();

// Support Popup
function openSupportPopup() {
    document.getElementById('supportOverlay').classList.add('open');
}
function closeSupportPopup() {
    document.getElementById('supportOverlay').classList.remove('open');
}
function closeSupportPopupOutside(e) {
    if (e.target === document.getElementById('supportOverlay')) closeSupportPopup();
}

async function submitSupport(e) {
    e.preventDefault();
    const form = document.getElementById('supportForm');
    const msg  = document.getElementById('spMsg');
    const btn  = form.querySelector('.sp-submit');
    btn.disabled = true;
    btn.textContent = 'Đang gửi...';
    try {
        const res  = await fetch('{{ route("support.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: new FormData(form),
        });
        const data = await res.json();
        msg.style.display = 'block';
        msg.className = 'sp-msg ' + (data.success ? 'success' : 'error');
        msg.textContent = data.message ?? (data.success ? 'Gửi thành công!' : 'Có lỗi xảy ra!');
        if (data.success) { form.reset(); setTimeout(closeSupportPopup, 2500); }
    } catch {
        msg.style.display = 'block';
        msg.className = 'sp-msg error';
        msg.textContent = 'Có lỗi kết nối, vui lòng thử lại!';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Gửi yêu cầu tư vấn';
    }
}
</script>

<script>
// Mobile nav toggle
function toggleMobileNav() {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('navHamburger');
    nav.classList.toggle('nav-open');
    btn.classList.toggle('active');
}
function closeMobileNav() {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('navHamburger');
    if (nav) nav.classList.remove('nav-open');
    if (btn) btn.classList.remove('active');
}
document.addEventListener('click', function(e) {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('navHamburger');
    if (!nav || !nav.classList.contains('nav-open')) return;
    if (e.target === nav || (!nav.contains(e.target) && !btn.contains(e.target))) closeMobileNav();
});
</script>

<script>
// Mobile drawer
function toggleMobileMenu() {
    document.getElementById('mobileDrawer').classList.toggle('open');
    document.getElementById('mobileDrawerOverlay').classList.toggle('open');
    document.body.classList.toggle('drawer-open');
}
</script>

<script>
// User dropdown
function toggleDropdown() {
    const dd = document.getElementById('userDropdown');
    dd.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('userDropdown');
    if (dd && !dd.contains(e.target)) dd.classList.remove('open');
});
</script>

<script>
// Favorite toggle (AJAX)
async function toggleFav(btn, e) {
    e.preventDefault();
    e.stopPropagation();
    const url   = btn.dataset.url;
    const token = document.querySelector('meta[name="csrf-token"]').content;
    btn.style.opacity = '.5';
    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } });
        const data = await res.json();
        btn.classList.toggle('is-fav', data.favorited);
        const path = btn.querySelector('svg path');
        if (path) path.setAttribute('fill', data.favorited ? 'currentColor' : 'none');
        btn.title = data.favorited ? 'Xóa yêu thích' : 'Thêm yêu thích';
    } catch {}
    btn.style.opacity = '1';
}
</script>

<script src="{{ asset('assets/js/main.js') }}" defer></script>

<script>
// SgDark toggle (DOMContentLoaded — chỉ toggle, không re-apply)
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.querySelector('[data-sg-dark-toggle]');
    if (!toggle) return;
    toggle.addEventListener('click', function() {
        var html = document.documentElement;
        var isDark = html.classList.contains('sg-dark');
        if (isDark) {
            html.classList.remove('sg-dark');
            localStorage.setItem('sg-theme', 'light');
        } else {
            html.classList.add('sg-dark');
            localStorage.setItem('sg-theme', 'dark');
        }
    });
});
</script>

@stack('scripts')

</body>
</html>
