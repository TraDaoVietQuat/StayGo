<!DOCTYPE html>
<html lang="vi" class="no-js">
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

    <script>document.documentElement.classList.replace('no-js','js')</script>
    <title>@yield('title', 'StayGo') - StayGo</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/StayGo.png') }}">
    {{-- Preload font DNS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" media="print" onload="this.media='all'">
    {{-- Load Google Fonts async (không block render) --}}
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    </noscript>
    {{-- Preload hero image (chỉ trang chủ) --}}
    @yield('preload_assets')

    {{-- CSS chính --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/staygo-design.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')" >

<header class="header @yield('header_class')" id="siteHeader">
    <div class="container header-flex">

        {{-- Logo trái --}}
        <a href="{{ route('home') }}" class="logo-link logo-left">
            <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" class="logo-img">
        </a>

        {{-- Nav trái --}}
        <nav id="mainNav" class="nav-left d-none-mobile">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ route('hotels.index', ['type' => 'hotel']) }}" class="{{ request()->routeIs('hotels.*') && request('type') === 'hotel' ? 'active' : '' }}">Khách sạn</a>
            <a href="{{ route('hotels.index', ['type' => 'homestay-resort']) }}" class="{{ request()->routeIs('hotels.*') && request('type') === 'homestay-resort' ? 'active' : '' }}">Khách sạn &amp; Resort</a>
            <a href="{{ route('deals.index') }}" class="{{ request()->routeIs('deals.*') ? 'active' : '' }}">Ưu đãi</a>
            <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Cẩm nang</a>
        </nav>

        <div class="header-right">

{{-- Auth block --}}
        @auth
            @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
            <div class="user-dropdown" id="userDropdown">
                <div class="user-trigger" onclick="toggleDropdown()">
                    <span class="user-greet">Xin chào,&nbsp;</span><span class="user-name">{{ auth()->user()->full_name }}</span>
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
                            <span class="item-icon">{{ $notif->data['type'] === 'booking_cancelled' ? '❌' : '✅' }}</span>
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
                                <span class="item-icon danger-icon">🚪</span> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-login-nav">Đăng nhập</a>
        @endauth

        {{-- Hamburger mobile menu --}}
        <button class="mobile-menu-btn d-none-desktop" onclick="toggleMobileMenu()" aria-label="Menu">
            <svg width="22" height="18" viewBox="0 0 22 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <line x1="0" y1="1" x2="22" y2="1"/>
                <line x1="0" y1="9" x2="22" y2="9"/>
                <line x1="0" y1="17" x2="22" y2="17"/>
            </svg>
        </button>

        {{-- Nút Liên hệ — nằm ngoài nav, cạnh phải header --}}
        <div class="nav-contact-wrap d-none-mobile" id="navContactWrap">
            <button class="nav-contact-btn" onclick="toggleNavContact()" aria-label="Liên hệ">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="0" y1="1"  x2="16" y2="1"/>
                    <line x1="0" y1="6.5" x2="16" y2="6.5"/>
                    <line x1="0" y1="12" x2="16" y2="12"/>
                </svg>
                <span class="nav-contact-label">Liên hệ</span>
            </button>
            <div class="nav-contact-dropdown" id="navContactDropdown">
                <div class="ncd-title">Liên hệ với chúng tôi</div>
                <a href="tel:0373848395" class="ncd-item ncd-call">
                    <span class="ncd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.22 1.18 2 2 0 012.18 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.72 6.72l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg></span>
                    <div><div class="ncd-label">Gọi điện</div><div class="ncd-val">037 384 8395</div></div>
                </a>
                <a href="https://zalo.me/0373848395" target="_blank" class="ncd-item ncd-zalo">
                    <span class="ncd-icon ncd-icon-zalo"><svg width="18" height="18" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="8" fill="#0068FF"/><text x="20" y="27" text-anchor="middle" font-size="16" font-weight="bold" fill="white" font-family="Arial">Z</text></svg></span>
                    <div><div class="ncd-label">Chat Zalo</div><div class="ncd-val">037 384 8395</div></div>
                </a>
                <a href="https://wa.me/84373848395" target="_blank" class="ncd-item ncd-whatsapp">
                    <span class="ncd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></span>
                    <div><div class="ncd-label">WhatsApp</div><div class="ncd-val">+84 373 848 395</div></div>
                </a>
                <a href="https://facebook.com" target="_blank" class="ncd-item ncd-fb">
                    <span class="ncd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
                    <div><div class="ncd-label">Facebook</div><div class="ncd-val">StayGo Official</div></div>
                </a>
                <button class="ncd-item ncd-consult" onclick="toggleNavContact();openSupportPopup()">
                    <span class="ncd-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span>
                    <div><div class="ncd-label">Tư vấn miễn phí</div><div class="ncd-val">Phản hồi trong 15 phút</div></div>
                </button>
                <div class="ncd-divider"></div>
                <a href="mailto:support@staygo.vn" class="ncd-email">support@staygo.vn</a>
            </div>
        </div>

        {{-- Hamburger: nằm trong header-right để chỉ còn 2 flex item: logo + header-right --}}
        <button class="nav-hamburger" id="navHamburger" onclick="toggleMobileNav()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
        </div>{{-- /.header-right --}}

    </div>{{-- /.header-flex --}}
</header>

@if(session('success') && !is_bool(session('success')))
<div class="alert alert-success" style="background:#d4edda;color:#155724;padding:12px 20px;text-align:center;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error" style="background:#f8d7da;color:#721c24;padding:12px 20px;text-align:center;">
    {{ session('error') }}
</div>
@endif

@yield('content')

@include('components.chatbot')

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-container">
            {{-- Cột 1: Về StayGo --}}
            <div>
                <div class="footer-logo">
                    <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:44px;object-fit:contain;">
                </div>
                <p class="footer-desc">Nền tảng đặt phòng khách sạn hàng đầu tại Đà Lạt, Nha Trang, Vũng Tàu & Đà Nẵng. Giá tốt nhất, đặt phòng dễ dàng, trải nghiệm tuyệt vời.</p>
                <div class="footer-socials">
                    <a href="#" class="fsocial fsocial-fb" title="Facebook">f</a>
                    <a href="#" class="fsocial fsocial-zalo" title="Zalo">Z</a>
                    <a href="#" class="fsocial fsocial-ig" title="Instagram">in</a>
                    <a href="#" class="fsocial fsocial-yt" title="YouTube">▶</a>
                </div>
            </div>

            {{-- Cột 2: Khám phá --}}
            <div>
                <a href="{{ route('hotels.index') }}" class="footer-heading">Khám phá</a>
                <ul class="footer-links">
                    <li><a href="{{ route('hotels.index', ['location' => 1]) }}">Khách sạn Đà Lạt</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 2]) }}">Khách sạn Nha Trang</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 3]) }}">Khách sạn Vũng Tàu</a></li>
                    <li><a href="{{ route('hotels.index', ['location' => 4]) }}">Khách sạn Đà Nẵng</a></li>
                    <li><a href="{{ route('hotels.index') }}">Tất cả khách sạn</a></li>
                    <li><a href="{{ route('blog.index') }}">Cẩm nang du lịch</a></li>
                </ul>
            </div>

            {{-- Cột 3: Hỗ trợ --}}
            <div>
                <a href="{{ route('support.create') }}" class="footer-heading">Hỗ trợ</a>
                <ul class="footer-links">
                    <li><a href="{{ route('support.create') }}">Liên hệ chúng tôi</a></li>
                    @auth
                    <li><a href="{{ route('booking.my') }}">Đặt phòng của tôi</a></li>
                    <li><a href="{{ route('profile.show') }}">Tài khoản</a></li>
                    @else
                    <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                    <li><a href="{{ route('register') }}">Đăng ký</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Cột 4: Liên hệ & Thanh toán --}}
            <div>
                <a href="{{ route('support.create') }}" class="footer-heading">Liên hệ</a>
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
                        <a href="mailto:support@staygo.vn">support@staygo.vn</a>
                    </li>
                </ul>
                <div class="footer-payment">
                    <div class="fp-label">Phương thức thanh toán</div>
                    <div class="fp-icons">
                        <div class="fp-badge-logo" data-tip="VNPay"><img src="{{ asset('assets/images/vnpay.png') }}" alt="VNPay" loading="lazy"></div>
                        <div class="fp-badge-logo" data-tip="Ví MoMo"><img src="{{ asset('assets/images/momo.png') }}" alt="MoMo" loading="lazy"></div>
                        <div class="fp-badge-logo" data-tip="Thẻ quốc tế (Visa/Mastercard)"><img src="{{ asset('assets/images/thanh_toanqt.png') }}" alt="Thẻ quốc tế" loading="lazy"></div>
                        <div class="fp-badge-logo" data-tip="Thanh toán chuyển khoản">
                            <svg width="36" height="28" viewBox="0 0 56 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                {{-- Thẻ phía sau --}}
                                <rect x="14" y="2" width="38" height="24" rx="3" stroke="#1a202c" stroke-width="2" fill="white" transform="rotate(8 14 2)"/>
                                {{-- Tờ tiền phía trước --}}
                                <rect x="2" y="14" width="44" height="24" rx="3" stroke="#1a202c" stroke-width="2" fill="white"/>
                                {{-- Đường kẻ trên tờ tiền --}}
                                <line x1="2" y1="20" x2="46" y2="20" stroke="#1a202c" stroke-width="1.5"/>
                                <line x1="2" y1="32" x2="46" y2="32" stroke="#1a202c" stroke-width="1.5"/>
                                {{-- Vòng tròn dollar --}}
                                <circle cx="24" cy="26" r="5.5" stroke="#1a202c" stroke-width="2" fill="white"/>
                                <text x="24" y="29.5" text-anchor="middle" font-size="6" font-weight="bold" fill="#1a202c" font-family="Arial">$</text>
                            </svg>
                        </div>
                        <div class="fp-badge-logo" data-tip="Thanh toán tại khách sạn">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="24" height="24" rx="5" fill="#e0f2fe"/>
                                <path d="M12 3L3 7v1h18V7L12 3z" fill="#0369a1"/>
                                <rect x="4" y="9" width="2.5" height="8" rx=".5" fill="#0369a1"/>
                                <rect x="7.5" y="9" width="2.5" height="8" rx=".5" fill="#0369a1"/>
                                <rect x="11" y="9" width="2.5" height="8" rx=".5" fill="#0369a1"/>
                                <rect x="14.5" y="9" width="2.5" height="8" rx=".5" fill="#0369a1"/>
                                <rect x="18" y="9" width="2.5" height="8" rx=".5" fill="#0369a1"/>
                                <rect x="3" y="17.5" width="18" height="2" rx=".5" fill="#0369a1"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-container" style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;color:#1a202c;">© {{ date('Y') }} StayGo. Tất cả quyền được bảo lưu.</span>
            <span style="font-size:12px;color:#4a5568;">Thiết kế bởi StayGo Team</span>
        </div>
    </div>
</footer>


{{-- SCROLL TO TOP --}}
<button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Lên đầu trang">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
</button>

{{-- POPUP TƯ VẤN NHANH --}}
<div class="support-popup-overlay" id="supportOverlay" onclick="closeSupportPopupOutside(event)">
    <div class="support-popup">
        <button class="sp-close" onclick="closeSupportPopup()">✕</button>
        <h3>🎧 Tư vấn miễn phí</h3>
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

<script>
// --- Scroll to Top ---
window.addEventListener('scroll', function () {
    document.getElementById('scrollTopBtn').classList.toggle('visible', window.scrollY > 300);
});

// --- Header transparent → scrolled khi qua hero ---
(function() {
    const header = document.getElementById('siteHeader');
    if (!header || !header.classList.contains('header-transparent')) return;
    function onScroll() {
        header.classList.toggle('scrolled', window.scrollY > 60);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();

// --- Nav Contact Dropdown Toggle ---
function toggleNavContact() {
    const wrap = document.getElementById('navContactWrap');
    wrap.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('navContactWrap');
    if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
});

// --- Support Popup ---
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
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const data = await res.json();
        msg.style.display = 'block';
        msg.className = 'sp-msg ' + (data.success ? 'success' : 'error');
        msg.textContent = data.message ?? (data.success ? 'Gửi thành công!' : 'Có lỗi xảy ra!');
        if (data.success) {
            form.reset();
            setTimeout(closeSupportPopup, 2500);
        }
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
// Close when clicking outside (or on the overlay pseudo-element)
document.addEventListener('click', function(e) {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('navHamburger');
    if (!nav || !nav.classList.contains('nav-open')) return;
    // Click on overlay (e.target === nav itself) or outside both nav & btn
    if (e.target === nav || (!nav.contains(e.target) && !btn.contains(e.target))) {
        closeMobileNav();
    }
});
</script>

<script>
// User dropdown toggle
function toggleDropdown() {
    const dd = document.getElementById('userDropdown');
    dd.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('userDropdown');
    if (dd && !dd.contains(e.target)) dd.classList.remove('open');
});
</script>
<script src="{{ asset('assets/js/main.js') }}" defer></script>
<script src="{{ asset('assets/js/payment.js') }}" defer></script>

<script>
// Favorite toggle (AJAX)
async function toggleFav(btn, e) {
    e.preventDefault();
    e.stopPropagation();

    const url   = btn.dataset.url;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    btn.style.opacity = '.5';
    try {
        const res  = await fetch(url, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        });
        const data = await res.json();
        btn.classList.toggle('is-fav', data.favorited);
        const path = btn.querySelector('svg path');
        if (path) path.setAttribute('fill', data.favorited ? 'currentColor' : 'none');
        btn.title = data.favorited ? 'Xóa yêu thích' : 'Thêm yêu thích';
    } catch {}
    btn.style.opacity = '1';
}
</script>

@stack('scripts')

{{-- Mobile Navigation Drawer --}}
<div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="toggleMobileMenu()"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:36px;">
        <button onclick="toggleMobileMenu()" class="mobile-drawer-close">✕</button>
    </div>
    <nav class="mobile-drawer-nav">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">🏠 Trang chủ</a>
        <a href="{{ route('hotels.index', ['type' => 'hotel']) }}" class="{{ request()->routeIs('hotels.*') && request('type') === 'hotel' ? 'active' : '' }}">🏨 Khách sạn</a>
        <a href="{{ route('hotels.index', ['type' => 'homestay-resort']) }}" class="{{ request()->routeIs('hotels.*') && request('type') === 'homestay-resort' ? 'active' : '' }}">🏨 Khách sạn &amp; Resort</a>
        <a href="{{ route('deals.index') }}" class="{{ request()->routeIs('deals.*') ? 'active' : '' }}">🎁 Ưu đãi</a>
        <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">📖 Cẩm nang</a>
        <a href="{{ route('support.create') }}">💬 Liên hệ</a>
    </nav>
    @auth
    <div class="mobile-drawer-footer">
        <a href="{{ route('profile.show') }}">👤 Tài khoản</a>
        <a href="{{ route('booking.my') }}">📋 Đặt phòng của tôi</a>
    </div>
    @else
    <div class="mobile-drawer-footer">
        <a href="{{ route('login') }}" class="mobile-drawer-login">Đăng nhập</a>
    </div>
    @endauth
</div>

<script>
function toggleMobileMenu() {
    document.getElementById('mobileDrawer').classList.toggle('open');
    document.getElementById('mobileDrawerOverlay').classList.toggle('open');
    document.body.classList.toggle('drawer-open');
}
</script>
</body>
</html>
