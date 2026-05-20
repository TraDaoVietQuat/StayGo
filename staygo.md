# StayGo — Tài liệu tổng hợp project

> Nền tảng đặt phòng khách sạn & resort tại 4 điểm đến Việt Nam:
> **Đà Lạt · Nha Trang · Vũng Tàu · Đà Nẵng**

---

## 1. Ngôn ngữ & Công nghệ

| Lớp | Công nghệ |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Admin Panel | Filament v3.3 |
| Frontend Build | Vite 7 + TailwindCSS 4 |
| Database | MySQL |
| Cache / Queue | Redis (predis ^3.4) |
| AI Chatbot | OpenAI PHP/Laravel (`openai-php/laravel`) |
| Social Auth | Laravel Socialite (Google, Facebook) |
| Image | Intervention Image Laravel |
| Mail | SMTP Gmail |
| Deploy | Railway (cloud) + XAMPP (local) |
| Testing | PHPUnit 11 |

---

## 2. Cấu trúc thư mục chính

```
StayGoLaravel/
├── app/
│   ├── Console/Commands/       # Artisan scheduled commands
│   ├── Filament/               # Admin panel resources & widgets
│   ├── Http/Controllers/       # Web controllers
│   ├── Mail/                   # Email templates (Mailable)
│   ├── Models/                 # Eloquent models
│   ├── Notifications/          # Laravel notifications
│   ├── Observers/              # Model observers
│   ├── Providers/              # Service providers
│   ├── Services/               # Business logic services
│   └── Traits/                 # Shared traits
├── config/
│   └── payment.php             # VNPay / MoMo / ZaloPay config
├── database/
│   ├── migrations/             # 40+ migration files
│   └── seeders/                # Hotel, blog, image seeders
├── resources/views/
│   ├── auth/                   # Login, register, OTP, forgot password
│   ├── components/             # Chatbot, hotel-card, sidebar
│   ├── emails/                 # Email blade templates
│   ├── layouts/                # app.blade.php (navbar + footer)
│   └── pages/                  # Tất cả các trang frontend
├── routes/
│   └── web.php                 # Toàn bộ routes
└── public/assets/              # CSS, JS, images tĩnh
```

---

## 3. Models (Database)

| Model | Bảng | Mô tả |
|---|---|---|
| `User` | users | Tài khoản người dùng |
| `Location` | locations | Địa điểm (Đà Lạt, Nha Trang…) |
| `Hotel` | hotels | Khách sạn & resort |
| `HotelImage` | hotel_images | Gallery ảnh khách sạn |
| `Room` | rooms | Loại phòng trong khách sạn |
| `Booking` | bookings | Đơn đặt phòng |
| `Payment` | payments | Giao dịch thanh toán |
| `Review` | reviews | Đánh giá của người dùng |
| `BlogPost` | blog_posts | Bài viết cẩm nang |
| `SupportRequest` | support_requests | Ticket hỗ trợ |
| `SupportReply` | support_replies | Tin nhắn trong ticket |
| `Favorite` | favorites | Khách sạn yêu thích |
| `Promo` | promos | Mã giảm giá |
| `Place` | places | Địa điểm gần khách sạn (map) |
| `AuditLog` | audit_logs | Nhật ký hành động admin |

---

## 4. Controllers & Routes

### Public (không cần đăng nhập)
| URL | Controller | Chức năng |
|---|---|---|
| `/` | `HomeController@index` | Trang chủ |
| `/hotels` | `HotelController@index` | Danh sách khách sạn |
| `/hotels/{hotel}` | `HotelController@show` | Chi tiết khách sạn |
| `/hotels/{hotel}/availability` | `HotelController@availability` | Kiểm tra phòng trống |
| `/uu-dai` | `DealsController@index` | Trang ưu đãi |
| `/blog` | `BlogController@index` | Danh sách blog |
| `/blog/{blogPost}` | `BlogController@show` | Chi tiết bài viết |
| `/lien-he` | `SupportController@create/store` | Liên hệ / tạo ticket |
| `/chatbot` | `ChatbotController@chat` | API chatbot AI |
| `/promo/validate` | `PromoController@validate` | Kiểm tra mã giảm giá |
| `/dat-phong/{room}` | `BookingController@create` | Form đặt phòng |
| `/dat-phong` (POST) | `BookingController@store` | Xử lý đặt phòng |
| `/thanh-toan/{booking}` | `PaymentController@show/process` | Trang & xử lý thanh toán |

### Auth (chỉ khách — guest)
| URL | Chức năng |
|---|---|
| `/dang-nhap` | Đăng nhập thông thường |
| `/dang-ky` | Đăng ký tài khoản |
| `/quen-mat-khau` | Quên mật khẩu → gửi OTP |
| `/xac-thuc-otp` | Xác thực OTP |
| `/dat-lai-mat-khau` | Đặt lại mật khẩu mới |
| `/dang-nhap-otp` | Đăng nhập bằng OTP email |
| `/auth/google` | Đăng nhập Google OAuth |
| `/auth/facebook` | Đăng nhập Facebook OAuth |

### Authenticated (cần đăng nhập)
| URL | Chức năng |
|---|---|
| `/tai-khoan` | Hồ sơ cá nhân |
| `/dat-phong-cua-toi` | Lịch sử đặt phòng |
| `/dat-phong/{booking}/huy` | Hủy đặt phòng |
| `/dat-phong/{booking}/hoan-tien` | Yêu cầu hoàn tiền |
| `/dat-phong/{booking}/hoa-don` | Tải hóa đơn PDF |
| `/danh-gia` | Viết / sửa / xóa đánh giá |
| `/yeu-thich` | Danh sách yêu thích |
| `/yeu-thich/{hotel}` | Toggle yêu thích |
| `/ho-tro/{supportRequest}` | Xem ticket hỗ trợ |
| `/ho-tro/{supportRequest}/tra-loi` | Trả lời ticket |
| `/thong-bao/doc-het` | Đánh dấu đã đọc thông báo |

### Payment Webhooks (no CSRF)
| URL | Cổng |
|---|---|
| `/webhook/vnpay/ipn` + `/return` | VNPay |
| `/webhook/momo/ipn` + `/return` | MoMo |
| `/webhook/sepay` | SePay (bank transfer) |

---

## 5. Chức năng theo nhóm

### 5.1 Xác thực người dùng
- Đăng ký / đăng nhập bằng email + mật khẩu
- Đăng nhập bằng OTP (gửi qua email)
- Quên mật khẩu → xác thực OTP → đặt lại mật khẩu
- Đăng nhập / đăng ký qua **Google OAuth**
- Đăng nhập qua **Facebook OAuth**
- Xác minh email (signed URL)
- Bảo vệ: rate limiting, reCAPTCHA v3

### 5.2 Khách sạn & Resort
- Trang danh sách: lọc theo địa điểm, loại (khách sạn / resort), giá, điểm đánh giá, sắp xếp
- Trang chi tiết: gallery ảnh lightbox, tiện nghi, map Google, địa điểm lân cận
- Kiểm tra phòng trống theo ngày nhận/trả
- Badge: Weekend deal, giảm giá %, điểm đánh giá cao
- Hiển thị sao (1–5), ranking, review count

### 5.3 Đặt phòng
- Đặt phòng cho cả khách vãng lai (không cần login)
- Chọn loại lưu trú: qua đêm / 1 ngày
- Nhập thông tin khách, ngày ở, yêu cầu đặc biệt
- Đếm ngược 15 phút giữ giá
- Hiển thị cảnh báo phòng sắp hết
- Lịch sử đặt phòng: xem trạng thái, hủy, yêu cầu hoàn tiền
- Tải hóa đơn PDF

### 5.4 Thanh toán
| Phương thức | Trạng thái |
|---|---|
| VietQR (SePay bank transfer) | Hoạt động |
| VNPay | Sandbox (chờ cấu hình) |
| MoMo | Sandbox (chờ cấu hình) |
| ZaloPay | Cấu hình sẵn (chờ triển khai) |
| Thanh toán tại khách sạn | Hoạt động |
| Thẻ tín dụng (Visa/MC/JCB) | UI sẵn |

- Mã giảm giá (promo code) validate AJAX
- Webhook tự động xác nhận thanh toán

### 5.5 Đánh giá
- Viết đánh giá sau khi hoàn thành kỳ lưu trú
- Sửa / xóa đánh giá của mình
- Điểm tổng hợp: Nhân viên, Vị trí, Sạch sẽ, Tiện nghi, Đáng giá

### 5.6 Blog / Cẩm nang du lịch
- Danh sách bài viết lọc theo danh mục
- Chi tiết bài viết với rich content
- Gợi ý khách sạn liên quan cuối bài
- Bài viết liên quan

### 5.7 Ưu đãi & Mã giảm giá
- Trang ưu đãi `/uu-dai`: hiển thị deals theo địa điểm
- Deal cuối tuần (WEEKEND20 — giảm 20%)
- Ưu đãi khách hàng mới (giảm 10% — nhận qua QR)
- Đặt phòng sớm (EARLY15 — giảm 15%)
- Copy mã giảm giá một click

### 5.8 Yêu thích
- Toggle ❤️ yêu thích ngay trên thẻ khách sạn
- Trang danh sách yêu thích riêng

### 5.9 Hồ sơ cá nhân
- Cập nhật họ tên, email, số điện thoại
- Đổi mật khẩu
- Upload avatar
- Kết nối / hiển thị trạng thái Google OAuth
- Xác minh email

### 5.10 Hỗ trợ / Liên hệ
- Tạo ticket hỗ trợ với chủ đề (đặt phòng, thanh toán, hủy phòng, khiếu nại, khác)
- Xem thread trò chuyện ticket
- Trả lời ticket (cả user và admin)
- Trạng thái ticket: Chờ xử lý / Đang xử lý / Đã giải quyết / Đã đóng

### 5.11 AI Chatbot
- Widget nổi góc phải màn hình
- Tích hợp OpenAI GPT-3.5-turbo
- Câu hỏi nhanh preset: Còn phòng?, Giá phòng?, Chính sách hủy?, Hỗ trợ đặt phòng
- Online/offline indicator

### 5.12 Thông báo (Notifications)
- Thông báo khi booking được xác nhận
- Thông báo khi booking bị hủy
- Thông báo khi hoàn tiền
- Badge đếm thông báo chưa đọc trên navbar
- Đánh dấu đọc tất cả

### 5.13 Admin Panel (Filament)
- **Resources quản lý:** Hotels, Rooms, Bookings, Payments, Users, Reviews, Blog Posts, Support Requests, Promos, Locations, Places, Audit Logs
- **Widgets dashboard:**
  - Stats Overview (tổng doanh thu, booking, user)
  - Occupancy (tỷ lệ phòng)
  - Revenue Chart (biểu đồ doanh thu)
  - Top Hotels Chart
  - Revenue Ranking
  - Booking Status (phân bổ trạng thái)
  - Payment Method Chart
  - Recent Bookings (booking gần nhất)
  - Payment Stats
  - Booking Stats

### 5.14 Tác vụ nền (Artisan Commands)
| Command | Chức năng |
|---|---|
| `ExpirePendingBookings` | Hủy booking chờ quá hạn |
| `CompleteStays` | Tự động hoàn thành kỳ lưu trú đã qua |
| `ProcessRefunds` | Xử lý các yêu cầu hoàn tiền |
| `SendPreArrivalReminders` | Gửi email nhắc nhở trước ngày check-in |
| `PrecomputeAdminStats` | Cache sẵn thống kê cho admin |
| `ImportSqlData` | Import dữ liệu từ file SQL |

### 5.15 Email tự động
| Mail | Khi nào gửi |
|---|---|
| `BookingConfirmation` | Xác nhận đặt phòng thành công |
| `EmailVerification` | Xác minh địa chỉ email mới |
| `PreArrivalReminder` | Nhắc nhở trước ngày check-in |
| OTP email | Đăng nhập OTP / quên mật khẩu |

### 5.16 SEO
- Sitemap XML động tại `/sitemap.xml`

---

## 6. Giao diện — Các trang & nút bấm

### Trang chủ (`/`)
**Sections:** Hero, Trust strip, Story, Hotel showcase, Locations carousel, Service tabs, Weekend deals carousel, Testimonials carousel, Travel guide carousel

**Nút bấm:**
- `TÌM PHÒNG` — submit form tìm kiếm
- `Đặt phòng` — trên mỗi hotel showcase card
- `◀` / `▶` — điều hướng carousel

**Form tìm kiếm hero:**
- Tabs: Qua đêm / 1 ngày
- Input: Điểm đến / tên khách sạn (có dropdown gợi ý)
- Date picker: Ngày nhận phòng / Ngày trả phòng
- Guests popup: +/- số phòng, người lớn, trẻ em
- Bộ lọc: Đánh giá (7+/8+/9+) | Giá/đêm | Sắp xếp | Loại (Khách sạn / Resort)

---

### Danh sách khách sạn (`/hotels`)
**Nút bấm trên mỗi thẻ:**
- `Xem chi tiết`
- `❤️` (toggle yêu thích)
- `Đặt ngay`
- `Xóa bộ lọc`
- `Xem tất cả khách sạn` (empty state)

---

### Chi tiết khách sạn (`/hotels/{hotel}`)
**Navigation sticky:** Tổng quan | Tiện nghi | Phòng trống | Quy tắc | Đánh giá | `Đặt ngay →`

**Gallery:** Lightbox với ảnh đầy đủ, mũi tên điều hướng, thumbnail strip

**Phòng:** Mỗi loại phòng có:
- Carousel ảnh phòng (`◀` / `▶` / dots)
- `🔍 Xem chi tiết phòng` (modal)
- `Đặt ngay` hoặc `Hết phòng` (mỗi row)
- `Xem thêm X lựa chọn ▾`

**Sidebar:**
- `Chọn phòng & Đặt ngay`
- `🗺️ Mở Google Maps ↗`

**Đánh giá:** Form viết đánh giá (nếu đã ở) với nút `Gửi đánh giá`

---

### Đặt phòng (`/dat-phong/{room}`)
**Form fields:** Họ tên | Điện thoại (country code) | Email | Ngày nhận | Ngày trả | Yêu cầu đặc biệt (checkbox + textarea)

**Phương thức thanh toán (radio):** VietQR | Ví điện tử | Ngân hàng di động | Thẻ tín dụng | Tại khách sạn | VietinBank (disabled) | Trả góp (disabled)

**Nút bấm:**
- `Thêm mã` — áp dụng promo code
- `Thanh toán [phương thức]` — submit

---

### Thanh toán (`/thanh-toan/{booking}`)
**3 trạng thái:** Đã thanh toán | Đặt phòng thành công (chờ thanh toán) | Đang chờ xác nhận

**Nút bấm:**
- `Copy` mã đặt phòng
- `Xác nhận đã chuyển khoản` / `Mở ứng dụng MoMo` / `Thanh toán VNPay`
- `Xem đặt phòng của tôi`
- `Trang chủ`

---

### Lịch sử đặt phòng (`/dat-phong-cua-toi`)
**Mỗi booking có:**
- `Thanh toán` (nếu chưa thanh toán)
- `Hủy` (với confirm dialog)
- `✍️ Đánh giá` (nếu đã hoàn thành)
- `📄 Hóa đơn` (tải PDF)

---

### Hồ sơ (`/tai-khoan`)
**Nút bấm:**
- `Cập nhật ảnh`
- `Lưu thay đổi` (thông tin cá nhân)
- `Kết nối Google` / `Gửi xác minh`
- `Đổi mật khẩu`

---

### Trang ưu đãi (`/uu-dai`)
**Nút bấm:**
- `Tìm khách sạn ngay →`
- `COPY` mã giảm giá (WEEKEND20, EARLY15)
- `NHẬN ƯU ĐÃI NGAY` (new user)
- `XEM KHÁCH SẠN ÁP DỤNG`
- Filter tabs theo địa điểm

---

### Blog (`/blog`, `/blog/{slug}`)
**Nút bấm:**
- Filter tabs theo danh mục
- `Đọc tiếp →`
- `← Quay lại Cẩm nang`

---

### Liên hệ (`/lien-he`)
**Form fields:** Họ tên | Điện thoại | Email | Chủ đề (dropdown) | Nội dung

**Nút:** `📨 Gửi yêu cầu`

---

### Đăng nhập (`/dang-nhap`)
**Form:** Email | Mật khẩu | reCAPTCHA

**Nút bấm:**
- `Đăng nhập`
- `Đăng nhập với Google`
- `Đăng nhập bằng OTP qua Email`
- `Quên mật khẩu?`

---

### Đăng ký (`/dang-ky`)
**Form:** Họ và tên | Email | Số điện thoại | Mật khẩu | Xác nhận mật khẩu | reCAPTCHA

**Nút bấm:**
- `Đăng ký`
- `Đăng ký với Google`

---

### Layout chung (Navbar + Footer)

**Navbar menu:** Trang chủ | Khách sạn | Resort | Ưu đãi | Cẩm nang | Liên hệ

**Nút navbar (khi đã đăng nhập):** Thông tin tài khoản | Lịch sử đặt phòng | Yêu thích | Admin Panel | Đăng xuất | `Đọc tất cả` (notifications)

**Nút navbar (chưa đăng nhập):** `Đăng nhập` | `Đặt ngay` (floating)

**Chatbot widget:**
- Preset questions: Còn phòng trống? | Giá phòng? | Chính sách hủy? | Hỗ trợ đặt phòng
- Gửi tin nhắn tự do

**QR Promo popup:**
- Tự động mở sau 4 giây (lần đầu)
- `Nhấn để nhận ngay →`

**Floating elements:**
- `Đặt phòng ngay` (fixed bottom-right)
- Scroll to top button
- Support popup: Họ tên | Điện thoại | Ghi chú

---

## 7. Services & Business Logic

| Service | Chức năng |
|---|---|
| `VNPayService` | Tạo URL thanh toán, xác minh chữ ký IPN |
| `MoMoService` | Tạo QR thanh toán, xử lý callback |
| `InvoiceService` | Tạo PDF hóa đơn đặt phòng |

---

## 8. Observers (tự động khi model thay đổi)

| Observer | Theo dõi | Hành động |
|---|---|---|
| `BookingObserver` | Booking | Ghi audit log, gửi notification |
| `PaymentObserver` | Payment | Cập nhật trạng thái booking |
| `HotelObserver` | Hotel | Xóa Redis cache |
| `ReviewObserver` | Review | Cập nhật điểm trung bình hotel |
| `BlogPostObserver` | BlogPost | Xóa cache blog |
| `LocationObserver` | Location | Xóa cache location |

---

## 9. Cấu hình bảo mật

- **Rate limiting:** Login (10/5min), Register (5/5min), OTP (5/15min), Booking (5/1min), Review (3/1min)
- **CSRF protection:** Tất cả form (trừ payment webhooks)
- **Throttle middleware** trên các route quan trọng
- **reCAPTCHA v3** trên login và register
- **Signed URL** cho email verification
- **bcrypt rounds:** 12

---

## 10. Files cấu hình quan trọng

| File | Mục đích |
|---|---|
| `.env` | Biến môi trường chính |
| `staygo.env` | Bản backup .env đã dọn dẹp |
| `config/payment.php` | VNPay / MoMo / ZaloPay credentials |
| `config/services.php` | Google, Facebook, reCAPTCHA |
| `config/openai.php` | OpenAI API |
| `staygo.md` | File tài liệu này |


# 🤖 StayGo — AI Prompt Guide v2.3

> **Đọc cùng với:** v2.0 + v2.1 + v2.2.
> **v2.3 bổ sung:** (1) Utility class thay inline style sidebar, (2) Flow 6 trang còn thiếu,
> (3) Trạng thái lỗi / loading cho form, (4) Thứ tự import JS cụ thể.

---

## PHẦN 6 — UTILITY CLASS (thay thế inline style trong template)

> **Quy tắc:** Bất kỳ `style="..."` lặp lại ≥ 2 lần trong một template → tách thành class.
> AI phải ưu tiên class trước khi viết inline style.

### 6.1 — Bảng utility class cần biết

| Inline style hay gặp | Class thay thế |
|---|---|
| `display:flex;align-items:center;gap:…` | `.sg-flex-center` + `.sg-gap-{n}` |
| `display:flex;justify-content:space-between` | `.sg-flex-between` |
| `display:grid;grid-template-columns:1fr 1fr` | `.sg-grid-2` |
| `font-size:var(--sg-text-xs);color:var(--sg-text-muted)` | `.sg-text-xs-muted` |
| `font-size:var(--sg-text-sm);color:var(--sg-text-sub)` | `.sg-text-sm-sub` |
| `font-weight:var(--sg-weight-semibold)` | `.sg-semibold` |
| `font-weight:var(--sg-weight-bold)` | `.sg-bold` |
| `padding:var(--sg-space-3) var(--sg-space-4)` | `.sg-px-4.sg-py-3` |
| `border-top:1px solid var(--sg-border-base)` | `.sg-divider-top` |
| `text-align:center` | `.sg-text-center` |
| `position:absolute;top:10px;left:10px` | `.sg-badge-overlay--tl` |
| `position:absolute;top:10px;right:10px` | `.sg-badge-overlay--tr` |

### 6.2 — CSS definitions (thêm vào utilities.css)

```css
/* Layout */
.sg-flex-center  { display: flex; align-items: center; }
.sg-flex-between { display: flex; align-items: center; justify-content: space-between; }
.sg-flex-baseline{ display: flex; align-items: baseline; }
.sg-flex-col     { display: flex; flex-direction: column; }
.sg-grid-2       { display: grid; grid-template-columns: 1fr 1fr; }

/* Gap shortcuts */
.sg-gap-1 { gap: var(--sg-space-1); }
.sg-gap-2 { gap: var(--sg-space-2); }
.sg-gap-3 { gap: var(--sg-space-3); }
.sg-gap-4 { gap: var(--sg-space-4); }
.sg-gap-6 { gap: var(--sg-space-6); }

/* Padding shortcuts */
.sg-p-3  { padding: var(--sg-space-3); }
.sg-p-4  { padding: var(--sg-space-4); }
.sg-p-5  { padding: var(--sg-space-5); }
.sg-p-6  { padding: var(--sg-space-6); }
.sg-px-4 { padding-inline: var(--sg-space-4); }
.sg-py-3 { padding-block:  var(--sg-space-3); }

/* Typography */
.sg-text-xs-muted { font-size: var(--sg-text-xs); color: var(--sg-text-muted); }
.sg-text-sm-sub   { font-size: var(--sg-text-sm); color: var(--sg-text-sub); }
.sg-text-xs-sub   { font-size: var(--sg-text-xs); color: var(--sg-text-sub); }
.sg-semibold      { font-weight: var(--sg-weight-semibold); }
.sg-bold          { font-weight: var(--sg-weight-bold); }
.sg-text-center   { text-align: center; }
.sg-leading       { line-height: var(--sg-leading-normal); }

/* Dividers */
.sg-divider-top    { border-top:    1px solid var(--sg-border-base); }
.sg-divider-bottom { border-bottom: 1px solid var(--sg-border-base); }
.sg-divider-right  { border-right:  1px solid var(--sg-border-base); }

/* Badge overlay (dùng trên ảnh card) */
.sg-badge-overlay--tl { position: absolute; top: 10px; left:  10px; }
.sg-badge-overlay--tr { position: absolute; top: 10px; right: 10px; }

/* Surface block */
.sg-surface-block {
  background: var(--sg-bg-surface);
  border-radius: var(--sg-radius-md);
  padding: var(--sg-space-4);
}

/* Wishlist button (tái dùng trong mọi card) */
.sg-wishlist-btn {
  width: 34px; height: 34px;
  border-radius: 9999px; padding: 0;
  background: rgba(255,255,255,0.9);
  border: none; cursor: pointer;
  color: var(--sg-text-sub);
  transition: color .15s, background .15s;
}
.sg-wishlist-btn:hover { background: #fff; color: var(--sg-color-danger-400); }

html.sg-dark .sg-wishlist-btn { background: rgba(0,0,0,0.55); color: var(--sg-text-muted); }
html.sg-dark .sg-wishlist-btn:hover { background: rgba(0,0,0,0.75); color: var(--sg-color-danger-300); }
```

### 6.3 — Template 4.2 refactored (sidebar đặt phòng)

> Giữ nguyên logic, xóa ~18 inline style, thay bằng class.

```blade
{{-- resources/views/components/booking-sidebar.blade.php --}}
@props(['hotel'])

<aside class="sg-hotel-sidebar" aria-label="Đặt phòng nhanh">
  <div class="sg-card" style="border-radius:var(--sg-radius-xl)">
    <div class="sg-p-5">

      {{-- Giá --}}
      <div class="sg-flex-baseline sg-gap-2" style="margin-bottom:var(--sg-space-4)">
        <span class="sg-card__price" style="font-size:var(--sg-text-2xl)">
          {{ number_format($hotel->price) }}đ
        </span>
        <span class="sg-text-sm-sub">/đêm</span>
      </div>

      {{-- Chọn ngày --}}
      <div class="sg-grid-2"
           style="border:1.5px solid var(--sg-border-base);
                  border-radius:var(--sg-radius-md);overflow:hidden;
                  margin-bottom:var(--sg-space-3)">
        <div class="sg-p-3 sg-divider-right">
          <p class="sg-label sg-text-xs-muted" style="margin-bottom:2px">Nhận phòng</p>
          <input type="date" class="sg-input sg-input--bare"
                 name="check_in" id="check-in" min="{{ date('Y-m-d') }}">
        </div>
        <div class="sg-p-3">
          <p class="sg-label sg-text-xs-muted" style="margin-bottom:2px">Trả phòng</p>
          <input type="date" class="sg-input sg-input--bare"
                 name="check_out" id="check-out">
        </div>
      </div>

      {{-- Số khách --}}
      <button type="button"
              class="sg-input sg-flex-between"
              data-sg-dropdown="dropdown-guests"
              aria-expanded="false" aria-haspopup="true"
              style="cursor:pointer;margin-bottom:var(--sg-space-3)">
        <span id="guests-label">2 người lớn · 0 trẻ em</span>
        <span aria-hidden="true" class="sg-text-xs-muted">▾</span>
      </button>

      <div id="dropdown-guests" class="sg-dropdown__menu" role="dialog" aria-label="Chọn số khách">
        {{-- Người lớn --}}
        <div class="sg-flex-between sg-px-4 sg-py-3">
          <div>
            <p class="sg-text-sm sg-semibold">Người lớn</p>
            <p class="sg-text-xs-muted">Từ 18 tuổi</p>
          </div>
          <div class="sg-counter">
            <button class="sg-counter__btn" data-counter-target="adults" data-action="dec"
                    aria-label="Giảm số người lớn" disabled>−</button>
            <span class="sg-counter__value" id="count-adults" aria-live="polite">2</span>
            <button class="sg-counter__btn" data-counter-target="adults" data-action="inc"
                    aria-label="Tăng số người lớn">+</button>
          </div>
        </div>
        {{-- Trẻ em --}}
        <div class="sg-flex-between sg-px-4 sg-py-3 sg-divider-top">
          <div>
            <p class="sg-text-sm sg-semibold">Trẻ em</p>
            <p class="sg-text-xs-muted">Dưới 18 tuổi</p>
          </div>
          <div class="sg-counter">
            <button class="sg-counter__btn" data-counter-target="children" data-action="dec"
                    aria-label="Giảm số trẻ em" disabled>−</button>
            <span class="sg-counter__value" id="count-children" aria-live="polite">0</span>
            <button class="sg-counter__btn" data-counter-target="children" data-action="inc"
                    aria-label="Tăng số trẻ em">+</button>
          </div>
        </div>
        <div class="sg-p-3">
          <button class="sg-btn sg-btn-primary sg-btn-sm sg-btn-full"
                  data-sg-dropdown-close>Xong</button>
        </div>
      </div>

      {{-- Tổng tiền --}}
      <div class="sg-surface-block" style="margin-bottom:var(--sg-space-4)">
        <div class="sg-flex-between sg-text-sm-sub" style="margin-bottom:var(--sg-space-2)">
          <span id="price-nights-label">{{ number_format($hotel->price) }}đ × 1 đêm</span>
          <span id="price-nights-total">{{ number_format($hotel->price) }}đ</span>
        </div>
        <div class="sg-flex-between sg-divider-top" style="padding-top:var(--sg-space-2)">
          <span class="sg-semibold" style="color:var(--sg-text-primary)">Tổng cộng</span>
          <span class="sg-bold" style="color:var(--sg-brand)" id="price-total">
            {{ number_format($hotel->price) }}đ
          </span>
        </div>
      </div>

      {{-- CTA --}}
      <a href="{{ route('booking.create', $hotel->slug) }}"
         class="sg-btn sg-btn-primary sg-btn-full sg-btn-lg">
        Đặt phòng ngay
      </a>

      {{-- Chính sách --}}
      <p class="sg-text-xs-muted sg-text-center" style="margin-top:var(--sg-space-3)">
        Chưa bị trừ tiền —
        <button type="button"
                class="sg-btn-link sg-text-xs"
                data-sg-tooltip="Hoàn tiền 100% nếu hủy trước 24 giờ check-in">
          Xem chính sách hủy
        </button>
      </p>

    </div>
  </div>
</aside>
```

> **Thêm 2 modifier mới vào input.css:**
> ```css
> /* Input không có border (dùng trong date-grid sidebar) */
> .sg-input--bare {
>   height: auto; border: none; padding: 0;
>   font-size: var(--sg-text-sm);
>   font-weight: var(--sg-weight-semibold);
>   background: transparent;
> }
>
> /* Link dạng inline */
> .sg-btn-link {
>   background: none; border: none; cursor: pointer; padding: 0;
>   color: var(--sg-text-link); text-decoration: underline;
>   font-size: inherit;
> }
> ```

---

## PHẦN 7 — FLOW CÁC TRANG CÒN THIẾU

### 7.1 — Trang Khuyến mãi (Promotions)

```
sg-navbar
sg-promo-hero          ← gradient đậm, text trắng, đếm ngược deal (nếu có)
  └─ sg-countdown (tùy chọn, nếu là flash sale)

sg-promo-tabs          ← All / Khách sạn / Vé máy bay / Combo
  │
  ├─ Tab active → grid xs:1 md:2 lg:3 → sg-promo-card (mỗi ưu đãi)
  │   sg-promo-card có:
  │     ├─ ảnh banner
  │     ├─ sg-badge-danger (% giảm) tại .sg-badge-overlay--tl
  │     ├─ tiêu đề + mô tả ngắn
  │     ├─ mã code + nút sao chép → SgToast.success('Đã sao chép mã')
  │     └─ CTA "Đặt ngay" → link đến trang search có promo pre-fill
  │
  └─ Rỗng (hết hạn) → sg-empty "Hiện chưa có ưu đãi cho mục này"

sg-footer
```

**Modals trên trang này:**

```
#modal-promo-detail → Chi tiết điều kiện áp dụng (mở khi click "Điều kiện")
```

---

### 7.2 — Trang Lịch sử đặt phòng (My Bookings)

```
sg-navbar (authenticated)

sg-tabs (horizontal, compact):
  ├─ Tất cả
  ├─ Sắp tới   (is-active mặc định)
  ├─ Đã hoàn thành
  └─ Đã hủy

Mỗi tab → sg-table (desktop) / danh sách card (mobile)

Desktop sg-table:
  Cột: [Mã đặt phòng | Khách sạn | Ngày | Phòng | Tổng tiền | Trạng thái | Hành động]
  Trạng thái → sg-badge:
    Xác nhận   → sg-badge-success
    Chờ TT     → sg-badge-warning
    Đã hủy     → sg-badge-danger
    Hoàn thành → sg-badge (neutral)
  Hành động → sg-dropdown__menu:
    ├─ Xem chi tiết  → link
    ├─ Tải hóa đơn   → link download
    └─ Hủy đặt phòng → SgModal.open('modal-cancel-confirm')
                       modal dùng sg-modal__confirm-icon--danger

Mobile card (thay sg-table):
  sg-card compact: tên KS + ngày + badge trạng thái + nút "Chi tiết"

sg-pagination (cuối danh sách)

Loading: sg-skeleton cho mỗi row / card
Rỗng:    sg-empty + gợi ý "Tìm khách sạn ngay"
```

**Modal hủy đặt phòng:**

```blade
<div class="sg-modal" id="modal-cancel-confirm" role="dialog"
     aria-labelledby="modal-cancel-title" aria-modal="true">
  <div class="sg-modal__content">
    <div class="sg-modal__confirm-icon sg-modal__confirm-icon--danger"
         aria-hidden="true">✕</div>
    <h2 class="sg-modal__title" id="modal-cancel-title">Hủy đặt phòng?</h2>
    <p class="sg-text-sm-sub sg-text-center">
      Bạn có chắc muốn hủy đặt phòng tại <strong id="modal-cancel-hotel-name"></strong>?
      Phí hủy có thể được áp dụng theo chính sách khách sạn.
    </p>
    <div class="sg-modal__actions">
      <button class="sg-btn sg-btn-ghost" data-sg-modal-close>Quay lại</button>
      <button class="sg-btn sg-btn-danger" id="btn-confirm-cancel">Xác nhận hủy</button>
    </div>
  </div>
</div>
```

---

### 7.3 — Trang Lỗi (Error Pages)

```
sg-navbar (minimal, không có search)

sg-error-page (full-page centered layout):
  ├─ sg-error-page__code   → "404" / "500" / "503"
  ├─ sg-error-page__title  → tiêu đề lỗi
  ├─ sg-error-page__desc   → mô tả thân thiện
  └─ actions:
       sg-btn-primary  "Về trang chủ"   → href="/"
       sg-btn-outline  "Thử lại"         → onclick="location.reload()"  (chỉ dùng cho 500/503)

sg-footer (minimal)
```

**3 biến thể bắt buộc:**

| Code | Icon | Tiêu đề | Có nút "Thử lại"? |
|---|---|---|---|
| 404 | 🔍 | Không tìm thấy trang này | Không |
| 500 | ⚙️ | Lỗi máy chủ | Có |
| 503 | 🔧 | Đang bảo trì | Có (kèm thời gian dự kiến) |

```blade
{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.minimal')
@section('content')
<main class="sg-error-page" role="main">
  <p class="sg-error-page__code" aria-hidden="true">404</p>
  <span class="sg-error-page__icon" aria-hidden="true">🔍</span>
  <h1 class="sg-error-page__title">Không tìm thấy trang này</h1>
  <p class="sg-error-page__desc sg-text-sm-sub">
    Trang bạn tìm kiếm có thể đã bị xóa hoặc địa chỉ không đúng.
  </p>
  <div class="sg-flex-center sg-gap-3" style="justify-content:center;margin-top:var(--sg-space-6)">
    <a href="/" class="sg-btn sg-btn-primary">Về trang chủ</a>
  </div>
</main>
@endsection
```

---

### 7.4 — Trang Không có kết quả (Empty Search)

> Không phải error page — vẫn giữ navbar + search bar để người dùng thử lại.

```
sg-navbar
[Search bar tóm tắt: "Đà Nẵng | 20-22/06 | 2 khách"  sg-btn-outline "Sửa"]

sg-empty (centered, bên dưới search bar):
  sg-empty__icon  🏨
  "Không tìm thấy khách sạn phù hợp"
  sg-text-sm-sub  "Thử thay đổi ngày hoặc giảm bộ lọc"
  gợi ý nhanh:
    sg-btn-outline "Xóa bộ lọc"     → reset filter params
    sg-btn-outline "Thêm 1 đêm"     → extend date +1
    sg-btn-outline "Thêm địa điểm lân cận" → expand radius
```

---

### 7.5 — Trang Tài khoản / Profile

```
sg-navbar (authenticated)

Layout 2 cột (md+) / 1 cột (mobile):
  ├─ Sidebar nav (sticky md+):
  │     ├─ Thông tin cá nhân  (is-active)
  │     ├─ Đặt phòng của tôi
  │     ├─ Yêu thích
  │     ├─ Phương thức thanh toán
  │     └─ Đăng xuất → SgModal.open('modal-logout-confirm')
  │
  └─ Main panel:
       "Thông tin cá nhân"  (h1)
       sg-avatar (lg) + sg-btn-outline "Đổi ảnh"
       Form:
         sg-input  Họ và tên
         sg-input  Email (disabled nếu đăng nhập Google)
         sg-input  Số điện thoại
         sg-select Quốc tịch
         sg-toggle Nhận thông báo qua email
       sg-btn-primary "Lưu thay đổi"
         → success: SgToast.success('Đã cập nhật thông tin')
         → error:   SgToast.danger('Cập nhật thất bại', 'Vui lòng thử lại')
```

---

### 7.6 — Trang FAQ

```
sg-navbar
[Breadcrumb: Trang chủ › Trợ giúp › FAQ]

"Câu hỏi thường gặp" (h1)
sg-input (search FAQ, optional)

Nhóm câu hỏi → mỗi nhóm là 1 block:
  h2  "Đặt phòng"
  sg-accordion (KHÔNG dùng <details>/<summary>) × N câu hỏi

  h2  "Thanh toán"
  sg-accordion × N

  h2  "Hủy và hoàn tiền"
  sg-accordion × N

  h2  "Tài khoản"
  sg-accordion × N

sg-footer
```

---

## PHẦN 8 — TRẠNG THÁI LỖI VÀ LOADING CHO FORM

### 8.1 — Input: trạng thái is-error

```blade
{{-- Trường có lỗi validation --}}
<div class="sg-form-group">
  <label class="sg-label" for="email">Email</label>
  <div class="sg-input-group">
    <input
      type="email"
      id="email"
      name="email"
      class="sg-input is-error"          {{-- thêm is-error --}}
      aria-invalid="true"                {{-- BẮT BUỘC cho a11y --}}
      aria-describedby="email-error"     {{-- trỏ đến message --}}
      value="{{ old('email') }}"
    >
    <span class="sg-input-group__icon" aria-hidden="true">⚠</span>
  </div>
  <p class="sg-field-error" id="email-error" role="alert">
    {{ $errors->first('email') }}
  </p>
</div>
```

```css
/* input.css — thêm vào cuối */
.sg-input.is-error {
  border-color: var(--sg-color-danger-400);
  background: var(--sg-color-danger-50);
}
.sg-input.is-error:focus {
  outline-color: var(--sg-color-danger-400);
  box-shadow: 0 0 0 3px rgba(var(--sg-color-danger-rgb), .15);
}
.sg-field-error {
  font-size: var(--sg-text-xs);
  color: var(--sg-color-danger-500);
  margin-top: var(--sg-space-1);
  display: flex;
  align-items: center;
  gap: var(--sg-space-1);
}
html.sg-dark .sg-input.is-error { background: var(--sg-color-danger-50); }
```

### 8.2 — Input: trạng thái is-success

```blade
<input type="text" class="sg-input is-success"
       aria-invalid="false" aria-describedby="phone-ok">
<p class="sg-field-success" id="phone-ok">Số điện thoại hợp lệ ✓</p>
```

```css
.sg-input.is-success { border-color: var(--sg-color-success-400); }
.sg-field-success {
  font-size: var(--sg-text-xs);
  color: var(--sg-color-success-500);
  margin-top: var(--sg-space-1);
}
```

### 8.3 — Input: trạng thái is-loading (async validate)

```blade
<div class="sg-input-group">
  <input type="text" class="sg-input" id="promo-code" name="promo_code">
  <span class="sg-input-group__icon sg-spinner" aria-label="Đang kiểm tra..." id="promo-spinner"
        style="display:none" aria-hidden="true"></span>
</div>
```

### 8.4 — Skeleton cho Room Card (loading state)

```blade
{{-- Dùng khi đang fetch danh sách phòng qua AJAX --}}
<div class="sg-skeleton-room-card" aria-hidden="true">
  <div style="display:flex;gap:var(--sg-space-4)">
    {{-- Ảnh --}}
    <div class="sg-skeleton" style="width:200px;height:140px;border-radius:var(--sg-radius-md);
                                    flex-shrink:0"></div>
    {{-- Nội dung --}}
    <div style="flex:1;display:flex;flex-direction:column;gap:var(--sg-space-3)">
      <div class="sg-skeleton" style="height:20px;width:60%"></div>
      <div class="sg-skeleton" style="height:14px;width:80%"></div>
      <div class="sg-skeleton" style="height:14px;width:40%"></div>
      <div style="margin-top:auto;display:flex;justify-content:flex-end">
        <div class="sg-skeleton" style="height:36px;width:120px;border-radius:var(--sg-radius-md)"></div>
      </div>
    </div>
  </div>
</div>
```

**Blade helper — dùng khi render trang:**

```blade
{{-- Nếu đang load, show skeleton; nếu có data, show room-card --}}
@if($loading)
  @for($i = 0; $i < 3; $i++)
    <x-skeleton-room-card />
  @endfor
@elseif($hotel->rooms->isEmpty())
  <x-sg-empty icon="🛏️" message="Không có phòng trống cho ngày đã chọn" />
@else
  @foreach($hotel->rooms as $room)
    <x-room-card :room="$room" :hotel="$hotel" />
  @endforeach
@endif
```

### 8.5 — Button: trạng thái loading khi submit

```blade
<button type="submit"
        class="sg-btn sg-btn-primary sg-btn-full"
        id="btn-submit-booking"
        data-loading-text="Đang xử lý..."
        data-default-text="Tiếp tục">
  Tiếp tục
</button>
```

```js
// Trong sg-utils.js — tự động xử lý tất cả button có data-loading-text
document.querySelectorAll('[data-loading-text]').forEach(btn => {
  btn.closest('form')?.addEventListener('submit', () => {
    btn.disabled = true;
    btn.textContent = btn.dataset.loadingText;
    btn.classList.add('is-loading');
  });
});
```

```css
/* button.css */
.sg-btn.is-loading { opacity: .7; cursor: not-allowed; }
.sg-btn.is-loading::before {
  content: '';
  display: inline-block;
  width: 14px; height: 14px;
  border: 2px solid currentColor;
  border-top-color: transparent;
  border-radius: 50%;
  animation: sg-spin .6s linear infinite;
  margin-right: var(--sg-space-2);
  vertical-align: middle;
}
@media (prefers-reduced-motion: reduce) {
  .sg-btn.is-loading::before { animation: none; opacity: .5; }
}
```

---

## PHẦN 9 — THỨ TỰ IMPORT JS (cụ thể)

### 9.1 — Vấn đề

`SgDark.init()` phải chạy **trước khi trình duyệt render frame đầu tiên** để tránh flash of wrong theme (FOWT). Nếu đặt trong `app.js` (cuối body), sẽ thấy trang sáng nhấp nháy trước khi chuyển tối.

### 9.2 — Nơi đặt: inline script trong `<head>`

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi" id="sg-html">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'StayGo')</title>

  {{-- 1. CSS tokens trước tiên (blocking, cần thiết) --}}
  @vite(['resources/css/tokens.css', 'resources/css/app.css'])

  {{-- 2. SgDark: inline script — KHÔNG dùng defer/async, KHÔNG dùng src --}}
  {{--    Chạy sync để tránh FOWT (flash of wrong theme)               --}}
  <script>
    (function () {
      const stored = localStorage.getItem('sg-theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (stored === 'dark' || (!stored && prefersDark)) {
        document.documentElement.classList.add('sg-dark');
      }
    })();
  </script>

</head>
<body>

  {{-- skip nav --}}
  <a href="#main-content" class="sg-skip-nav">Bỏ qua điều hướng</a>

  @yield('content')

  {{-- 3. app.js: defer — chứa SgDark.init() (đăng ký toggle event),  --}}
  {{--   SgToast, SgModal, SgDrawer, SgDropdown, SgCounter, SgAccordion --}}
  @vite('resources/js/app.js')

</body>
</html>
```

### 9.3 — Thứ tự trong app.js

```js
// resources/js/app.js
// Thứ tự import QUAN TRỌNG — không đổi chỗ

// 1. Utilities & theme (không có DOM dependency)
import './sg/sg-utils.js';
import './sg/sg-dark.js';       // SgDark.init() đăng ký toggle listener

// 2. Components không phụ thuộc nhau
import './sg/sg-toast.js';
import './sg/sg-tooltip.js';
import './sg/sg-accordion.js';

// 3. Components có thể mở overlay → cần Toast đã sẵn sàng
import './sg/sg-modal.js';
import './sg/sg-drawer.js';
import './sg/sg-dropdown.js';

// 4. Components dùng trong overlay
import './sg/sg-counter.js';
import './sg/sg-range.js';

// 5. Page-level scripts (chỉ chạy nếu element tồn tại)
import './sg/sg-booking-sidebar.js';   // tính tổng tiền, update guests-label
import './sg/sg-payment.js';           // countdown, polling
import './sg/sg-gallery.js';           // lightbox

// 6. Khởi tạo tất cả
document.addEventListener('DOMContentLoaded', () => {
  SgDark.init();       // đăng ký sự kiện toggle (class đã apply từ <head>)
  SgToast.init();
  SgModal.init();
  SgDrawer.init();
  SgDropdown.init();
  SgAccordion.init();
  SgCounter.init();
});
```

### 9.4 — SgDark: tách biệt hai nhiệm vụ

```js
// resources/js/sg/sg-dark.js
const SgDark = {
  // apply() được gọi inline trong <head> (inlined, không import)
  // KHÔNG đặt ở đây để tránh circular dependency

  // init() đăng ký toggle button, chạy sau DOMContentLoaded
  init() {
    document.querySelectorAll('[data-sg-toggle-dark]').forEach(btn => {
      btn.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('sg-dark');
        localStorage.setItem('sg-theme', isDark ? 'dark' : 'light');
        btn.setAttribute('aria-pressed', isDark);
        // Cập nhật icon nếu có
        const icon = btn.querySelector('[data-dark-icon]');
        if (icon) icon.textContent = isDark ? '☀️' : '🌙';
      });
    });
  }
};

window.SgDark = SgDark;
export default SgDark;
```

---

## PHẦN 10 — CHECKLIST BỔ SUNG (v2.3)

```
UTILITY CLASS (Phần 6)
  [ ] Không có inline style lặp lại ≥ 2 lần trong cùng một template?
  [ ] Dùng .sg-surface-block thay vì background: var(--sg-bg-surface) inline?
  [ ] Dùng .sg-wishlist-btn thay vì button style inline?
  [ ] .sg-input--bare cho input trong date-grid?
  [ ] .sg-btn-link cho anchor text button?

FLOW TRANG (Phần 7)
  [ ] Error page có đủ 3 biến thể 404 / 500 / 503?
  [ ] Empty search vẫn giữ search bar (KHÔNG dùng sg-error-page)?
  [ ] Modal hủy đặt phòng dùng sg-modal__confirm-icon--danger?

TRẠNG THÁI FORM (Phần 8)
  [ ] Input lỗi có class is-error + aria-invalid="true" + aria-describedby?
  [ ] .sg-field-error có role="alert"?
  [ ] Skeleton room-card dùng đúng cấu trúc (ảnh trái + content phải)?
  [ ] Button submit có data-loading-text và is-loading state?
  [ ] is-loading animation respect prefers-reduced-motion?

JS IMPORT (Phần 9)
  [ ] SgDark apply() là inline script trong <head>, KHÔNG có src/defer?
  [ ] app.js import đúng thứ tự: utils → toast → modal → counter → init?
  [ ] DOMContentLoaded bao bọc tất cả .init() call?
  [ ] SgDark.init() trong DOMContentLoaded (chỉ đăng ký toggle, không apply lại)?
```# StayGo - Wed May 20 16:50:51 SEAST 2026
