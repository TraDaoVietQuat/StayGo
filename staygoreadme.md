# 🏨 STAYGO HOTEL BOOKING PLATFORM — COMPREHENSIVE GUIDE v2.5

> **Project:** StayGoLaravel  
> **Scope:** UI Redesign (Traveloka-style) + Technical Implementation  
> **Color Scheme:** XANH & TRẮNG (Xanh dominant)  
> **Tech Stack:** Laravel 12 + Vite 5 + Custom Utility Classes  
> **Last Updated:** 2024 | **Version:** 2.5

---

## 📑 TABLE OF CONTENTS

- [⚡ QUICK START — ĐỌC TRƯỚC KHI CODE](#quick-start)
- [I. DESIGN SYSTEM — MÀU & TYPOGRAPHY](#design-system)
- [II. LAYOUT & COMPONENTS](#layout-components)
- [III. TECHNICAL IMPLEMENTATION](#technical-impl)
- [IV. UTILITY CLASS REFERENCE](#utility-class)
- [V. FORM STATES & ACCESSIBILITY](#form-states)
- [VI. PAGE FLOWS](#page-flows)
- [VII. JS IMPLEMENTATION](#js-impl)
- [VIII. CHECKLIST](#checklist)

---

<a name="quick-start"></a>
## ⚡ QUICK START — 7 QUY TẮC QUAN TRỌNG

**Đọc block này trước khi viết bất kỳ dòng code nào.**

| # | Rule | Chi tiết |
|---|---|---|
| Q1 | **Không inline style** nếu đã có class trong Bảng 6.1 — kể cả `margin-*` | Tra Bảng 6.1, thay bằng class |
| Q2 | Input lỗi **bắt buộc** `is-error` + `aria-invalid="true"` + `aria-describedby` | Phần V.1 |
| Q3 | SgDark apply = **inline script trong `<head>`**, không `src`, không `defer` | Phần VII.2 |
| Q4 | `.sg-field-error` **bắt buộc** `role="alert"` | Phần V.1 |
| Q5 | Empty Search **không dùng** `sg-error-page` — dùng `sg-empty` + giữ navbar | Phần VI.4 |
| Q6 | **Không tự bịa** tên class hay token — tra Bảng 6.1 / Phần 0, hoặc dùng `/* TODO */` | Khi thiếu info |
| Q7 | Sau khi sinh code, **chạy Checklist VIII** — mục `[BLOCKER]` phải pass trước khi trả output | Phần VIII |

---

## 📌 PROJECT SCOPE

| Mục | Giá trị |
|---|---|
| **Framework backend** | Laravel 12+ (Blade template engine) |
| **Build tool** | Vite 5+ (dùng `@vite()` directive) |
| **CSS approach** | Utility-first custom class (không dùng Tailwind) |
| **JS approach** | Vanilla ES Modules, không framework frontend |
| **Ngôn ngữ UI** | Tiếng Việt |
| **Responsive** | Mobile-first, breakpoint `md` = 768px, `lg` = 1024px |

---

<a name="design-system"></a>
## I. DESIGN SYSTEM — MÀU & TYPOGRAPHY

### 1.1 — Design Token Reference

#### Color Token (Light Mode)

| Token | Dùng cho | Tham khảo |
|---|---|---|
| `--sg-text-primary` | Text chính, heading | #1a1a1a |
| `--sg-text-sub` | Text phụ, label | #555555 |
| `--sg-text-muted` | Text mờ, placeholder | #888888 |
| `--sg-text-link` | Link, anchor | #1a6ef5 |
| `--sg-brand` | Brand primary (giá tiền, CTA) | #e8473f |
| `--sg-bg-surface` | Nền card / panel | #ffffff |
| `--sg-border-base` | Đường kẻ mặc định | #e0e0e0 |
| `--sg-color-danger-50/400/500` | Lỗi | #fff0f0 / #e53e3e / #c53030 |
| `--sg-color-success-400/500` | Thành công | #38a169 / #276749 |

#### Color Token (Dark Mode)

| Token | Giá trị dark |
|---|---|
| `--sg-text-primary` | #f0f0f0 |
| `--sg-text-sub` | #a0a0a0 |
| `--sg-text-muted` | #666666 |
| `--sg-text-link` | #5b9eff |
| `--sg-brand` | #ff6b63 |
| `--sg-bg-surface` | #1e1e1e |
| `--sg-border-base` | #333333 |
| `--sg-color-danger-400/500` | #fc6868 / #ff4d4d |
| `--sg-color-success-400/500` | #4ade80 / #22c55e |

#### Spacing Scale

| Token | Giá trị |
|---|---|
| `--sg-space-1` | 4px |
| `--sg-space-2` | 8px |
| `--sg-space-3` | 12px |
| `--sg-space-4` | 16px |
| `--sg-space-5` | 20px |
| `--sg-space-6` | 24px |
| `--sg-space-8` | 32px |

#### Typography

| Token | Giá trị |
|---|---|
| `--sg-text-xs` | 11px |
| `--sg-text-sm` | 13px |
| `--sg-text-base` | 15px |
| `--sg-text-md` | 16px |
| `--sg-text-lg` | 18px |
| `--sg-text-xl` | 20px |
| `--sg-text-2xl` | 24px |
| `--sg-weight-normal` | 400 |
| `--sg-weight-semibold` | 600 |
| `--sg-weight-bold` | 700 |
| `--sg-leading-normal` | 1.5 |

#### Border & Radius

| Token | Giá trị |
|---|---|
| `--sg-radius-sm` | 4px |
| `--sg-radius-md` | 8px |
| `--sg-radius-lg` | 12px |
| `--sg-radius-xl` | 16px |

### 1.2 — Color Strategy: XANH DOMINANT

```
XANH = Chủ yếu (Primary, Headers, Buttons, Icons, Accents)
TRẮNG = Neutral/Background (Cards, Forms, Content areas)
```

**Màu XANH dùng ở:**
```
✅ Header/Navbar background (100% width)
✅ Hero Section (GRADIENT XANH #003580 → #0066cc)
✅ Primary Buttons & CTAs
✅ Links & Anchors
✅ Checkboxes & Radios (checked state)
✅ Icons (primary & secondary)
✅ Section headers & dividers
✅ Loading spinners & progress bars
✅ Hover states & accents
```

**Màu TRẮNG/Neutral dùng ở:**
```
✅ Card backgrounds
✅ Form input backgrounds
✅ Main content area
✅ Modal bodies
✅ Section backgrounds
```

### 1.3 — Typography Scale

#### Heading Styles (XANH TEXT — var(--sg-text-primary))

```
H1: 32-40px | 700 weight | Line-height 1.2
H2: 26-28px | 700 weight | Line-height 1.3
H3: 20-22px | 600 weight | Line-height 1.4
H4: 16-18px | 600 weight | Line-height 1.4
H5: 14-16px | 600 weight | Line-height 1.5
H6: 12-14px | 600 weight | Line-height 1.5
```

#### Body Text

```
Large: 16px | 400 weight | Line-height 1.6
Body: 14px | 400 weight | Line-height 1.6
Small: 12px | 400 weight | Line-height 1.5
Caption: 11px | 400 weight | Line-height 1.4
```

#### Special Text

```
Price: 24px | 700 weight | 'Courier New' | Color var(--sg-brand)
Original Price: 14px | strikethrough | Color var(--sg-text-muted)
Discount %: 12px | 700 weight | Color var(--sg-color-danger-500)
Link text: Color var(--sg-text-link) | Hover darker | Underline on hover
```

---

<a name="layout-components"></a>
## II. LAYOUT & COMPONENTS

### 2.1 — Header / Navigation Bar (XANH DOMINANT)

```
┌═══════════════════════════════════════════════════════════┐
│ [🏨 LOGO] [🔍 Search] [USD ▼] [❤️ Wishlist] [👤 User ▼]  │
│ BACKGROUND: var(--sg-brand) | TEXT: Trắng                │
└═══════════════════════════════════════════════════════════┘

STYLING:
- Height: 70px (desktop), 60px (mobile)
- Background: var(--sg-brand) (#e8473f light / #ff6b63 dark)
- Text color: Trắng #FFFFFF
- Position: Sticky, z-index: 100
- Logo: Trắng, font-weight: 700, font-size: 24px
- Icons: Trắng, Hover: Background var(--sg-bg-surface), Rounded: 6px
- Dropdown hover: Background darker shade
```

### 2.2 — Hero / Search Section (GRADIENT XANH)

```
BACKGROUND: Linear #003580 → #0066cc (right)
HEIGHT: 400px (desktop), 300px (mobile)
PADDING: 60px 40px

📝 HEADLINE: Trắng #FFFFFF, 32px, bold
📝 SUBHEADING: Xanh nhạt / Trắng 80%, 16px

Search Box:
- Background: var(--sg-bg-surface)
- Border: 1px solid var(--sg-border-base)
- Focus: Border var(--sg-text-link), shadow 0 0 0 3px rgba(26,110,245,0.1)
- Button: var(--sg-brand) background, Trắng text
```

### 2.3 — Filter Sidebar (XANH ACCENTS)

```
Header: H3 color var(--sg-text-primary)
Background: var(--sg-bg-surface)
Padding: 20px, Border-radius: var(--sg-radius-md)
Checkboxes checked: var(--sg-text-link) background
Hover states: Background var(--sg-border-base)
"Show more" links: Color var(--sg-text-link)
Dividers: var(--sg-border-base)
```

### 2.4 — Hotel Card

```
LAYOUT:
┌─────────────────────────────────────────┐
│ ┌──────────────────────────────────┐    │
│ │ [Hotel Image 16:9]           ❤️  │    │
│ └──────────────────────────────────┘    │
│                                         │
│ ⭐⭐⭐⭐⭐ 4.8 (320 reviews)           │
│ 🏨 Hotel Name                           │
│ 📍 Location - District                  │
│                                         │
│ ─── PRICE SECTION ───                   │
│ Was: 2,500,000đ (strikethrough)         │
│ Now: 1,800,000đ (var(--sg-brand))       │
│ [SALE BADGE] -27% OFF                   │
│                                         │
│ [View Details →] [Book Now]             │
└─────────────────────────────────────────┘

STYLING:
- Card background: var(--sg-bg-surface)
- Border-radius: var(--sg-radius-lg)
- Box-shadow: 0 2px 8px rgba(0,0,0,0.08)
- Hover: Box-shadow: 0 8px 20px rgba(0,0,0,0.15)
- Hover: Border-top: 3px solid var(--sg-brand)
- Hover transform: translateY(-4px)
- Rating stars: #FFAD33 (vàng)
- Hotel name: Color var(--sg-text-primary), 18px bold
- Price: Color var(--sg-brand), 22px bold
- Links: Color var(--sg-text-link)
- Wishlist heart: Red on hover
```

### 2.5 — Button Variants

```
PRIMARY BUTTON:
- Background: var(--sg-brand)
- Text: Trắng
- Padding: 12px 24px
- Border-radius: var(--sg-radius-sm)
- Font-weight: var(--sg-weight-semibold)
- Hover: Darker shade of var(--sg-brand)
- Transition: 0.3s ease

SECONDARY BUTTON:
- Background: var(--sg-border-base)
- Text: var(--sg-text-primary)
- Border: 1px var(--sg-text-link)
- Hover: Background var(--sg-bg-surface)

GHOST BUTTON:
- Background: Transparent
- Text: var(--sg-text-link)
- Border: 2px var(--sg-text-link)
- Hover: Background var(--sg-border-base)

DANGER BUTTON:
- Background: var(--sg-color-danger-500)
- Text: Trắng
- Hover: var(--sg-color-danger-400)
```

### 2.6 — Input Fields

```
STANDARD INPUT:
- Border: 1px solid var(--sg-border-base)
- Border-radius: var(--sg-radius-sm)
- Padding: 10px 14px
- Focus border: var(--sg-text-link) 2px
- Focus shadow: 0 0 0 3px rgba(26,110,245,0.1)
- Placeholder: Color var(--sg-text-muted)

ERROR STATE (is-error class):
- Border-color: var(--sg-color-danger-500)
- Background: var(--sg-color-danger-50)
- aria-invalid="true"
- aria-describedby="[error-id]"

DISABLED STATE:
- Background: var(--sg-border-base)
- Color: var(--sg-text-muted)
- Cursor: not-allowed
```

### 2.7 — Rating Component

```
⭐⭐⭐⭐⭐ 4.8/5.0 (XANH text — var(--sg-text-link))
(320 reviews) — XANH link var(--sg-text-link)

Stars:
- Filled: #FFAD33 (vàng)
- Empty: var(--sg-border-base)

Rating Bars:
- Bar color: var(--sg-text-link)
- Background: var(--sg-border-base)
```

### 2.8 — Modal / Dialog

```
STRUCTURE:
┌─────────────────────────────────┐
│ Modal Title              [✕]    │ (Close: var(--sg-text-link) icon)
├─────────────────────────────────┤
│ [Modal Body Content]            │
├─────────────────────────────────┤
│ [Cancel] [Confirm/Action]       │
│          (var(--sg-brand) button)
└─────────────────────────────────┘

STYLING:
- Overlay: rgba(0,0,0,0.5)
- Content BG: var(--sg-bg-surface)
- Border-radius: var(--sg-radius-lg)
- Box-shadow: 0 10px 40px rgba(0,0,0,0.2)
- Padding: 32px
```

### 2.9 — Accordion / Collapse

```
🎯 KHÔNG dùng <details>/<summary> native HTML
💡 Dùng template sg-accordion với animation height

STRUCTURE:
<div class="sg-accordion" data-sg-accordion>
  <button class="sg-accordion__trigger" aria-expanded="false">
    <span class="sg-accordion__title">Câu hỏi</span>
    <span class="sg-accordion__icon" aria-hidden="true">▾</span>
  </button>
  <div class="sg-accordion__panel" hidden role="region">
    <div class="sg-accordion__body">Trả lời</div>
  </div>
</div>

STYLING:
- Trigger: Padding var(--sg-space-4), no background, cursor pointer
- Trigger hover: Title color → var(--sg-text-link)
- Icon: Rotate 180deg khi mở (transition 0.2s)
- Panel: Height animate từ 0 → scrollHeight (0.2s ease)
```

---

<a name="technical-impl"></a>
## III. TECHNICAL IMPLEMENTATION

### 3.1 — Hard Rules (KHÔNG được bỏ qua)

| # | Quy tắc | Scope | Hành động |
|---|---|---|---|
| H1 | **Tra Bảng IV.1 trước** khi viết bất kỳ `style="..."` nào **(→ xem ngoại lệ §H1-ex)** | Blade template | Xóa inline style, thay class |
| H2 | Input lỗi **phải có** `is-error` + `aria-invalid="true"` + `aria-describedby` | Blade + CSS | Không được bỏ sót |
| H3 | SgDark apply **phải là inline script trong `<head>`**, không `src` hay `defer` | Blade layout | Chép block từ VII.2 |
| H4 | `.sg-field-error` **phải có** `role="alert"` | Blade | Thêm attribute |
| H5 | Empty Search **không dùng** `sg-error-page` | Blade | Dùng `sg-empty` + giữ navbar |
| H6 | Margin **phải dùng** `.sg-mb-*` / `.sg-mt-*` | Blade template | Tra Bảng IV.1 |

### 3.2 — Ngoại lệ cho H1 (Inline style được phép)

| Trường hợp | Lý do | Ví dụ |
|---|---|---|
| **Skeleton placeholder** | Width/height động | `style="width:60%;height:20px"` |
| **Giá trị tính toán PHP/JS** | Không cố định compile time | `style="width:{{ $percent }}%"` |
| **CSS custom property override** | Ghi đè token cục bộ | `style="--sg-radius-md: 0"` |

> **Khi dùng ngoại lệ:** Thêm comment `{{-- §H1-ex: [lý do] --}}` cạnh dòng.

---

<a name="utility-class"></a>
## IV. UTILITY CLASS REFERENCE

### 4.1 — Bảng Utility Class (thay thế inline style)

| Inline style | Class thay thế |
|---|---|
| `display:flex;align-items:center;gap:…` | `.sg-flex-center` + `.sg-gap-{n}` |
| `display:flex;justify-content:space-between` | `.sg-flex-between` |
| `display:flex;flex-wrap:wrap` | `.sg-flex-wrap` |
| `display:grid;grid-template-columns:1fr 1fr` | `.sg-grid-2` |
| `font-size:var(--sg-text-xs);color:var(--sg-text-muted)` | `.sg-text-xs-muted` |
| `font-size:var(--sg-text-sm);color:var(--sg-text-sub)` | `.sg-text-sm-sub` |
| `font-weight:var(--sg-weight-semibold)` | `.sg-semibold` |
| `font-weight:var(--sg-weight-bold)` | `.sg-bold` |
| `padding:var(--sg-space-3) var(--sg-space-4)` | `.sg-px-4.sg-py-3` |
| `border-top:1px solid var(--sg-border-base)` | `.sg-divider-top` |
| `text-align:center` | `.sg-text-center` |
| `overflow:hidden` | `.sg-overflow-hidden` |
| `cursor:pointer` | `.sg-cursor-pointer` |
| `width:100%` | `.sg-w-full` |
| `margin-bottom:var(--sg-space-*)`  | `.sg-mb-{1,2,3,4,6}` |
| `margin-top:var(--sg-space-*)`     | `.sg-mt-{2,3,4,6}` |
| `margin-inline:auto` | `.sg-mx-auto` |
| `justify-content:center` (flex) | `.sg-justify-center` |
| `padding-top:var(--sg-space-2)` | `.sg-pt-2` |
| `background:var(--sg-bg-surface);border-radius:var(--sg-radius-md);padding:var(--sg-space-4)` | `.sg-surface-block` |

### 4.2 — CSS Definitions (utilities.css)

```css
/* Layout */
.sg-flex-center    { display: flex; align-items: center; }
.sg-flex-between   { display: flex; align-items: center; justify-content: space-between; }
.sg-flex-baseline  { display: flex; align-items: baseline; }
.sg-flex-col       { display: flex; flex-direction: column; }
.sg-flex-wrap      { display: flex; flex-wrap: wrap; }
.sg-grid-2         { display: grid; grid-template-columns: 1fr 1fr; }
.sg-justify-center { justify-content: center; }
.sg-mx-auto        { margin-inline: auto; }

/* Sizing & overflow */
.sg-w-full         { width: 100%; }
.sg-overflow-hidden{ overflow: hidden; }
.sg-cursor-pointer { cursor: pointer; }

/* Gap shortcuts */
.sg-gap-1 { gap: var(--sg-space-1); }
.sg-gap-2 { gap: var(--sg-space-2); }
.sg-gap-3 { gap: var(--sg-space-3); }
.sg-gap-4 { gap: var(--sg-space-4); }
.sg-gap-6 { gap: var(--sg-space-6); }

/* Padding shortcuts */
.sg-p-3  { padding: var(--sg-space-3); }
.sg-p-4  { padding: var(--sg-space-4); }
.sg-px-4 { padding-inline: var(--sg-space-4); }
.sg-py-3 { padding-block: var(--sg-space-3); }
.sg-pt-2 { padding-top: var(--sg-space-2); }

/* Margin shortcuts — BẮT BUỘC thay vì inline margin */
.sg-mb-1 { margin-bottom: var(--sg-space-1); }
.sg-mb-2 { margin-bottom: var(--sg-space-2); }
.sg-mb-3 { margin-bottom: var(--sg-space-3); }
.sg-mb-4 { margin-bottom: var(--sg-space-4); }
.sg-mb-6 { margin-bottom: var(--sg-space-6); }
.sg-mt-2 { margin-top: var(--sg-space-2); }
.sg-mt-3 { margin-top: var(--sg-space-3); }
.sg-mt-4 { margin-top: var(--sg-space-4); }
.sg-mt-6 { margin-top: var(--sg-space-6); }

/* Typography */
.sg-text-xs-muted { font-size: var(--sg-text-xs); color: var(--sg-text-muted); }
.sg-text-sm-sub   { font-size: var(--sg-text-sm); color: var(--sg-text-sub); }
.sg-semibold      { font-weight: var(--sg-weight-semibold); }
.sg-bold          { font-weight: var(--sg-weight-bold); }
.sg-text-center   { text-align: center; }

/* Dividers */
.sg-divider-top    { border-top:    1px solid var(--sg-border-base); }
.sg-divider-bottom { border-bottom: 1px solid var(--sg-border-base); }
.sg-divider-right  { border-right:  1px solid var(--sg-border-base); }

/* Surface block */
.sg-surface-block {
  background: var(--sg-bg-surface);
  border-radius: var(--sg-radius-md);
  padding: var(--sg-space-4);
}

/* Wishlist button */
.sg-wishlist-btn {
  width: 34px; height: 34px;
  border-radius: 9999px; padding: 0;
  background: rgba(255,255,255,0.9);
  border: none; cursor: pointer;
  color: var(--sg-text-sub);
  transition: color .15s, background .15s;
}
.sg-wishlist-btn:hover { 
  background: #fff; 
  color: var(--sg-color-danger-400); 
}

html.sg-dark .sg-wishlist-btn { 
  background: rgba(0,0,0,0.55); 
  color: var(--sg-text-muted); 
}
html.sg-dark .sg-wishlist-btn:hover { 
  background: rgba(0,0,0,0.75); 
  color: var(--sg-color-danger-400); 
}
```

### 4.3 — Ví dụ SAI → ĐÚNG

**❌ SAI — viết inline style dù class đã có:**
```blade
<div style="display:flex; align-items:center; gap:8px">
  <span style="font-size:var(--sg-text-xs); color:var(--sg-text-muted)">
    2 người lớn
  </span>
</div>
```

**✅ ĐÚNG:**
```blade
<div class="sg-flex-center sg-gap-2">
  <span class="sg-text-xs-muted">2 người lớn</span>
</div>
```

---

**❌ SAI — hardcode margin:**
```blade
<div style="margin-bottom:var(--sg-space-4)">...</div>
```

**✅ ĐÚNG:**
```blade
<div class="sg-mb-4">...</div>
```

---

<a name="form-states"></a>
## V. FORM STATES & ACCESSIBILITY

### 5.1 — Input Error State (bắt buộc H2 + H4)

```blade
<div class="sg-form-group">
  <label for="email-input" class="sg-label">Email</label>
  <input
    type="email"
    id="email-input"
    name="email"
    class="sg-input is-error"
    aria-invalid="true"
    aria-describedby="email-error"
    value="{{ old('email') }}"
  >
  <div id="email-error" class="sg-field-error" role="alert">
    Email không hợp lệ hoặc đã được sử dụng
  </div>
</div>
```

**CSS:**
```css
.sg-input.is-error {
  border-color: var(--sg-color-danger-500);
  background-color: var(--sg-color-danger-50);
}

.sg-field-error {
  color: var(--sg-color-danger-500);
  font-size: var(--sg-text-xs);
  margin-top: var(--sg-space-2);
  /* role="alert" is required */
}
```

### 5.2 — Loading State

```blade
<button type="submit" class="sg-btn sg-btn-primary" data-loading-text="Đang xử lý...">
  Gửi
</button>
```

**CSS:**
```css
.sg-btn.is-loading {
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none;
  position: relative;
}

.sg-btn.is-loading::after {
  content: '';
  position: absolute;
  width: 16px; height: 16px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin .8s linear infinite;
  margin-left: 8px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (prefers-reduced-motion: reduce) {
  .sg-btn.is-loading::after {
    animation: none;
  }
}
```

### 5.3 — Skeleton Loading (8.4)

```blade
<div class="sg-card">
  <div class="sg-flex-center sg-gap-4">
    <!-- Ảnh trái -->
    <div class="sg-skeleton-img">
      <div class="sg-skeleton" style="width:100px;height:100px"></div>
    </div>
    
    <!-- Content phải -->
    <div class="sg-flex-col sg-gap-2" style="flex:1">
      <div class="sg-skeleton" style="width:60%;height:20px"></div>
      <div class="sg-skeleton" style="width:80%;height:16px"></div>
      <div class="sg-skeleton" style="width:40%;height:14px"></div>
    </div>
  </div>
</div>
```

**CSS:**
```css
.sg-skeleton {
  background: linear-gradient(90deg, 
    var(--sg-border-base) 25%, 
    var(--sg-bg-surface) 50%, 
    var(--sg-border-base) 75%);
  background-size: 200% 100%;
  animation: pulse 1.5s infinite;
  border-radius: var(--sg-radius-sm);
}

@keyframes pulse {
  0%, 100% { background-position: 200% 0; }
  50% { background-position: -200% 0; }
}

@media (prefers-reduced-motion: reduce) {
  .sg-skeleton { animation: none; }
}
```

---

<a name="page-flows"></a>
## VI. PAGE FLOWS

### 6.1 — Home Page

```
[XANH HEADER - Fixed]
[GRADIENT HERO - var(--sg-brand)→#0066cc]
────────────────────────────────────────
[Quick Navigation Tabs]
  🏆 Popular | 🔥 Hot Deals | 📍 Near You | 🕐 Recent

[Featured Hotels Grid]
  4 columns (responsive), Cards with XANH accents

[PROMO BANNER]
  GRADIENT, CTA button var(--sg-brand)

[Trust Badges]
  ✓ Giá tốt nhất | ✓ 2M+ khách | ✓ Hỗ trợ 24/7

[Footer]
```

### 6.2 — Search Results Page

```
[XANH HEADER - Sticky with Search Bar]
────────────────────────────────────────
[Filter Sidebar 25%] | [Hotel Grid 75%]
  ⭐ Rating           3-4 columns desktop
  💰 Price            [Sort Dropdown]
  🏠 Type             Hotel cards

[Pagination]

[Footer]
```

### 6.3 — Hotel Detail Page

```
[XANH HEADER - Sticky]
────────────────────────────────────────
[Image Gallery]           [Booking Sidebar - STICKY]
Main image + Thumbnails   💰 Total price
Active: XANH border       📅 Check-in/out
                         🛏️  Guests
[Hotel Info & Ratings]    [Reserve Button]
⭐ Stars (VÀNG)           ✓ Free Cancellation
H2 Name (var(--sg-text-primary))
📍 Location
🏷️ Amenities (Icons XANH)

[Room Selection Cards]
  Card border: var(--sg-border-base)
  Active: Border var(--sg-text-link) 3px
  Price: var(--sg-brand)

[Reviews Section]
  Rating number: var(--sg-text-link), 28px
  Rating bars: var(--sg-text-link) filled

[Map Section]
  Header: var(--sg-text-primary)
  Pins: var(--sg-text-link)

[Footer]
```

### 6.4 — Empty Search Page (⚠️ NOT error page)

```
[XANH HEADER - Sticky with Search Bar] ← GIỮ NAVBAR
[Search Bar Highlighted]

[ICON] "Không tìm thấy khách sạn"

"Hãy thử:"
- Thay đổi ngày
- Thay đổi địa điểm
- Xóa filter

[Updated Search Button]

[Footer]
```

> **⚠️ H5:** Không dùng `sg-error-page` ở đây. Giữ navbar để người dùng search lại.

### 6.5 — Checkout Page

```
[XANH HEADER]
────────────────────────────────────────
Progress: [1. Hotel ✓] ──XANH──► [2. Info ●] ──GRAY──► [3. Payment ○]

[Left 60%]
THÔNG TIN KHÁCH (H2: var(--sg-text-primary))
  Full Name input (focus: var(--sg-text-link) border)
  Email input
  Phone input
  ☑ Terms (checkbox XANH)

THANH TOÁN (H2)
  ○ Credit/Debit Card
  ○ VNPay
  ○ Momo
  [PAY SECURE] Button var(--sg-brand)

[Right 40% - STICKY]
ĐƠN HÀNG CỦA BẠN
  Hotel name
  Room type × nights
  ────────────
  Subtotal
  Tax
  Discount
  ════════════
  TOTAL (var(--sg-brand), bold)
  
  🎉 Save X%
  ✓ Free breakfast
```

### 6.6 — FAQ Page

```
[XANH HEADER]
[Breadcrumb: Home › Help › FAQ]

"Câu hỏi thường gặp" (H1)
[Search FAQ]

Nhóm câu hỏi — mỗi nhóm là 1 block:
  H2 "Đặt phòng"
  sg-accordion × N câu hỏi

  H2 "Thanh toán"
  sg-accordion × N

  ...

[Footer]
```

**Accordion Template (xem VII.3 để xem JS):**

```blade
<div class="sg-accordion" data-sg-accordion>
  <button class="sg-accordion__trigger sg-flex-between sg-w-full"
          aria-expanded="false"
          aria-controls="faq-{{ $faq->id }}"
          id="faq-btn-{{ $faq->id }}"
          type="button">
    <span class="sg-accordion__title sg-semibold">{{ $faq->question }}</span>
    <span class="sg-accordion__icon" aria-hidden="true">▾</span>
  </button>

  <div class="sg-accordion__panel"
       id="faq-{{ $faq->id }}"
       role="region"
       aria-labelledby="faq-btn-{{ $faq->id }}"
       hidden>
    <div class="sg-accordion__body sg-text-sm-sub sg-leading sg-p-4">
      {!! $faq->answer_html !!}
    </div>
  </div>
</div>
```

---

<a name="js-impl"></a>
## VII. JS IMPLEMENTATION

### 7.1 — SgDark Apply (inline script in `<head>`)

> **H3 RULE:** Phải là inline script, KHÔNG có `src` hay `defer`.

```html
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StayGo</title>
  
  <!-- SgDark apply — PHẢI inline, không async/defer -->
  <script>
    (function() {
      const theme = localStorage.getItem('sg-theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const isDark = theme === 'dark' || (theme === null && prefersDark);
      
      if (isDark) {
        document.documentElement.classList.add('sg-dark');
      }
    })();
  </script>
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

### 7.2 — App.js Import Order (phải đúng thứ tự)

```javascript
// resources/js/app.js

// 1️⃣ UTILS first
import { debounce, scrollToTop } from './utils/helpers';

// 2️⃣ TOAST & MODAL (cần dùng trong nhiều chỗ)
import SgToast from './sg/sg-toast';
import SgModal from './sg/sg-modal';

// 3️⃣ COMPONENTS
import SgCounter from './sg/sg-counter';
import SgAccordion from './sg/sg-accordion';
import SgDropdown from './sg/sg-dropdown';

// 4️⃣ PAGE-SPECIFIC
import SgCountdown from './sg/sg-countdown';

// 5️⃣ DARK MODE
import SgDark from './sg/sg-dark';

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  SgToast.init();
  SgModal.init();
  SgCounter.init();
  SgAccordion.init();
  SgDropdown.init();
  SgCountdown.init();
  SgDark.init(); // DOMContentLoaded only — register toggle, don't re-apply theme
});
```

### 7.3 — SgAccordion.js (Accordion with height animation)

```javascript
// resources/js/sg/sg-accordion.js

const SgAccordion = {
  init() {
    document.querySelectorAll('[data-sg-accordion]').forEach(el => {
      const trigger = el.querySelector('.sg-accordion__trigger');
      const panel   = el.querySelector('.sg-accordion__panel');
      if (!trigger || !panel) return;

      trigger.addEventListener('click', () => {
        const isOpen = el.hasAttribute('data-open');

        if (isOpen) {
          // CLOSE
          panel.style.height = panel.scrollHeight + 'px';
          requestAnimationFrame(() => {
            panel.style.transition = 'height .2s ease';
            panel.style.height = '0';
          });
          panel.addEventListener('transitionend', () => {
            panel.setAttribute('hidden', '');
            panel.style.height = '';
            panel.style.transition = '';
          }, { once: true });
          el.removeAttribute('data-open');
          trigger.setAttribute('aria-expanded', 'false');
        } else {
          // OPEN
          panel.removeAttribute('hidden');
          panel.style.height = '0';
          requestAnimationFrame(() => {
            panel.style.transition = 'height .2s ease';
            panel.style.height = panel.scrollHeight + 'px';
          });
          panel.addEventListener('transitionend', () => {
            panel.style.height = '';
            panel.style.transition = '';
          }, { once: true });
          el.setAttribute('data-open', '');
          trigger.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }
};

export default SgAccordion;
```

### 7.4 — SgDark.js (Theme toggle)

```javascript
// resources/js/sg/sg-dark.js

const SgDark = {
  init() {
    const toggle = document.querySelector('[data-sg-dark-toggle]');
    if (!toggle) return;

    toggle.addEventListener('click', () => {
      const html = document.documentElement;
      const isDark = html.classList.contains('sg-dark');
      
      if (isDark) {
        html.classList.remove('sg-dark');
        localStorage.setItem('sg-theme', 'light');
      } else {
        html.classList.add('sg-dark');
        localStorage.setItem('sg-theme', 'dark');
      }
    });
  }
};

export default SgDark;
```

---

<a name="checklist"></a>
## VIII. COMPREHENSIVE CHECKLIST

> **Cách dùng:** Chạy checklist này sau khi sinh code.
>
> **Ưu tiên sửa:**
> - `[BLOCKER]` — Không pass = **không được trả code**
> - `[WARN]` — Không pass = trả code nhưng **ghi chú TODO**

### 8.1 — Utility Class (Phần IV)

| # | Mức | Kiểm tra | Hành động nếu fail |
|---|---|---|---|
| U1 | `[BLOCKER]` | Không có inline style lặp lại ≥ 2 lần? | Tra Bảng IV.1, thay bằng class |
| U2 | `[WARN]` | Dùng `.sg-surface-block` thay vì `style="background:..."`? | Thay bằng class |
| U3 | `[WARN]` | Dùng `.sg-wishlist-btn` không phải button style inline? | Thay bằng class |
| U4 | `[BLOCKER]` | `.sg-input--bare` cho input trong date-grid? | Thêm class |
| U5 | `[WARN]` | `.sg-btn-link` cho anchor text button? | Thêm class |
| U6 | `[BLOCKER]` | Dùng `.sg-mb-*` / `.sg-mt-*` không phải `style="margin-*"`? | Tra Bảng IV.1 |
| U7 | `[WARN]` | Dùng `.sg-overflow-hidden`, `.sg-cursor-pointer`, `.sg-w-full`? | Thêm class |

### 8.2 — Page Flow (Phần VI)

| # | Mức | Kiểm tra | Hành động nếu fail |
|---|---|---|---|
| P1 | `[WARN]` | Error page có 404 / 500 / 503 variant? | Tạo file còn thiếu |
| P2 | `[BLOCKER]` | Empty search giữ navbar + không dùng `sg-error-page`? | Xóa error-page, giữ navbar |
| P3 | `[WARN]` | Modal confirm dùng `sg-modal__confirm-icon--danger`? | Thêm class |
| P4 | `[WARN]` | `sg-promo-card` dùng template, không tự bịa? | Chép từ VI.1 |
| P5 | `[WARN]` | `sg-avatar` có fallback initials? | Thêm fallback |
| P6 | `[BLOCKER]` | `sg-accordion` KHÔNG dùng `<details>`/`<summary>`? | Thay bằng template VI.6 |

### 8.3 — Form & Accessibility (Phần V)

| # | Mức | Kiểm tra | Hành động nếu fail |
|---|---|---|---|
| F1 | `[BLOCKER]` | Input lỗi có đủ `is-error` + `aria-invalid="true"` + `aria-describedby`? | Thêm đủ ba attribute |
| F2 | `[BLOCKER]` | `.sg-field-error` có `role="alert"`? | Thêm attribute |
| F3 | `[WARN]` | Skeleton: ảnh trái, content phải? | Đổi layout |
| F4 | `[WARN]` | Button submit có `data-loading-text` và `.is-loading`? | Thêm attribute + CSS |
| F5 | `[BLOCKER]` | `.is-loading` animation có `@media (prefers-reduced-motion: reduce)`? | Thêm `@media` block |

### 8.4 — JS Implementation (Phần VII)

| # | Mức | Kiểm tra | Hành động nếu fail |
|---|---|---|---|
| J1 | `[BLOCKER]` | SgDark apply là inline script trong `<head>`, KHÔNG `src`/`defer`? | Chép block từ VII.1 |
| J2 | `[WARN]` | `app.js` import đúng thứ tự: utils → toast → modal → counter → init? | Sắp xếp lại theo VII.2 |
| J3 | `[BLOCKER]` | `DOMContentLoaded` bao bọc tất cả `.init()` call? | Wrap vào event listener |
| J4 | `[WARN]` | `SgDark.init()` ở trong `DOMContentLoaded` (chỉ toggle, không re-apply)? | Tách logic |
| J5 | `[WARN]` | `sg-accordion.js` có trong import list? | Thêm dòng import |

### 8.5 — Color & Dark Mode

| # | Mức | Kiểm tra | Hành động nếu fail |
|---|---|---|---|
| C1 | `[BLOCKER]` | Dùng token color, NOT hardcode hex? | Tra Phần I.1, thay bằng token |
| C2 | `[WARN]` | Dark mode CSS selectors dùng `html.sg-dark`? | Thêm CSS selector |
| C3 | `[WARN]` | Dark mode button/icon color override? | Thêm dark selector |

---

## 📌 WHEN IN DOUBT

**Không chắc token/class nào?** → Tra lại:
1. **Color:** Phần I.1 (Design Token)
2. **Spacing/Padding:** Phần IV.1 (Utility Class)
3. **Typography:** Phần I.3 (Typography Scale)
4. **Form State:** Phần V (Form States)
5. **Page Layout:** Phần VI (Page Flows)

**Nếu vẫn không tìm được:**
```javascript
/* TODO: cần giá trị token cho [mục đích cụ thể] */
```

> Đừng tự bịa. Ghi `/* TODO */` và hỏi lại.

---

## 🔍 QUICK REFERENCE — GHI NHỚ NHANH

```
✅ RULES CỐT LÕI (HARD RULES):
  1. Không inline style nếu có class → Tra Bảng IV.1
  2. Input lỗi: is-error + aria-invalid + aria-describedby
  3. SgDark apply = inline script trong <head>
  4. .sg-field-error cần role="alert"
  5. Empty search = không dùng sg-error-page
  6. Margin/padding phải dùng .sg-mb-* / .sg-mt-*
  7. Chạy Checklist VIII trước submit

✅ STRUCTURE CHỦ ĐẠOSTRUCTURE CHỦ ĐẠO:
  Header (XANH) → Hero (GRADIENT) → Content (Cards) → Footer

✅ COLOR STRATEGY:
  XANH = Primary (buttons, headers, icons)
  TRẮNG = Background (cards, forms, surfaces)

✅ RESPONSIVE:
  Mobile-first, breakpoint md=768px, lg=1024px
```

---

**StayGo README v2.5**  
*Combine Design + Implementation Guide*  
**Status:** ✅ Ready for Development