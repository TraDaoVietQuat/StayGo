# MOBILE UI — STAYGO TRAVELOKA STYLE
> Dự án: Laravel Blade + **Plain CSS** (không dùng SCSS/Sass)
> JS: **Vanilla JavaScript** (không dùng Alpine.js — project không có)
> Breakpoints: 360px / 480px / 768px
> Test devices: Samsung Galaxy S8 (360px), iPhone 12 (390px), iPad Mini (768px)

---

## THỰC TẾ CODEBASE — ĐỌC TRƯỚC KHI LÀM

| Hạng mục | Thực tế |
|----------|---------|
| CSS system | Plain `.css` files, không có Sass/SCSS build pipeline |
| JS framework | Vanilla JS (`toggleMobileNav()`, `toggleMobileMenu()` etc.) |
| CSS load order | `style.css` → `staygo-design.css` → `luxury-theme.css` → `staygo-theme.css` → `sg-system.css` |
| Biến CSS | `--sg-*` trong `:root` của `sg-system.css` (39 biến) |
| Header class | `.header` (70px, sticky), `.header-flex` |
| Drawer class | `.mobile-drawer`, `.mobile-drawer-overlay` (toggle bằng `toggleMobileMenu()`) |
| Hamburger | `.nav-hamburger` + `onclick="toggleMobileNav()"` |
| Auth layout | **KHÔNG tồn tại** — login/register đang `@extends('layouts.app')` |
| Alpine.js | **KHÔNG có** — đừng dùng `x-data`, `x-show`, `:class` |
| Body class | `@yield('body_class')` |
| Dark mode | `html.sg-dark` class toggle |
| Primary color hiện tại | `#004391` (login btn), `--sg-brand: #0066cc` |
| Background đã fix | Toàn hệ thống `#fff`, footer `#f8f9fa` |

---

## PHẦN 0 — CSS CUSTOM PROPERTIES (MỞ RỘNG sg-system.css)

**File:** `public/assets/css/sg-system.css`

Thêm vào block `:root` **hiện có** (KHÔNG tạo file mới, KHÔNG tạo `:root` thứ hai).
Chèn sau dòng `--sg-btn-blue-dark: ...;`:

```css
/* ── Mobile Traveloka tokens ── */
--mob-blue          : #004391;       /* CTA chính — đồng bộ với login btn hiện tại */
--mob-blue-hover    : #003070;       /* hover state */
--mob-blue-light    : #e8f0fc;       /* chip bg, badge bg, tab indicator */
--mob-amber         : #F5A623;       /* rating star, deal highlight */
--mob-green         : #00A650;       /* giá KM, badge "Tiết kiệm" */
--mob-red           : #E63946;       /* hết phòng, notification dot */
--mob-bg-page       : #f5f7fa;       /* page background mobile (xám nhạt) */
--mob-border        : #e5e7eb;       /* border card, input */
--mob-shadow-card   : 0 1px 4px rgba(0,0,0,0.08);
--mob-shadow-sticky : 0 -2px 8px rgba(0,0,0,0.08);
--mob-shadow-header : 0 2px 8px rgba(0,0,0,0.10);
--mob-radius-pill   : 999px;
--mob-touch         : 44px;          /* min-height/width cho tất cả interactive elements */
--mob-font-body     : 16px;          /* bắt buộc >= 16px — tránh iOS auto-zoom */
--mob-font-sm       : 14px;
--mob-font-xs       : 12px;
--mob-font-micro    : 11px;
```

Thêm dark mode override vào block `html.sg-dark` **hiện có**:
```css
html.sg-dark {
  --mob-bg-page   : #111827;
  --mob-border    : #374151;
  --mob-blue-light: #1e3a5f;
}
```

✅ **Done when:** Inspect element trên Chrome → `:root` thấy `--mob-blue: #004391`.

---

## PHẦN 1 — CSS RESPONSIVE NỀN TẢNG

**File:** `public/assets/css/sg-system.css`

Thêm vào **cuối file** (sau Section cuối cùng hiện có). Không xoá CSS desktop.

```css
/* ════════════════════════════════════════════
   SECTION MOBILE — Traveloka responsive system
   ════════════════════════════════════════════ */

/* Page background mobile */
@media (max-width: 768px) {
  body { background: var(--mob-bg-page); overflow-x: hidden; }

  /* Tất cả section về white card trên nền xám */
  .tz-section,
  .sg-section,
  [class*="-section"] {
    background: #ffffff;
    border-radius: var(--sg-radius-lg);
    margin: 8px;
    padding: 20px 16px;
  }

  /* Headings scale */
  h1 { font-size: 22px !important; }
  h2 { font-size: 18px !important; }
  h3 { font-size: 16px !important; }

  /* Body font tối thiểu 16px — iOS không auto-zoom */
  body, input, select, textarea, button {
    font-size: var(--mob-font-body);
  }

  /* Touch targets */
  button, a, [role="button"], input[type="submit"] {
    min-height: var(--mob-touch);
  }

  /* Tránh horizontal overflow trên 360px */
  * { max-width: 100%; box-sizing: border-box; }

  /* Ẩn topbar trên mobile (đã có rule 600px, bổ sung 768px) */
  .sg-topbar { display: none !important; }

  /* Decorative elements — ẩn trên mobile */
  .gold-accent, .decorative-ribbon, .ornament,
  .tz-about-section { display: none !important; }

  /* Luxury theme padding override */
  .luxury-section,
  [style*="padding: 80px"],
  [style*="padding: 60px"] {
    padding: 24px 16px !important;
  }
}

@media (max-width: 480px) {
  [class*="-section"] { margin: 6px; padding: 16px 12px; }
}

@media (max-width: 360px) {
  /* Samsung Galaxy S8 — zero horizontal scroll */
  body { overflow-x: hidden !important; }
  .container, [class*="container"] { padding-left: 12px !important; padding-right: 12px !important; }
}
```

✅ **Done when:** Không có horizontal scrollbar trên 360px, background page là `#f5f7fa`.

---

## PHẦN 2 — HEADER & MOBILE DRAWER

**File:** `public/assets/css/sg-system.css` (thêm vào mobile section)

### 2.1 Header mobile

```css
@media (max-width: 768px) {
  /* Header thu gọn */
  .header {
    height: 56px !important;
    padding: 0 16px !important;
  }
  .header-flex {
    padding: 0 !important;
    max-width: 100% !important;
  }

  /* Ẩn desktop nav */
  .nav-left, #mainNav,
  .nav-book-btn { display: none !important; }

  /* Logo nhỏ lại */
  .logo-text-stay, .logo-text-go { font-size: 18px !important; }

  /* Hamburger luôn hiện */
  .nav-hamburger {
    display: flex !important;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: var(--mob-touch);
    height: var(--mob-touch);
    padding: 8px;
    background: none;
    border: none;
    cursor: pointer;
  }
  .nav-hamburger span {
    display: block;
    width: 22px;
    height: 2px;
    background: #1a202c;
    border-radius: 2px;
    transition: all 250ms ease;
  }

  /* Header white → hamburger span màu tối */
  html body header.header:not(.header-transparent) .nav-hamburger span {
    background: #1a202c !important;
  }

  /* Mini search bar (xuất hiện khi scrolled — JS thêm class .scrolled) */
  .header-mini-search {
    flex: 1;
    margin: 0 10px;
    height: 36px;
    background: #f1f5f9;
    border-radius: var(--mob-radius-pill);
    display: flex;
    align-items: center;
    padding: 0 12px;
    gap: 8px;
    color: var(--sg-text-muted);
    font-size: var(--mob-font-sm);
    cursor: pointer;
    opacity: 0;
    pointer-events: none;
    transition: opacity 200ms ease;
  }
  .header.scrolled .header-mini-search {
    opacity: 1;
    pointer-events: auto;
  }

  /* Login btn thu gọn */
  .btn-login-nav {
    font-size: 13px !important;
    padding: 6px 12px !important;
  }
}
```

### 2.2 Mobile Drawer cải tiến

Thêm CSS cho drawer **hiện có** (giữ nguyên HTML `#mobileDrawer`, `#mobileDrawerOverlay`):

```css
/* Drawer backdrop */
.mobile-drawer-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  z-index: 998;
  opacity: 0;
  pointer-events: none;
  transition: opacity 250ms ease;
}
.mobile-drawer-overlay.active {
  opacity: 1;
  pointer-events: auto;
}

/* Drawer panel slide từ phải */
.mobile-drawer {
  position: fixed;
  top: 0; right: 0; bottom: 0;
  width: min(300px, 85vw);
  background: #ffffff;
  z-index: 999;
  transform: translateX(100%);
  transition: transform 300ms ease;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}
.mobile-drawer.active { transform: translateX(0); }

/* Drawer header — blue background */
.mobile-drawer-header {
  background: var(--mob-blue);
  padding: 20px 16px 16px;
  color: #ffffff;
  min-height: 90px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.drawer-avatar {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 700; color: #fff;
  flex-shrink: 0;
}
.drawer-user-name { font-size: 15px; font-weight: 600; color: #fff; }
.drawer-user-email { font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 2px; }

/* Drawer nav links */
.mobile-drawer-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 16px;
  height: 52px;
  border-bottom: 1px solid var(--mob-border);
  font-size: 15px;
  color: var(--sg-text-primary);
  text-decoration: none;
  min-height: var(--mob-touch);
  transition: background 200ms;
}
.mobile-drawer-nav a:active { background: var(--mob-blue-light); }
.mobile-drawer-nav a.active {
  color: var(--mob-blue);
  background: var(--mob-blue-light);
  font-weight: 600;
}

/* Close button */
.mobile-drawer-close {
  position: absolute;
  top: 16px; right: 16px;
  width: var(--mob-touch); height: var(--mob-touch);
  background: rgba(255,255,255,0.15);
  border: none; border-radius: 50%;
  color: #fff; font-size: 18px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
}
```

### 2.3 JavaScript — cải tiến toggleMobileMenu()

Trong `resources/views/layouts/app.blade.php`, cập nhật hàm `toggleMobileMenu()` hiện có:

```javascript
// Thêm swipe-to-close cho drawer
(function() {
  const drawer = document.getElementById('mobileDrawer');
  const overlay = document.getElementById('mobileDrawerOverlay');
  if (!drawer) return;

  let touchStartX = 0;

  drawer.addEventListener('touchstart', function(e) {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });

  drawer.addEventListener('touchend', function(e) {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (dx > 60) closeMobileDrawer();  // swipe right to close
  }, { passive: true });

  overlay.addEventListener('click', closeMobileDrawer);

  // Tự đóng khi click link bên trong
  drawer.querySelectorAll('a').forEach(function(link) {
    link.addEventListener('click', closeMobileDrawer);
  });

  // Header mini-search scroll trigger
  const header = document.querySelector('.header');
  const miniSearch = document.querySelector('.header-mini-search');
  if (miniSearch) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 80) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }, { passive: true });
  }
})();

function closeMobileDrawer() {
  document.getElementById('mobileDrawer').classList.remove('active');
  document.getElementById('mobileDrawerOverlay').classList.remove('active');
  document.body.classList.remove('drawer-open');
}
```

### 2.4 HTML — Thêm mini search vào header

Trong `app.blade.php`, tìm `.header-flex` và thêm div mini search **giữa logo và hamburger**:

```html
{{-- Mini search (chỉ hiện mobile khi scroll) --}}
<div class="header-mini-search" onclick="document.getElementById('searchModal')?.showModal()">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
    </svg>
    <span>Tìm khách sạn...</span>
</div>
```

✅ **Done when:** Drawer slide 300ms mượt từ phải, swipe phải đóng, overlay click đóng, mini search xuất hiện khi scroll > 80px trên mobile.

---

## PHẦN 3 — BOTTOM NAVIGATION (5 TAB)

### 3.1 Tạo partial mới

**File mới:** `resources/views/components/mobile-bottom-nav.blade.php`

```blade
{{-- Chỉ hiện trên mobile, ẩn tablet/desktop --}}
<nav class="mob-bottom-nav" aria-label="Mobile navigation">
    <a href="{{ route('home') }}"
       class="mob-nav-tab {{ request()->routeIs('home') ? 'mob-nav-tab--active' : '' }}">
        <svg class="mob-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        <span class="mob-nav-label">Trang chủ</span>
    </a>

    <a href="{{ route('hotels.index') }}"
       class="mob-nav-tab {{ request()->routeIs('hotels*') ? 'mob-nav-tab--active' : '' }}">
        <svg class="mob-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 22V8l9-6 9 6v14"/><rect x="9" y="22" width="6" height="0"/>
            <path d="M9 9h1v4H9zm5 0h1v4h-1z"/>
        </svg>
        <span class="mob-nav-label">Khách sạn</span>
    </a>

    <a href="{{ route('deals') }}"
       class="mob-nav-tab {{ request()->routeIs('deals*') ? 'mob-nav-tab--active' : '' }}">
        <svg class="mob-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
        </svg>
        <span class="mob-nav-label">Ưu đãi</span>
    </a>

    <a href="{{ auth()->check() ? route('my-bookings') : route('login') }}"
       class="mob-nav-tab {{ request()->routeIs('my-bookings*') ? 'mob-nav-tab--active' : '' }}">
        <svg class="mob-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="mob-nav-label">Đặt chỗ</span>
    </a>

    <a href="{{ auth()->check() ? route('profile') : route('login') }}"
       class="mob-nav-tab {{ request()->routeIs('profile*') ? 'mob-nav-tab--active' : '' }}">
        <svg class="mob-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <span class="mob-nav-label">Tài khoản</span>
    </a>
</nav>
```

### 3.2 Include vào layout

Trong `resources/views/layouts/app.blade.php`, thêm **trước `</body>`**:

```blade
@include('components.mobile-bottom-nav')
```

### 3.3 CSS cho bottom nav

Thêm vào `sg-system.css` (mobile section):

```css
.mob-bottom-nav {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 300;
  background: #ffffff;
  border-top: 1px solid var(--mob-border);
  display: none;              /* ẩn desktop mặc định */
  height: calc(56px + env(safe-area-inset-bottom));
  padding-bottom: env(safe-area-inset-bottom);
  box-shadow: var(--mob-shadow-sticky);
}

@media (max-width: 768px) {
  .mob-bottom-nav { display: flex; }

  /* Thêm padding body để nav không che content */
  body {
    padding-bottom: calc(56px + env(safe-area-inset-bottom)) !important;
  }
}

.mob-nav-tab {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  text-decoration: none;
  color: var(--sg-text-muted);
  position: relative;
  min-height: var(--mob-touch);
  transition: color 200ms ease;
  -webkit-tap-highlight-color: transparent;
}
.mob-nav-tab:active { opacity: 0.65; }

.mob-nav-icon {
  width: 22px; height: 22px;
  stroke: currentColor;
}
.mob-nav-label {
  font-size: var(--mob-font-micro);
  line-height: 1;
  white-space: nowrap;
}

.mob-nav-tab--active {
  color: var(--mob-blue);
}
.mob-nav-tab--active .mob-nav-icon {
  stroke: var(--mob-blue);
}
.mob-nav-tab--active .mob-nav-label {
  font-weight: 600;
}

/* Badge đỏ (nếu cần) */
.mob-nav-badge {
  position: absolute;
  top: 6px;
  left: calc(50% + 8px);
  min-width: 16px; height: 16px;
  border-radius: var(--mob-radius-pill);
  background: var(--mob-red);
  color: #fff;
  font-size: 10px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  padding: 0 3px;
  border: 2px solid #fff;
}

/* Dark mode */
html.sg-dark .mob-bottom-nav { background: #1e1e2e; border-top-color: #374151; }
html.sg-dark .mob-nav-tab { color: #9ca3af; }
```

✅ **Done when:** 5 tab đều nhau trên 360px, active state đúng trang, safe-area đúng iPhone 14 Pro, ẩn hoàn toàn ≥ 769px.

---

## PHẦN 4 — TRANG CHỦ MOBILE

**File:** `resources/views/pages/home.blade.php`

### 4.1 Hero compact (thêm sau `@section('content')`)

Thêm HTML sau `@section('content')`, trước hero section hiện tại. Dùng `@push('styles')` để thêm CSS.

```html
{{-- Hero mobile compact — chỉ hiện < 768px --}}
<section class="mob-hero">
    <p class="mob-hero-greeting">Bạn muốn đi đâu?</p>
    <div class="mob-hero-search" onclick="document.querySelector('.sg-search-bar')?.scrollIntoView({behavior:'smooth'})">
        <svg width="16" height="16" fill="none" stroke="#004391" stroke-width="2.5" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <span>Tìm khách sạn, địa điểm...</span>
    </div>
    <div class="mob-hero-chips">
        <button class="mob-chip">📍 Gần đây</button>
        <button class="mob-chip">🔥 Phổ biến</button>
        <button class="mob-chip">💰 Ưu đãi</button>
    </div>
</section>

{{-- Service icon grid — đặc trưng Traveloka --}}
<section class="mob-service-grid">
    <a href="{{ route('hotels.index') }}" class="mob-service-item">
        <div class="mob-service-icon">🏨</div>
        <span>Khách sạn</span>
    </a>
    <a href="{{ route('hotels.index', ['type' => 'resort']) }}" class="mob-service-item">
        <div class="mob-service-icon">🏖️</div>
        <span>Resort</span>
    </a>
    <a href="{{ route('deals') }}" class="mob-service-item">
        <div class="mob-service-icon">🏷️</div>
        <span>Ưu đãi</span>
    </a>
    <a href="{{ route('blog') }}" class="mob-service-item">
        <div class="mob-service-icon">🗺️</div>
        <span>Cẩm nang</span>
    </a>
</section>
```

CSS trong `@push('styles')`:
```css
/* Hero mobile */
.mob-hero {
  background: var(--mob-blue);
  padding: 20px 16px 24px;
  display: none;
}
@media (max-width: 768px) { .mob-hero { display: block; } }

.mob-hero-greeting { color: #fff; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
.mob-hero-search {
  background: #fff;
  border-radius: var(--mob-radius-pill);
  height: 48px;
  display: flex; align-items: center;
  padding: 0 16px; gap: 10px;
  color: var(--sg-text-muted);
  font-size: var(--mob-font-body);
  cursor: pointer;
}
.mob-hero-chips { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
.mob-chip {
  background: rgba(255,255,255,0.18);
  color: #fff; border: 1px solid rgba(255,255,255,0.3);
  border-radius: var(--mob-radius-pill);
  padding: 6px 14px;
  font-size: var(--mob-font-sm);
  min-height: var(--mob-touch);
  cursor: pointer;
}

/* Service grid */
.mob-service-grid {
  display: none;
  grid-template-columns: repeat(4, 1fr);
  gap: 4px;
  padding: 16px;
  background: #ffffff;
  border-bottom: 1px solid var(--mob-border);
}
@media (max-width: 768px) { .mob-service-grid { display: grid; } }

.mob-service-item {
  display: flex; flex-direction: column;
  align-items: center; gap: 6px;
  text-decoration: none;
  color: var(--sg-text-muted);
  font-size: var(--mob-font-xs);
  padding: 10px 4px;
  border-radius: var(--sg-radius-md);
  min-height: var(--mob-touch);
  transition: opacity 150ms;
  text-align: center;
}
.mob-service-item:active { opacity: 0.6; }
.mob-service-icon {
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--mob-blue-light);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}
```

### 4.2 HLC cards (hotel scroll ngang) — cập nhật CSS hiện có

Thêm vào `@push('styles')` của `home.blade.php`:

```css
@media (max-width: 768px) {
  /* HLC grid chuyển sang flex scroll */
  .hlc-grid {
    display: flex !important;
    gap: 12px !important;
    overflow-x: auto !important;
    scroll-snap-type: x mandatory !important;
    padding: 4px 16px 16px !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: none !important;
  }
  .hlc-grid::-webkit-scrollbar { display: none; }

  /* Card kích thước mobile */
  .hlc-card {
    flex: 0 0 72vw !important;
    max-width: 240px !important;
    height: 320px !important;
    scroll-snap-align: start;
  }

  /* Destination grid: 2x2 */
  .tz-destination-grid,
  .destinations-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 8px !important;
    padding: 0 12px !important;
  }

  /* Blog grid: 1 cột */
  .blog-grid, .sg-blog-grid {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }

  /* Why section: 1 cột */
  .tz-why-grid, .why-grid {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }
}
```

---

## PHẦN 5 — TRANG /HOTELS (DANH SÁCH)

**File:** `resources/views/pages/hotels.blade.php` + `@push('styles')`

### 5.1 Filter chips bar sticky

Thêm **trước** vòng lặp danh sách khách sạn:

```html
{{-- Filter chips bar — chỉ mobile --}}
<div class="mob-filter-bar" id="filterChipsBar">
    <button class="mob-chip-filter mob-chip-filter--primary" onclick="toggleFilterSheet()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/>
            <line x1="11" y1="18" x2="13" y2="18"/>
        </svg>
        Bộ lọc
    </button>
    <button class="mob-chip-filter" onclick="setSortMobile('price_asc')" id="chipPriceAsc">Giá ↑</button>
    <button class="mob-chip-filter" onclick="setSortMobile('price_desc')" id="chipPriceDesc">Giá ↓</button>
    <button class="mob-chip-filter" onclick="setSortMobile('rating')" id="chipRating">Điểm cao</button>
    <button class="mob-chip-filter" id="chipStar">Hạng sao</button>
</div>
```

CSS:
```css
.mob-filter-bar {
  display: none;
}
@media (max-width: 768px) {
  .mob-filter-bar {
    display: flex;
    position: sticky; top: 56px; z-index: 90;
    background: #fff;
    border-bottom: 1px solid var(--mob-border);
    gap: 8px; padding: 10px 16px;
    overflow-x: auto; scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .mob-filter-bar::-webkit-scrollbar { display: none; }

  /* Ẩn sidebar desktop */
  .hotel-sidebar, .sg-sidebar, [class*="sidebar"] {
    display: none !important;
  }
  /* Main content full width */
  .hotel-main, .hotels-main, [class*="-main"] {
    width: 100% !important;
    max-width: 100% !important;
  }
}

.mob-chip-filter {
  flex-shrink: 0;
  height: 36px; padding: 0 14px;
  border-radius: var(--mob-radius-pill);
  border: 1px solid var(--mob-border);
  background: #fff; color: var(--sg-text-primary);
  font-size: var(--mob-font-sm);
  white-space: nowrap;
  display: flex; align-items: center; gap: 6px;
  min-height: var(--mob-touch);
  cursor: pointer; transition: all 200ms;
  font-family: inherit;
}
.mob-chip-filter--primary {
  background: var(--mob-blue); color: #fff;
  border-color: var(--mob-blue);
}
.mob-chip-filter--active {
  background: var(--mob-blue-light);
  color: var(--mob-blue);
  border-color: var(--mob-blue);
}
```

### 5.2 Hotel list card dạng ngang (Traveloka)

Thêm CSS để override card grid thành list trên mobile:

```css
@media (max-width: 768px) {
  /* Override grid → list */
  .hotels-grid, .hotel-grid, [class*="hotel-grid"] {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    padding: 0 12px 12px !important;
  }

  /* Hotel card: ngang layout */
  .hotel-card, [class*="hotel-card"] {
    display: flex !important;
    flex-direction: row !important;
    align-items: stretch !important;
    gap: 0 !important;
    border-radius: var(--sg-radius-md) !important;
    overflow: hidden !important;
    box-shadow: var(--mob-shadow-card) !important;
    background: #fff !important;
    min-height: 100px !important;
    max-height: 110px !important;
  }

  /* Ảnh vuông bên trái */
  .hotel-card .hotel-thumb,
  .hotel-card img:first-child,
  .hotel-card .card-img,
  .hotel-card .hotel-image {
    width: 100px !important;
    min-width: 100px !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 0 !important;
  }

  /* Thông tin bên phải */
  .hotel-card .hotel-body,
  .hotel-card .card-body,
  .hotel-card .hotel-info {
    flex: 1 !important;
    padding: 10px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    overflow: hidden !important;
  }

  .hotel-card .hotel-name,
  .hotel-card .card-title {
    font-size: var(--mob-font-sm) !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-bottom: 2px !important;
  }

  .hotel-card .hotel-price,
  .hotel-card .price {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--mob-blue) !important;
  }
}
```

### 5.3 Bottom sheet filter

Thêm HTML **trước `</body>`** hoặc cuối trang hotels:

```html
{{-- Filter bottom sheet --}}
<div class="mob-sheet-backdrop" id="filterBackdrop" onclick="toggleFilterSheet()" style="display:none"></div>
<div class="mob-sheet" id="filterSheet" style="display:none">
    <div class="mob-sheet-handle"></div>
    <div class="mob-sheet-header">
        <h3>Bộ lọc</h3>
        <button onclick="toggleFilterSheet()" class="mob-sheet-close">✕</button>
    </div>
    <div class="mob-sheet-content">
        <p class="mob-filter-label">Khoảng giá/đêm</p>
        <div class="mob-filter-chips">
            <button class="mob-chip-filter" onclick="toggleChip(this)">Dưới 500K</button>
            <button class="mob-chip-filter" onclick="toggleChip(this)">500K – 1 triệu</button>
            <button class="mob-chip-filter" onclick="toggleChip(this)">Trên 1 triệu</button>
        </div>

        <p class="mob-filter-label">Hạng sao</p>
        <div class="mob-filter-chips">
            <button class="mob-chip-filter" onclick="toggleChip(this)">⭐ 3 sao</button>
            <button class="mob-chip-filter" onclick="toggleChip(this)">⭐⭐ 4 sao</button>
            <button class="mob-chip-filter" onclick="toggleChip(this)">⭐⭐⭐ 5 sao</button>
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button class="mob-btn-outline" onclick="clearFilters()">Xóa lọc</button>
        <button class="mob-btn-solid" onclick="applyFilters()">Áp dụng</button>
    </div>
</div>

<script>
function toggleFilterSheet() {
  var sheet = document.getElementById('filterSheet');
  var backdrop = document.getElementById('filterBackdrop');
  var isOpen = sheet.style.display !== 'none';
  sheet.style.display = isOpen ? 'none' : 'flex';
  backdrop.style.display = isOpen ? 'none' : 'block';
  document.body.style.overflow = isOpen ? '' : 'hidden';
}

function toggleChip(btn) {
  btn.classList.toggle('mob-chip-filter--active');
}

// Swipe down to close
(function() {
  var sheet = document.getElementById('filterSheet');
  if (!sheet) return;
  var startY = 0;
  sheet.addEventListener('touchstart', function(e) {
    startY = e.touches[0].clientY;
  }, { passive: true });
  sheet.addEventListener('touchend', function(e) {
    if (e.changedTouches[0].clientY - startY > 60) toggleFilterSheet();
  }, { passive: true });
})();
</script>
```

CSS:
```css
.mob-sheet-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  z-index: 400;
}
.mob-sheet {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: #fff;
  border-radius: 14px 14px 0 0;
  max-height: 80vh;
  z-index: 401;
  flex-direction: column;
  overflow: hidden;
}
.mob-sheet-handle {
  width: 36px; height: 4px;
  border-radius: var(--mob-radius-pill);
  background: var(--mob-border);
  margin: 10px auto 0;
  flex-shrink: 0;
}
.mob-sheet-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid var(--mob-border);
  flex-shrink: 0;
}
.mob-sheet-header h3 { font-size: 16px; font-weight: 700; }
.mob-sheet-close {
  width: var(--mob-touch); height: var(--mob-touch);
  display: flex; align-items: center; justify-content: center;
  background: none; border: none;
  font-size: 18px; color: var(--sg-text-muted); cursor: pointer;
}
.mob-sheet-content {
  flex: 1; overflow-y: auto;
  padding: 16px;
  -webkit-overflow-scrolling: touch;
}
.mob-sheet-footer {
  display: flex; gap: 12px; padding: 12px 16px;
  padding-bottom: max(12px, env(safe-area-inset-bottom));
  border-top: 1px solid var(--mob-border);
  flex-shrink: 0;
}
.mob-filter-label { font-size: 13px; font-weight: 600; color: var(--sg-text-primary); margin: 16px 0 8px; }
.mob-filter-chips { display: flex; gap: 8px; flex-wrap: wrap; }
.mob-btn-outline {
  flex: 1; height: 48px; border-radius: var(--sg-radius-md);
  border: 1.5px solid var(--mob-blue); color: var(--mob-blue);
  background: transparent; font-size: var(--mob-font-body);
  cursor: pointer; font-family: inherit;
}
.mob-btn-solid {
  flex: 2; height: 48px; border-radius: var(--sg-radius-md);
  background: var(--mob-blue); color: #fff; border: none;
  font-size: var(--mob-font-body); font-weight: 700;
  cursor: pointer; font-family: inherit;
}
```

---

## PHẦN 6 — CHI TIẾT KHÁCH SẠN

**File:** `resources/views/pages/hotel-detail.blade.php`

### 6.1 Gallery mobile

Thêm CSS vào `@push('styles')`:

```css
@media (max-width: 768px) {
  /* Gallery: 2/3 + 1/3 */
  .hotel-gallery,
  [class*="gallery"] {
    display: grid !important;
    grid-template-columns: 2fr 1fr !important;
    gap: 2px !important;
    height: 200px !important;
    border-radius: 0 !important;
  }

  .gallery-count-badge {
    position: absolute; bottom: 8px; right: 8px;
    background: rgba(0,0,0,0.55); color: #fff;
    font-size: 11px; padding: 3px 8px;
    border-radius: var(--mob-radius-pill);
    pointer-events: none;
  }

  /* Tab bar: scroll ngang, không wrap */
  .hotel-tabs, .detail-tab-bar {
    display: flex !important;
    overflow-x: auto !important;
    scroll-snap-type: x mandatory !important;
    border-bottom: 1px solid var(--mob-border) !important;
    scrollbar-width: none !important;
    background: #fff !important;
    position: sticky !important;
    top: 56px !important;
    z-index: 80 !important;
  }
  .hotel-tabs::-webkit-scrollbar { display: none; }

  .hotel-tab-btn, .detail-tab-btn {
    flex-shrink: 0 !important;
    height: 44px !important;
    padding: 0 20px !important;
    border: none !important;
    border-bottom: 2px solid transparent !important;
    background: none !important;
    font-size: var(--mob-font-sm) !important;
    color: var(--sg-text-muted) !important;
    scroll-snap-align: start !important;
    cursor: pointer !important;
    white-space: nowrap !important;
  }
  .hotel-tab-btn.active, .detail-tab-btn.active {
    color: var(--mob-blue) !important;
    border-bottom-color: var(--mob-blue) !important;
    font-weight: 600 !important;
  }

  /* Booking widget desktop → ẩn, dùng sticky bar thay */
  .booking-widget-desktop, [class*="booking-widget"] {
    display: none !important;
  }

  /* Amenity chips scroll ngang */
  .amenity-list, [class*="amenity"] {
    display: flex !important;
    overflow-x: auto !important;
    gap: 8px !important;
    scrollbar-width: none !important;
    flex-wrap: nowrap !important;
    padding-bottom: 4px !important;
  }
}
```

### 6.2 Sticky booking bar

Thêm HTML **cuối** `hotel-detail.blade.php` (trong `@section('content')`):

```html
{{-- Sticky booking bar — chỉ mobile --}}
<div class="mob-sticky-bar" id="mobStickyBar" style="display:none">
    <div class="mob-sticky-bar__price">
        <span class="mob-sticky-from">Từ</span>
        <span class="mob-sticky-amount">{{ number_format($hotel->price) }}đ
            <small>/đêm</small>
        </span>
    </div>
    <a href="#booking-widget" class="mob-sticky-btn">Đặt ngay</a>
</div>

<script>
// Hiện sticky bar khi scroll qua booking widget
(function() {
  var bar = document.getElementById('mobStickyBar');
  var widget = document.getElementById('booking-widget') || document.querySelector('[class*="booking-widget"]');
  if (!bar || !widget) return;

  var observer = new IntersectionObserver(function(entries) {
    var isVisible = entries[0].isIntersecting;
    if (window.innerWidth <= 768) {
      bar.style.display = isVisible ? 'none' : 'flex';
    }
  }, { threshold: 0.1 });

  observer.observe(widget);
})();
</script>
```

CSS:
```css
.mob-sticky-bar {
  position: fixed; bottom: calc(56px + env(safe-area-inset-bottom));
  left: 0; right: 0; z-index: 150;
  background: #fff;
  box-shadow: var(--mob-shadow-sticky);
  align-items: center; justify-content: space-between;
  padding: 10px 16px;
  display: none;
}
@media (max-width: 768px) { /* controlled by JS */ }

.mob-sticky-bar__price { display: flex; flex-direction: column; }
.mob-sticky-from { font-size: 11px; color: var(--sg-text-muted); }
.mob-sticky-amount {
  font-size: 18px; font-weight: 700; color: var(--mob-blue);
}
.mob-sticky-amount small {
  font-size: 12px; font-weight: 400; color: var(--sg-text-muted);
}
.mob-sticky-btn {
  background: var(--mob-blue); color: #fff;
  width: 120px; height: 44px;
  border-radius: var(--sg-radius-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; font-weight: 700;
  text-decoration: none;
}
```

---

## PHẦN 7 — AUTH PAGES

### 7.1 Tạo layout auth riêng

**File mới:** `resources/views/layouts/auth.blade.php`

```blade
<!DOCTYPE html>
<html lang="vi" class="{{ Cookie::get('sg-theme') === 'dark' ? 'sg-dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Đăng nhập') – StayGo</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sg-system.css') }}">
    @stack('styles')
</head>
<body class="auth-body">

    <div class="auth-page-wrap">
        {{-- Top blue header: 30% --}}
        <div class="auth-top-panel">
            <a href="{{ route('home') }}" class="auth-logo-link">
                <span class="logo-text-stay" style="color:#fff;font-size:26px;">Stay</span>
                <span class="logo-text-go" style="color:rgba(255,255,255,0.8);font-size:26px;">Go</span>
            </a>
            <p class="auth-tagline">Đặt phòng dễ dàng, trải nghiệm tuyệt vời</p>
        </div>

        {{-- Bottom white card: 70% --}}
        <div class="auth-card-panel">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
```

### 7.2 Cập nhật login.blade.php dùng layout mới

Thay `@extends('layouts.app')` → `@extends('layouts.auth')`.
Restructure `@section('content')`:

```html
<h1 class="auth-title">@yield('auth_title', 'Đăng nhập')</h1>

{{-- Toggle Email / Số điện thoại --}}
<div class="auth-toggle-bar">
    <button class="auth-toggle-btn auth-toggle-btn--active" onclick="switchAuthTab('email', this)">Email</button>
    <button class="auth-toggle-btn" onclick="switchAuthTab('phone', this)">Số điện thoại</button>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="auth-field">
        <label>Email</label>
        <input type="email" name="email" class="auth-input" placeholder="email@example.com"
               onfocus="this.scrollIntoView({behavior:'smooth',block:'center'})"
               value="{{ old('email') }}" required>
    </div>
    <div class="auth-field">
        <label>Mật khẩu</label>
        <div class="auth-input-wrap">
            <input type="password" name="password" id="pwField" class="auth-input" placeholder="••••••••" required>
            <button type="button" class="auth-pw-toggle" onclick="togglePw()">👁</button>
        </div>
    </div>
    <a href="{{ route('password.request') }}" class="auth-forgot">Quên mật khẩu?</a>
    <button type="submit" class="auth-submit-btn">Đăng nhập</button>
</form>

<div class="auth-divider"><span>Hoặc</span></div>

<a href="{{ route('auth.google') }}" class="auth-social-btn">
    <img src="{{ asset('assets/images/google-icon.png') }}" width="20" alt="Google">
    Tiếp tục với Google
</a>

<p class="auth-switch-link">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
```

CSS cho auth (thêm vào `sg-system.css` hoặc `@push('styles')`):
```css
.auth-body { background: var(--mob-blue); min-height: 100dvh; margin: 0; }
.auth-page-wrap { min-height: 100dvh; display: flex; flex-direction: column; }
.auth-top-panel {
  flex: 3; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 8px; padding: 32px 16px 20px;
  background: var(--mob-blue);
}
.auth-tagline { color: rgba(255,255,255,0.8); font-size: var(--mob-font-sm); text-align: center; }
.auth-card-panel {
  flex: 7; background: #fff;
  border-radius: 14px 14px 0 0;
  padding: 24px 20px;
  padding-bottom: max(24px, env(safe-area-inset-bottom));
  overflow-y: auto;
}
.auth-title { font-size: 22px; font-weight: 700; color: var(--sg-text-primary); margin-bottom: 20px; }
.auth-toggle-bar {
  display: flex; background: var(--mob-blue-light);
  border-radius: var(--mob-radius-pill);
  padding: 4px; margin-bottom: 20px;
}
.auth-toggle-btn {
  flex: 1; height: 36px; border-radius: var(--mob-radius-pill);
  border: none; background: transparent;
  font-size: var(--mob-font-sm); color: var(--sg-text-muted);
  cursor: pointer; transition: all 200ms; font-family: inherit;
}
.auth-toggle-btn--active {
  background: #fff; color: var(--mob-blue); font-weight: 600;
}
.auth-field { margin-bottom: 20px; }
.auth-field label { font-size: 12px; color: var(--sg-text-muted); display: block; margin-bottom: 6px; }
.auth-input {
  width: 100%; border: none; border-bottom: 1.5px solid var(--mob-border);
  padding: 10px 0; font-size: var(--mob-font-body);
  color: var(--sg-text-primary); background: transparent;
  outline: none; font-family: inherit;
}
.auth-input:focus { border-bottom-color: var(--mob-blue); }
.auth-input-wrap { position: relative; }
.auth-pw-toggle {
  position: absolute; right: 0; top: 50%; transform: translateY(-50%);
  background: none; border: none; font-size: 18px; cursor: pointer;
  min-height: var(--mob-touch); min-width: var(--mob-touch);
  display: flex; align-items: center; justify-content: center;
}
.auth-forgot {
  display: block; text-align: right; font-size: var(--mob-font-sm);
  color: var(--mob-blue); text-decoration: none; margin-bottom: 20px;
}
.auth-submit-btn {
  width: 100%; height: 50px; border-radius: var(--sg-radius-md);
  background: var(--mob-blue); color: #fff; border: none;
  font-size: var(--mob-font-body); font-weight: 700;
  cursor: pointer; font-family: inherit;
}
.auth-divider {
  display: flex; align-items: center; gap: 12px;
  margin: 20px 0; color: var(--sg-text-muted); font-size: 12px;
}
.auth-divider::before, .auth-divider::after {
  content: ''; flex: 1; height: 1px; background: var(--mob-border);
}
.auth-social-btn {
  width: 100%; height: 48px; border-radius: var(--sg-radius-md);
  border: 1px solid var(--mob-border); background: #fff;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  font-size: var(--mob-font-sm); color: var(--sg-text-primary);
  text-decoration: none; margin-bottom: 12px;
}
.auth-switch-link {
  text-align: center; margin-top: 16px; font-size: var(--mob-font-sm);
  color: var(--sg-text-muted);
}
.auth-switch-link a { color: var(--mob-blue); text-decoration: none; font-weight: 600; }

/* JS helpers */
function togglePw() {
  var f = document.getElementById('pwField');
  f.type = f.type === 'password' ? 'text' : 'password';
}
function switchAuthTab(type, btn) {
  document.querySelectorAll('.auth-toggle-btn').forEach(function(b) {
    b.classList.remove('auth-toggle-btn--active');
  });
  btn.classList.add('auth-toggle-btn--active');
}
```

---

## PHẦN 8 — BOOKING & PAYMENT

**Files:** `booking.blade.php`, `payment.blade.php`

CSS thêm vào `@push('styles')`:

```css
@media (max-width: 768px) {
  /* Progress steps */
  .booking-progress { display: flex; align-items: center; padding: 12px 16px; gap: 0; }
  .booking-step-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,0.3); flex-shrink: 0;
  }
  .booking-step-dot.active { background: var(--mob-amber); }
  .booking-step-dot.done { background: #fff; }
  .booking-step-line { flex: 1; height: 1px; background: rgba(255,255,255,0.25); }
  .booking-step-line.done { background: rgba(255,255,255,0.7); }

  /* Layout 1 cột */
  .booking-layout {
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    padding: 0 !important;
  }

  /* Order summary thu gọn */
  .order-summary-card {
    margin: 8px;
    border-radius: var(--sg-radius-md);
    border-left: 3px solid var(--mob-blue);
    background: var(--mob-blue-light);
    overflow: hidden;
    cursor: pointer;
  }
  .order-summary-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 14px;
  }
  .order-summary-body { padding: 0 14px 12px; border-top: 1px solid var(--mob-border); }
  .order-row {
    display: flex; justify-content: space-between;
    font-size: var(--mob-font-sm); padding: 5px 0;
    color: var(--sg-text-muted);
  }
  .order-row-total {
    border-top: 1px solid var(--mob-border); margin-top: 4px; padding-top: 8px;
    color: var(--sg-text-primary); font-weight: 700; font-size: 15px;
  }

  /* Form fields underline style */
  .booking-form-card {
    background: #fff; margin: 8px;
    border-radius: var(--sg-radius-md);
    padding: 16px;
    box-shadow: var(--mob-shadow-card);
  }
  .booking-field { margin-bottom: 18px; }
  .booking-field label { font-size: 12px; color: var(--sg-text-muted); display: block; margin-bottom: 4px; }
  .booking-input {
    width: 100%; border: none; border-bottom: 1.5px solid var(--mob-border);
    padding: 10px 0; font-size: var(--mob-font-body);
    background: transparent; outline: none; font-family: inherit;
  }
  .booking-input:focus { border-bottom-color: var(--mob-blue); }

  /* Sticky bottom bar */
  .booking-bottom-bar {
    position: fixed; bottom: calc(56px + env(safe-area-inset-bottom));
    left: 0; right: 0; z-index: 150;
    background: #fff; box-shadow: var(--mob-shadow-sticky);
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px;
  }
  body { padding-bottom: calc(112px + env(safe-area-inset-bottom)) !important; }
}
```

---

## PHẦN 9 — BLOG & BLOG DETAIL

**Files:** `blog.blade.php`, `blog-detail.blade.php`

```css
@media (max-width: 768px) {
  /* Blog grid: 1 cột */
  .blog-grid, [class*="blog-grid"] {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
    padding: 0 12px !important;
  }

  /* Category filter scroll ngang */
  .blog-categories, .category-filter {
    display: flex !important;
    overflow-x: auto !important;
    gap: 8px !important;
    padding: 10px 16px !important;
    scrollbar-width: none !important;
    flex-wrap: nowrap !important;
  }
  .blog-categories::-webkit-scrollbar { display: none; }

  /* Blog detail: typography mobile */
  .blog-detail-content, [class*="post-content"] {
    font-size: var(--mob-font-body) !important;
    line-height: 1.7 !important;
    padding: 0 16px !important;
  }
  .blog-detail-content img { width: 100% !important; border-radius: var(--sg-radius-md) !important; }

  /* Related posts: scroll ngang */
  .related-posts-grid {
    display: flex !important;
    overflow-x: auto !important;
    gap: 12px !important;
    padding: 0 16px 12px !important;
    scrollbar-width: none !important;
  }
  .related-posts-grid::-webkit-scrollbar { display: none; }
  .related-post-card { flex: 0 0 70vw !important; max-width: 250px !important; }
}
```

---

## PHẦN 10 — TRANG ƯU ĐÃI (DEALS)

```css
@media (max-width: 768px) {
  /* Deals grid: 1 cột */
  .deals-grid, [class*="deal-grid"] {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
    padding: 0 12px !important;
  }

  /* Deal card: giữ badge %, countdown */
  .deal-badge {
    position: absolute !important;
    top: 8px !important; left: 8px !important;
    font-size: 11px !important;
    padding: 3px 8px !important;
  }
  .deal-countdown {
    font-size: 13px !important;
    letter-spacing: 1px !important;
  }

  /* CTA full width */
  .deal-cta-btn, [class*="deal-btn"] {
    width: 100% !important;
    height: 44px !important;
    font-size: var(--mob-font-sm) !important;
  }
}
```

---

## PHẦN 11 — LIÊN HỆ (CONTACT)

```css
@media (max-width: 768px) {
  /* Contact layout: 1 cột */
  .contact-layout, [class*="contact-grid"] {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
  }

  /* Map full width */
  .contact-map, iframe[src*="google.com/maps"] {
    width: 100% !important;
    height: 220px !important;
    border-radius: var(--sg-radius-md) !important;
  }

  /* Contact form */
  .contact-form input, .contact-form textarea {
    font-size: var(--mob-font-body) !important;  /* tránh iOS zoom */
  }
}
```

---

## PHẦN 12 — PROFILE & MY BOOKINGS

```css
@media (max-width: 768px) {
  /* Sidebar → tab bar ngang */
  .profile-sidebar, .account-sidebar {
    display: none !important;
  }
  .profile-tab-bar {
    display: flex !important;
    overflow-x: auto !important;
    border-bottom: 1px solid var(--mob-border) !important;
    scrollbar-width: none !important;
    background: #fff !important;
    position: sticky !important; top: 56px !important; z-index: 80 !important;
  }
  .profile-tab-bar::-webkit-scrollbar { display: none; }
  .profile-tab-btn {
    flex-shrink: 0 !important;
    height: 44px !important; padding: 0 16px !important;
    border: none !important; border-bottom: 2px solid transparent !important;
    background: none !important; font-size: var(--mob-font-sm) !important;
    color: var(--sg-text-muted) !important; cursor: pointer !important;
    white-space: nowrap !important;
  }
  .profile-tab-btn.active {
    color: var(--mob-blue) !important;
    border-bottom-color: var(--mob-blue) !important;
    font-weight: 600 !important;
  }

  /* Profile content full width */
  .profile-content, .account-content {
    width: 100% !important;
    max-width: 100% !important;
    padding: 12px !important;
  }

  /* Booking card: dọc layout */
  .booking-card, [class*="booking-card"] {
    flex-direction: column !important;
  }
  .booking-card-img {
    width: 100% !important; height: 160px !important;
    border-radius: var(--sg-radius-md) var(--sg-radius-md) 0 0 !important;
  }

  /* Action buttons 50/50 */
  .booking-actions {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 8px !important;
  }
}
```

---

## PHẦN 13 — SUPPORT THREAD

```css
@media (max-width: 768px) {
  .support-layout { flex-direction: column !important; }
  .support-sidebar { width: 100% !important; max-height: 200px !important; overflow-y: auto !important; }
  .support-chat { padding: 12px !important; }
  .support-message { max-width: 85% !important; }

  /* Input reply sticky bottom */
  .support-reply-bar {
    position: sticky !important; bottom: calc(56px + env(safe-area-inset-bottom)) !important;
    background: #fff !important; padding: 8px 12px !important;
    border-top: 1px solid var(--mob-border) !important;
  }
}
```

---

## PHẦN 14 — DARK MODE MOBILE

Thêm vào `sg-system.css` sau các mobile rules:

```css
html.sg-dark .mob-bottom-nav       { background: #1e1e2e; border-top-color: #374151; }
html.sg-dark .mob-nav-tab          { color: #6b7280; }
html.sg-dark .mob-nav-tab--active  { color: #60a5fa; }
html.sg-dark .mob-sheet            { background: #1e1e2e; }
html.sg-dark .mob-sheet-header     { border-bottom-color: #374151; }
html.sg-dark .mob-filter-bar       { background: #1e1e2e; border-bottom-color: #374151; }
html.sg-dark .mob-chip-filter      { background: #1e1e2e; color: #e5e7eb; border-color: #374151; }
html.sg-dark .mob-hero             { background: #003070; }
html.sg-dark .mob-service-grid     { background: #1e1e2e; border-bottom-color: #374151; }
html.sg-dark .mob-sticky-bar       { background: #1e1e2e; }
html.sg-dark .auth-card-panel      { background: #1e1e2e; }
html.sg-dark .auth-input           { color: #f0f0f0; border-bottom-color: #374151; }
html.sg-dark .auth-title           { color: #f0f0f0; }
html.sg-dark .booking-form-card    { background: #1e1e2e; }
html.sg-dark .booking-input        { color: #f0f0f0; border-bottom-color: #374151; }
html.sg-dark .mobile-drawer        { background: #1e1e2e; }
html.sg-dark .mobile-drawer-nav a  { color: #e5e7eb; border-bottom-color: #374151; }
```

---

## THỨ TỰ THỰC HIỆN

```
Phần 0  → Phần 1  → Phần 2  → Phần 3
(tokens)  (CSS nền)  (Header)  (Bottom nav)
    ↓
Phần 4  → Phần 5  → Phần 6
(Home)   (Hotels)  (Detail)
    ↓
Phần 7  → Phần 8  → Phần 9 → Phần 10
(Auth)   (Booking) (Blog)   (Deals)
    ↓
Phần 11 → Phần 12 → Phần 13 → Phần 14
(Contact) (Profile) (Support) (Dark mode)
```

---

## ACCEPTANCE CRITERIA

| Màn hình | Test case | Pass khi |
|----------|-----------|----------|
| Samsung S8 360px | Scroll ngang toàn trang | Không có horizontal scrollbar |
| iPhone 12 390px | Tap bất kỳ input | Không auto-zoom (font ≥ 16px) |
| iPhone 12 390px | Keyboard mở | Input scroll vào view, không bị che |
| iPhone 14 Pro | Bottom nav + sticky bars | Không bị Dynamic Island / home indicator che |
| Chrome DevTools Touch | Swipe drawer | Vuốt phải đóng drawer |
| Chrome DevTools Touch | Swipe sheet | Vuốt xuống đóng bottom sheet |
| Mọi trang ≤ 768px | Bottom nav | 5 tab đều nhau, active đúng |
| Mọi trang ≥ 769px | Bottom nav, sticky bars | Ẩn hoàn toàn |
| Mọi trang | Dark mode | Không có text trắng trên nền trắng |
| Hotels page | Filter sheet | Áp dụng filter reload đúng URL params |
| Hotel detail | Sticky booking bar | Ẩn khi widget visible, hiện khi scroll qua |
| Auth pages | Login flow | Form submit đúng route, CSRF không lỗi |
| Auth pages | Safe area | Card không bị home indicator che trên iPhone |

---

## GHI CHÚ KỸ THUẬT

- **KHÔNG dùng Alpine.js** — project chỉ dùng vanilla JS
- **KHÔNG tạo file SCSS** — plain CSS, thêm vào sg-system.css hoặc `@push('styles')`
- **KHÔNG override `!important` hiện có** bằng specificity thấp hơn — dùng HTML selector dài hơn
- **Auth layout**: Đổi `@extends('layouts.app')` → `@extends('layouts.auth')` cho cả 7 file auth
- **CSS load order**: Luôn thêm mobile rules vào `sg-system.css` (file cuối cùng, cao nhất) để không bị override
- **`env(safe-area-inset-bottom)`**: Luôn dùng `max(Xpx, env(...))` thay vì chỉ `env(...)` để tránh lỗi trên Chrome Android
- **`100dvh`** thay vì `100vh` cho auth page — tránh layout jump khi keyboard mở trên mobile
