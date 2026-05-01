@extends('layouts.app')
@section('title', 'Trang chủ')
@section('body_class', 'page-home')
@section('header_class', 'header-transparent')

@section('preload_assets')
<link rel="preload" as="image" href="{{ asset('assets/images/hero-bg.jpg') }}" fetchpriority="high">
@endsection

@section('content')

{{-- Hero Fullscreen --}}
<div class="hero-search-section">

    {{-- Slideshow ảnh nền --}}
    <div class="hero-slides">
        <div class="hero-slide" style="--delay:0s">
            <img src="{{ asset('assets/images/hero-bg.jpg') }}" alt="" fetchpriority="high">
        </div>
        <div class="hero-slide" style="--delay:2s">
            <img src="{{ asset('assets/images/anhbien.jpg') }}" alt="" loading="lazy">
        </div>
    </div>

    {{-- Nội dung giữa hero --}}
    <div class="hero-content">
        <div class="hero-search-title">Tìm khách sạn hoàn hảo cho bạn</div>
        <div class="hero-search-sub">Hàng trăm khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu & Đà Nẵng</div>

        {{-- Search type tabs --}}
        <div class="hsb-tabs-wrap">
            <button type="button" class="hsb-tab-btn active" onclick="setStayType('night', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Qua đêm
            </button>
            <button type="button" class="hsb-tab-btn" onclick="setStayType('oneday', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>
                1 ngày
            </button>
        </div>
    </div>

    {{-- Booking bar — dính đáy hero --}}
    <div class="hero-booking-bar">
        <form action="{{ route('hotels.index') }}" method="GET" class="hero-search-box has-filter-row" id="hsbForm">
            <input type="hidden" name="location" id="hsb-location-id">
            <input type="hidden" name="stay_type" id="hsb-stay-type" value="night">

            {{-- Destination --}}
            <div class="hsb-field hsb-keyword">
                <span class="hsb-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Điểm đến / Tên khách sạn
                </span>
                <input class="hsb-input" type="text" name="keyword" id="hsb-keyword-input"
                    placeholder="Tên khách sạn, địa điểm..." autocomplete="off"
                    onfocus="showLocationDropdown()" oninput="filterLocations(this.value)">
                <div id="hsb-location-dropdown" class="hsb-dropdown">
                    {{-- Gần tôi --}}
                    <div class="hsb-loc-nearby" id="hsbNearbyBtn" onclick="findNearbyHotels()">
                        <div class="hsb-loc-nearby-icon">
                            <svg width="15" height="15" fill="none" stroke="#e91e8c" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div class="hsb-loc-name" style="color:#e91e8c;font-weight:700;">Gần tôi</div>
                            <div class="hsb-loc-count" id="hsbNearbyStatus">Dùng vị trí của bạn để tìm khách sạn</div>
                        </div>
                        <svg id="hsbNearbySpinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e91e8c" stroke-width="2" style="display:none;margin-left:auto;animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-dasharray="40" stroke-dashoffset="10"/></svg>
                    </div>
                    <div class="hsb-dropdown-divider"></div>
                    {{-- Điểm đến phổ biến --}}
                    <div class="hsb-dropdown-title">Điểm đến phổ biến</div>
                    @foreach($locations->sortByDesc('hotels_count') as $loc)
                    <div class="hsb-loc-item"
                        data-id="{{ $loc->id }}"
                        data-name="{{ strtolower($loc->name) }}"
                        onclick="selectLocation('{{ $loc->name }}', {{ $loc->id }})">
                        <div class="hsb-loc-icon">
                            <svg width="13" height="13" fill="none" stroke="#004391" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div style="flex:1;">
                            <div class="hsb-loc-name">{{ $loc->name }}</div>
                            <div class="hsb-loc-count">{{ $loc->hotels_count ?? 0 }} khách sạn</div>
                        </div>
                        @if($loc->hotels_avg_rating)
                        <div class="hsb-loc-rating">
                            <svg width="10" height="10" fill="#f59e0b" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            {{ number_format($loc->hotels_avg_rating, 1) }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Checkin --}}
            <div class="hsb-field hsb-date">
                <span class="hsb-label hsb-label-checkin">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Nhận phòng
                </span>
                <input class="hsb-input" type="date" name="checkin" id="h_checkin"
                    min="{{ date('Y-m-d') }}"
                    onchange="updateMinCheckout(this.value)">
            </div>

            {{-- Checkout --}}
            <div class="hsb-field hsb-date">
                <span class="hsb-label hsb-label-checkout">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Trả phòng
                </span>
                <input class="hsb-input" type="date" name="checkout" id="h_checkout"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>

            {{-- Guests + Children popup --}}
            <div class="hsb-field hsb-guests" style="position:relative;">
                <span class="hsb-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    Số khách
                </span>
                <input type="hidden" name="guests" id="h_guests" value="1">
                <input type="hidden" name="children" id="h_children" value="0">
                <div class="hsb-input hsb-guests-display" id="hsbGuestsDisplay" onclick="toggleGuestsPopup()" style="cursor:pointer;user-select:none;">
                    1 người lớn
                </div>
                <div class="hsb-guests-popup" id="hsbGuestsPopup" style="display:none;">
                    <div class="hsb-gp-header">
                        <span>Khách &amp; Phòng</span>
                        <button type="button" class="hsb-gp-close" onclick="toggleGuestsPopup()">✕</button>
                    </div>
                    <div class="hsb-gp-row">
                        <div class="hsb-gp-info">
                            <svg width="18" height="18" fill="none" stroke="#64748b" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h6"/></svg>
                            <div><div class="hsb-gp-label">Phòng</div></div>
                        </div>
                        <div class="hsb-gp-stepper">
                            <button type="button" onclick="changeRooms(-1)">−</button>
                            <span id="hsbRoomsVal">1</span>
                            <button type="button" onclick="changeRooms(1)">+</button>
                        </div>
                    </div>
                    <div class="hsb-gp-divider"></div>
                    <div class="hsb-gp-row">
                        <div class="hsb-gp-info">
                            <svg width="18" height="18" fill="none" stroke="#64748b" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            <div><div class="hsb-gp-label">Người lớn</div></div>
                        </div>
                        <div class="hsb-gp-stepper">
                            <button type="button" onclick="changeGuests(-1)">−</button>
                            <span id="hsbAdultsVal">1</span>
                            <button type="button" onclick="changeGuests(1)">+</button>
                        </div>
                    </div>
                    <div class="hsb-gp-divider"></div>
                    <div class="hsb-gp-row">
                        <div class="hsb-gp-info">
                            <svg width="18" height="18" fill="none" stroke="#64748b" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-1a6 6 0 0112 0v1"/><path d="M9 12l-2 4h10l-2-4"/></svg>
                            <div><div class="hsb-gp-label">Trẻ em</div><div class="hsb-gp-sub">Tuổi 0–17</div></div>
                        </div>
                        <div class="hsb-gp-stepper">
                            <button type="button" onclick="changeChildren(-1)">−</button>
                            <span id="hsbChildrenVal">0</span>
                            <button type="button" onclick="changeChildren(1)">+</button>
                        </div>
                    </div>
                    <div id="hsbChildAges" style="display:none; padding: 0 18px 4px;"></div>
                    <button type="button" class="hsb-gp-done" onclick="toggleGuestsPopup()">Xong</button>
                </div>
            </div>

            <button type="submit" class="hsb-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                TÌM PHÒNG
            </button>
        </form>

        {{-- Filter row --}}
        <div class="hsb-filter-row" id="home-filter-row">
            {{-- Đánh giá --}}
            <div class="hsb-fr-group">
                <span class="hsb-fr-label">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Đánh giá
                </span>
                <div class="hsb-fr-chips">
                    <a href="javascript:void(0)" onclick="setHomeRating('', this)" class="hsb-fr-chip active">Tất cả</a>
                    <a href="javascript:void(0)" onclick="setHomeRating('7', this)" class="hsb-fr-chip">7+</a>
                    <a href="javascript:void(0)" onclick="setHomeRating('8', this)" class="hsb-fr-chip">8+</a>
                    <a href="javascript:void(0)" onclick="setHomeRating('9', this)" class="hsb-fr-chip">9+</a>
                </div>
            </div>

            <div class="hsb-fr-sep"></div>

            {{-- Giá / đêm --}}
            <div class="hsb-fr-group">
                <span class="hsb-fr-label">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Giá / đêm
                </span>
                <div class="hsb-fr-chips">
                    <a href="javascript:void(0)" onclick="setHomePrice('','500000', this)" class="hsb-fr-chip">Dưới 500k</a>
                    <a href="javascript:void(0)" onclick="setHomePrice('500000','1000000', this)" class="hsb-fr-chip">500k–1tr</a>
                    <a href="javascript:void(0)" onclick="setHomePrice('1000000','', this)" class="hsb-fr-chip">Trên 1tr</a>
                </div>
            </div>

            <div class="hsb-fr-sep"></div>

            {{-- Sắp xếp --}}
            <div class="hsb-fr-group">
                <span class="hsb-fr-label">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                    Sắp xếp
                </span>
                <select class="hsb-fr-select" onchange="document.getElementById('hsb-sort-input').value=this.value">
                    <option value="rating">Đánh giá cao nhất</option>
                    <option value="price_asc">Giá thấp → cao</option>
                    <option value="price_desc">Giá cao → thấp</option>
                    <option value="popular">Phổ biến nhất</option>
                </select>
            </div>

        </div>

        {{-- Filter row 2: Loại hình --}}
        <div class="hsb-filter-row" id="home-filter-row-2" style="border-top: 1.5px solid #f0f4f8 !important;">
            <div class="hsb-fr-group">
                <a href="javascript:void(0)" onclick="setHomeType('', this)" class="hsb-fr-chip active">Tất cả</a>
                <a href="javascript:void(0)" onclick="setHomeType('hotel', this)" class="hsb-fr-chip">🏨 Khách sạn</a>
                <a href="javascript:void(0)" onclick="setHomeType('homestay-resort', this)" class="hsb-fr-chip">🏨 Khách sạn & Resort</a>
            </div>
        </div>

        {{-- Hidden inputs for filter values --}}
        <input type="hidden" name="type"      id="hsb-type-input"     form="hsbForm">
        <input type="hidden" name="rating"    id="hsb-rating-input"   form="hsbForm">
        <input type="hidden" name="min_price" id="hsb-minprice-input" form="hsbForm">
        <input type="hidden" name="max_price" id="hsb-maxprice-input" form="hsbForm">
        <input type="hidden" name="sort"      id="hsb-sort-input"     form="hsbForm" value="rating">
    </div>
</div>

{{-- Trust Strip --}}
<div class="sg-trust-strip">
    <div class="sg-trust-inner">
        <div class="sg-trust-item">
            <div class="sg-trust-icon">🏨</div>
            <div>
                <div class="sg-trust-title">100+ Khách sạn</div>
                <div class="sg-trust-sub">Tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</div>
            </div>
        </div>
        <div class="sg-trust-item">
            <div class="sg-trust-icon">💰</div>
            <div>
                <div class="sg-trust-title">Giá tốt nhất</div>
                <div class="sg-trust-sub">Cam kết giá rẻ nhất thị trường</div>
            </div>
        </div>
        <div class="sg-trust-item">
            <div class="sg-trust-icon">✅</div>
            <div>
                <div class="sg-trust-title">Hủy miễn phí</div>
                <div class="sg-trust-sub">Linh hoạt, không lo rủi ro</div>
            </div>
        </div>
        <div class="sg-trust-item">
            <div class="sg-trust-icon">⚡</div>
            <div>
                <div class="sg-trust-title">Đặt phòng nhanh</div>
                <div class="sg-trust-sub">Xác nhận tức thì, an toàn</div>
            </div>
        </div>
    </div>
</div>

{{-- Story Section --}}
<section class="sg-story-section">
    {{-- Decoration nằm sát mép trái màn hình --}}
    <img class="sg-story-decor" src="{{ asset('assets/images/caybien.png') }}" alt="" loading="lazy" aria-hidden="true">

    <div class="container">
        <div class="sg-story-grid">
            <div class="sg-story-cursive">Hành trình<br>bất tận</div>
            <div class="sg-story-text">
                <p>Từ Đà Lạt mộng mơ giữa ngàn hoa, Nha Trang rực rỡ với bãi biển dài trắng xóa, Vũng Tàu yên bình của những buổi chiều hoàng hôn hồng rực — đến Đà Nẵng năng động bên dòng sông Hàn lung linh ánh đèn. Mỗi điểm đến là một bức tranh riêng, đẹp theo cách riêng của mình.</p>
                <p>StayGo kết nối bạn với hàng trăm khách sạn, homestay và resort được tuyển chọn kỹ lưỡng tại những điểm đến này — để mỗi chuyến đi không chỉ là một kỳ nghỉ, mà là một hành trình khó quên đọng lại mãi trong ký ức.</p>
            </div>
        </div>
        <div class="sg-story-img-wrap">
            <img src="{{ asset('assets/images/anhbien2.jpg') }}" alt="Biển đẹp Việt Nam" loading="lazy">
        </div>
    </div>
</section>

{{-- Phòng nghỉ Editorial Intro --}}
<div class="sg-rooms-editorial">
    <span class="sg-rooms-editorial-label" data-nosnippet>PHÒNG NGHỈ</span>
    <h2 class="sg-rooms-editorial-title">
        <span class="sg-story-cursive">Không gian nghỉ dưỡng<br>tuyệt vời dành cho bạn</span>
    </h2>
    <p class="sg-rooms-editorial-desc" data-nosnippet>Từ view biển xanh ngọc bích tại Nha Trang, không gian resort sang trọng ven biển Đà Nẵng, đến những căn villa ấm áp giữa lòng Đà Lạt — từng hạng phòng tại StayGo đều được tuyển chọn kỹ lưỡng, từ phòng đôi ấm cúng đến suite rộng rãi, kết hợp phong cách thiết kế tinh tế với đầy đủ tiện nghi hiện đại. Mỗi kỳ lưu trú là một trải nghiệm riêng khó quên.</p>
</div>

{{-- Hotel Showcase --}}
<section class="sg-hotel-showcase">
    <div class="container">
        <div class="sg-showcase-grid">

            {{-- The Imperial Vung Tau Hotel & Resort --}}
            <div class="sg-showcase-card">
                <div class="sg-showcase-img-wrap">
                    <img src="{{ asset('assets/images/hotels/01KQ7RMTP55JMKGPP4AYY9VA61.jpg') }}" alt="The Imperial Vung Tau Hotel & Resort" loading="lazy">
                </div>
                <div class="sg-showcase-info">
                    <span class="sg-showcase-badge">Khách sạn 5 sao · Vũng Tàu</span>
                    <h3 class="sg-showcase-title">The Imperial Vung Tau Hotel &amp; Resort</h3>
                    <p class="sg-showcase-desc">Tọa lạc tại vị trí đắc địa giữa trái tim phường Thắng Tam, The Imperial Vung Tau là biểu tượng kiến trúc Victoria kiêu sa bên bờ biển Vũng Tàu. Không gian sự kiện hoàng gia, khu Spa cao cấp và dịch vụ tinh tế — tất cả cộng hưởng để mang đến một kỳ nghỉ dưỡng đẳng cấp khó quên.</p>
                    <a href="{{ route('hotels.show', 7) }}" class="sg-showcase-btn">Đặt phòng</a>
                </div>
            </div>

            {{-- Marina Bay Vung Tau Resort --}}
            <div class="sg-showcase-card sg-showcase-card--reverse">
                <div class="sg-showcase-img-wrap">
                    <img src="{{ asset('assets/images/hotels/01KQ658AECY4YJ9AGAF4J1VAGJ.jpg') }}" alt="Marina Bay Vung Tau Resort" loading="lazy">
                </div>
                <div class="sg-showcase-info">
                    <span class="sg-showcase-badge">Resort 4 sao · Vũng Tàu</span>
                    <h3 class="sg-showcase-title">Marina Bay Vung Tau Resort &amp; Spa</h3>
                    <p class="sg-showcase-desc">Tọa lạc ngay mặt tiền biển Bãi Trước trên tuyến đường Trần Phú sầm uất, Marina Bay Resort sở hữu tầm nhìn panorama trực diện ra biển. Hồ bơi ngoài trời view biển, quầy bar tầng thượng và trung tâm Spa hơn 20 liệu trình — điểm đến lý tưởng cho kỳ nghỉ cuối tuần chỉ 2 giờ từ TP.HCM.</p>
                    <a href="{{ route('hotels.show', 9) }}" class="sg-showcase-btn">Đặt phòng</a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Locations --}}
@if($locations->count())
<section class="home-section">
    <div class="container">
        <div class="home-section-head home-section-head--center">
            <div>
                <h2 class="home-section-title">Địa điểm đến đang thịnh hành</h2>
                <p class="home-section-sub">Những điểm đến biển và núi nổi bật tại Việt Nam</p>
            </div>
        </div>
        <div class="locations-carousel-wrap">
            <button class="loc-arrow loc-arrow--prev" id="locPrev" aria-label="Trước">&#8592;</button>
            <div class="locations-grid" id="locGrid">
                @foreach($locations as $loc)
                <a href="{{ route('hotels.index', ['location' => $loc->id]) }}" class="location-card">
                    @if($loc->image)
                    <img src="{{ asset('storage/' . $loc->image) }}" alt="{{ $loc->name }}" loading="lazy">
                    @endif
                    <div class="location-info">
                        <div class="location-count-badge">🏨 {{ $loc->hotels_count }} khách sạn</div>
                        <div class="location-name">{{ $loc->name }}</div>
                        @if($loc->description)
                        <div class="location-desc">{{ Str::limit($loc->description, 80) }}</div>
                        @endif
                        <span class="location-cta">Khám phá</span>
                    </div>
                </a>
                @endforeach
            </div>
            <button class="loc-arrow loc-arrow--next" id="locNext" aria-label="Tiếp">&#8594;</button>
        </div>
        <script>
        (function(){
            const grid = document.getElementById('locGrid');
            const prev = document.getElementById('locPrev');
            const next = document.getElementById('locNext');
            const step = 340;
            prev.addEventListener('click', () => grid.scrollBy({ left: -step, behavior: 'smooth' }));
            next.addEventListener('click', () => grid.scrollBy({ left:  step, behavior: 'smooth' }));
            // Drag to scroll
            let isDown = false, startX, scrollLeft;
            grid.addEventListener('mousedown', e => {
                isDown = true;
                grid.classList.add('is-dragging');
                startX = e.pageX - grid.offsetLeft;
                scrollLeft = grid.scrollLeft;
            });
            grid.addEventListener('mouseleave', () => { isDown = false; grid.classList.remove('is-dragging'); });
            grid.addEventListener('mouseup',    () => { isDown = false; grid.classList.remove('is-dragging'); });
            grid.addEventListener('mousemove',  e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - grid.offsetLeft;
                grid.scrollLeft = scrollLeft - (x - startX) * 1.4;
            });
            // Prevent click after drag
            grid.addEventListener('click', e => {
                if (Math.abs(grid.scrollLeft - scrollLeft) > 5) e.preventDefault();
            }, true);
        })();
        </script>
    </div>
</section>
@endif

{{-- Dịch vụ --}}
<section class="sg-service-section">
    {{-- Rùa biển trang trí góc phải --}}
    <img class="sg-service-decor" src="{{ asset('assets/images/ruabien.png') }}" alt="" aria-hidden="true" loading="lazy">

    <div class="container">

        {{-- Tiêu đề + mô tả --}}
        <div class="sg-service-header">
            <div class="sg-service-header-left">
                <span class="sg-service-label">DỊCH VỤ</span>
                <h2 class="sg-service-cursive">Tận tâm với<br>mọi trải nghiệm</h2>
            </div>
            <div class="sg-service-header-right">
                <p>StayGo kết nối bạn với hàng trăm khách sạn và resort được tuyển chọn kỹ lưỡng — từ khách sạn sang trọng ven biển Vũng Tàu, resort nghỉ dưỡng giữa Đà Lạt sương mù đến khu resort đẳng cấp tại Nha Trang và Đà Nẵng. Dù là chuyến đi cuối tuần cùng gia đình, kỳ trăng mật lãng mạn hay chuyến công tác ngắn ngày, chúng tôi luôn có không gian phù hợp để mỗi hành trình của bạn trở nên thật trọn vẹn.</p>
            </div>
        </div>

        {{-- Ảnh + tab menu --}}
        <div class="sg-service-body">
            <div class="sg-service-img-wrap">
                <img id="svcImg" src="{{ asset('assets/images/ks-bien1.jpg') }}" alt="Dịch vụ khách sạn" loading="lazy">
            </div>
            <div class="sg-service-tabs">
                <button class="sg-svc-tab active" data-img="{{ asset('assets/images/ks-bien1.jpg') }}" data-alt="Khách sạn cao cấp">
                    Khách sạn
                </button>
                <button class="sg-svc-tab" data-img="{{ asset('assets/images/ks-bien2.jpg') }}" data-alt="Khách sạn & Resort">
                    Khách sạn &amp; Resort
                </button>
            </div>
        </div>

    </div>
</section>
<script>
(function(){
    const tabs = document.querySelectorAll('.sg-svc-tab');
    const img  = document.getElementById('svcImg');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            img.style.opacity = '0';
            setTimeout(() => {
                img.src = tab.dataset.img;
                img.alt = tab.dataset.alt;
                img.style.opacity = '1';
            }, 220);
        });
    });
})();
</script>

{{-- Weekend Deals --}}
@if($weekendDeals->count())
<section class="home-section wd-section" style="background:#fff;">
    <div class="container">
        <div class="home-section-head">
            <div>
                <h2 class="home-section-title">Ưu đãi cho cuối tuần</h2>
                <p class="home-section-sub">Những ưu đãi đặc biệt chỉ có vào cuối tuần</p>
            </div>
            <div class="wd-nav-btns">
                <button class="wd-arrow" id="wdPrev" aria-label="Trước">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button class="wd-arrow" id="wdNext" aria-label="Tiếp">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        </div>
        <div class="wd-carousel-wrap">
            <div class="wd-track" id="wdTrack">
                @foreach($weekendDeals as $hotel)
                <div class="wd-slide">
                    @include('components.hotel-card', ['hotel' => $hotel])
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<style>
.wd-section .home-section-head { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:24px; }
.wd-nav-btns { display:flex; gap:8px; flex-shrink:0; }
.wd-arrow { width:38px; height:38px; border-radius:50%; border:1.5px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#1a202c; transition:all .2s; flex-shrink:0; }
.wd-arrow:hover { background:#004391; border-color:#004391; color:#fff; }
.wd-carousel-wrap { overflow:hidden; }
.wd-track { display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; -ms-overflow-style:none; cursor:grab; }
.wd-track::-webkit-scrollbar { display:none; }
.wd-track.is-dragging { cursor:grabbing; }
.wd-slide { flex:0 0 calc(33.333% - 14px); scroll-snap-align:start; min-width:0; }
.wd-slide .hotel-card { width:100%; }
@media(max-width:900px) { .wd-slide { flex:0 0 calc(50% - 10px); } }
@media(max-width:580px) { .wd-slide { flex:0 0 calc(85% - 10px); } }
</style>
<script>
(function(){
    const track = document.getElementById('wdTrack');
    const prev  = document.getElementById('wdPrev');
    const next  = document.getElementById('wdNext');
    if (!track) return;
    function slideWidth() { return track.querySelector('.wd-slide')?.offsetWidth + 20 || 340; }
    prev.addEventListener('click', () => track.scrollBy({ left: -slideWidth() * 3, behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left:  slideWidth() * 3, behavior: 'smooth' }));
    let isDown = false, startX, scrollLeft;
    track.addEventListener('mousedown', e => { isDown = true; track.classList.add('is-dragging'); startX = e.pageX - track.offsetLeft; scrollLeft = track.scrollLeft; });
    track.addEventListener('mouseleave', () => { isDown = false; track.classList.remove('is-dragging'); });
    track.addEventListener('mouseup',    () => { isDown = false; track.classList.remove('is-dragging'); });
    track.addEventListener('mousemove',  e => { if (!isDown) return; e.preventDefault(); track.scrollLeft = scrollLeft - (e.pageX - track.offsetLeft - startX) * 1.3; });
    track.addEventListener('click', e => { if (Math.abs(track.scrollLeft - scrollLeft) > 5) e.preventDefault(); }, true);
})();
</script>
@endif

{{-- Testimonials --}}
<section class="sg-testimonials-section">
    <div class="container">
        <div class="home-section-head home-section-head--center">
            <div>
                <span class="sg-service-label" style="display:block;margin-bottom:8px;">ĐÁNH GIÁ TRẢI NGHIỆM</span>
                <h2 class="sg-service-cursive" style="font-size:40px;">Trải nghiệm tuyệt vời</h2>
            </div>
        </div>

        @php
        $staticReviews = [
            ['name'=>'Minh Tuấn', 'hotel'=>'The Imperial Vung Tau Hotel & Resort', 'rating'=>9.3, 'initials'=>'MT', 'color'=>'#e91e8c',
             'comment'=>'Kiến trúc Victoria tráng lệ, phòng rộng rãi và sạch sẽ. Nhân viên phục vụ rất chuyên nghiệp, vị trí gần biển tiện lợi. Kỳ nghỉ cuối tuần tuyệt vời!'],
            ['name'=>'Thu Hà', 'hotel'=>'Marina Bay Vung Tau Resort & Spa', 'rating'=>8.9, 'initials'=>'TH', 'color'=>'#004391',
             'comment'=>'Hồ bơi view biển cực đẹp, hoàng hôn nhìn từ tầng thượng không thể tuyệt hơn. Spa thư giãn, nhân viên nhiệt tình. Chỉ 2 tiếng từ Sài Gòn mà như đến thiên đường!'],
            ['name'=>'Vy Hà', 'hotel'=>'Merperle Hon Tam Resort Nha Trang', 'rating'=>9.0, 'initials'=>'VH', 'color'=>'#7c3aed',
             'comment'=>'Resort trên đảo Hòn Tằm rất đặc biệt, biển xanh ngọc bích, không khí trong lành. Dịch vụ spa tuyệt vời, nhân viên hỗ trợ nhiệt tình. Cực kỳ hài lòng!'],
            ['name'=>'Tuấn Anh', 'hotel'=>'Ana Mandara Villas Dalat Resort & Spa', 'rating'=>8.5, 'initials'=>'TA', 'color'=>'#059669',
             'comment'=>'Những biệt thự Pháp cổ điển giữa vườn hoa Đà Lạt thật lãng mạn. Không khí mát mẻ, view đẹp, ẩm thực tinh tế. Sẽ giới thiệu cho bạn bè và quay lại!'],
        ];
        @endphp

        @php $avatarColors = ['#e91e8c','#004391','#7c3aed','#059669']; @endphp
        <div class="sg-testi-carousel-wrap">
            <button class="sg-testi-arrow sg-testi-arrow--prev" id="testiPrev">&#8592;</button>
            <div class="sg-testimonials-grid" id="testiGrid">
                @foreach($homeReviews as $rev)
                <div class="sg-testi-card">
                    <div class="sg-testi-stars">★★★★★</div>
                    <p class="sg-testi-comment">"{{ $rev->comment }}"</p>
                    <div class="sg-testi-author">
                        <div class="sg-testi-avatar" style="background:{{ $avatarColors[$loop->index % 4] }}">
                            {{ mb_strtoupper(mb_substr($rev->user->full_name ?? 'U', 0, 2, 'UTF-8'), 'UTF-8') }}
                        </div>
                        <div>
                            <div class="sg-testi-name">{{ $rev->user->full_name ?? 'Khách hàng' }}</div>
                            <div class="sg-testi-hotel">{{ $rev->hotel->name ?? '' }}</div>
                        </div>
                        <div class="sg-testi-score">{{ number_format($rev->rating, 1) }}</div>
                    </div>
                </div>
                @endforeach

                @if($homeReviews->isEmpty())
                @foreach($staticReviews as $i => $rev)
                <div class="sg-testi-card">
                    <div class="sg-testi-stars">★★★★★</div>
                    <p class="sg-testi-comment">"{{ $rev['comment'] }}"</p>
                    <div class="sg-testi-author">
                        <div class="sg-testi-avatar" style="background:{{ $rev['color'] }}">{{ $rev['initials'] }}</div>
                        <div>
                            <div class="sg-testi-name">{{ $rev['name'] }}</div>
                            <div class="sg-testi-hotel">{{ $rev['hotel'] }}</div>
                        </div>
                        <div class="sg-testi-score">{{ $rev['rating'] }}</div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <button class="sg-testi-arrow sg-testi-arrow--next" id="testiNext">&#8594;</button>
        </div>
    </div>
</section>
<script>
(function(){
    const grid = document.getElementById('testiGrid');
    const prev = document.getElementById('testiPrev');
    const next = document.getElementById('testiNext');
    if (!grid) return;
    const step = 320;
    prev.addEventListener('click', () => grid.scrollBy({ left: -step, behavior: 'smooth' }));
    next.addEventListener('click', () => grid.scrollBy({ left:  step, behavior: 'smooth' }));
    let isDown = false, startX, scrollLeft;
    grid.addEventListener('mousedown', e => { isDown = true; grid.classList.add('is-dragging'); startX = e.pageX - grid.offsetLeft; scrollLeft = grid.scrollLeft; });
    grid.addEventListener('mouseleave', () => { isDown = false; grid.classList.remove('is-dragging'); });
    grid.addEventListener('mouseup',    () => { isDown = false; grid.classList.remove('is-dragging'); });
    grid.addEventListener('mousemove',  e => { if (!isDown) return; e.preventDefault(); grid.scrollLeft = scrollLeft - (e.pageX - grid.offsetLeft - startX) * 1.3; });
    grid.addEventListener('click', e => { if (Math.abs(grid.scrollLeft - scrollLeft) > 5) e.preventDefault(); }, true);
})();
</script>

{{-- Cẩm nang du lịch --}}
@if(isset($blogPosts) && $blogPosts->count())
<section class="cnd-section">
    <div class="cnd-container">

        {{-- Header --}}
        <div class="cnd-header">
            <div class="cnd-header-left">
                <span class="cnd-icon">🗺️</span>
                <h2 class="cnd-title">Cẩm nang du lịch</h2>
            </div>
        </div>

        {{-- Swiper với arrow hai bên --}}
        <div class="cnd-carousel-wrap">
            <button class="cnd-arrow cnd-arrow--prev" id="cndPrev" aria-label="Trước">&#8592;</button>
            <div class="swiper cndSwiper">
                <div class="swiper-wrapper">
                    @foreach($blogPosts as $post)
                    <div class="swiper-slide">
                        <a href="{{ route('blog.show', $post) }}" class="cnd-card">
                            @if($post->thumb)
                            <img class="cnd-card-img" src="{{ str_starts_with($post->thumb,'http') ? $post->thumb : asset('storage/'.$post->thumb) }}" alt="{{ $post->category }}" loading="lazy"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='none';this.parentElement.style.background='linear-gradient(135deg,#1e3a5f,#2563eb)';">
                            @else
                            <div class="cnd-card-img cnd-card-img-fallback">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                            @endif
                            <div class="cnd-card-overlay"></div>
                            <div class="cnd-card-info">
                                <div class="cnd-card-name">{{ $post->category }}</div>
                                <div class="cnd-card-sub">Việt Nam</div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <button class="cnd-arrow cnd-arrow--next" id="cndNext" aria-label="Tiếp">&#8594;</button>
        </div>

        {{-- Xem thêm --}}
        <div class="cnd-more-wrap">
            <a href="{{ route('blog.index') }}" class="cnd-more-btn">Xem thêm →</a>
        </div>

    </div>
</section>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
// ── Cẩm nang du lịch Swiper ──────────────────────────────
(function() {
    const el = document.querySelector('.cndSwiper');
    if (!el) return;
    const swiper = new Swiper('.cndSwiper', {
        slidesPerView: 1.2,
        spaceBetween: 16,
        loop: false,
        grabCursor: true,
        speed: 420,
        breakpoints: {
            480:  { slidesPerView: 1.8, spaceBetween: 18 },
            768:  { slidesPerView: 2.4, spaceBetween: 20 },
            1024: { slidesPerView: 3.2, spaceBetween: 22 },
            1280: { slidesPerView: 4,   spaceBetween: 24 },
        },
    });
    document.getElementById('cndPrev')?.addEventListener('click', () => swiper.slidePrev());
    document.getElementById('cndNext')?.addEventListener('click', () => swiper.slideNext());
})();
</script>
<script>
const dropdown = document.getElementById('hsb-location-dropdown');
function showLocationDropdown() {
    document.querySelectorAll('.hsb-loc-item').forEach(el => el.style.display = 'flex');
    dropdown.style.display = 'block';
}
function filterLocations(val) {
    const q = val.toLowerCase();
    document.getElementById('hsb-location-id').value = '';
    document.querySelectorAll('.hsb-loc-item').forEach(el => {
        el.style.display = (q === '' || el.dataset.name.includes(q)) ? 'flex' : 'none';
    });
    dropdown.style.display = 'block';
}
function selectLocation(name, id) {
    document.getElementById('hsb-keyword-input').value = name;
    document.getElementById('hsb-location-id').value = id;
    dropdown.style.display = 'none';
    setTimeout(() => {
        try {
            const el = document.getElementById('h_checkin');
            if (el.showPicker) el.showPicker(); else el.focus();
        } catch(e) { document.getElementById('h_checkin').focus(); }
    }, 100);
}
function updateMinCheckout(checkinVal) {
    const checkout = document.getElementById('h_checkout');
    if (checkinVal) {
        const next = new Date(checkinVal);
        next.setDate(next.getDate() + 1);
        const nextStr = next.toISOString().split('T')[0];
        checkout.min = nextStr;
        const stayType = document.getElementById('hsb-stay-type').value;
        if (stayType === 'oneday') {
            checkout.value = nextStr;
        } else if (checkout.value && checkout.value <= checkinVal) {
            checkout.value = nextStr;
        }
    }
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.hsb-keyword')) dropdown.style.display = 'none';
});
// Gần tôi — geolocation + Haversine distance
const _locationCoords = {
    @foreach($locations as $loc)
    {{ $loc->id }}: { name: "{{ $loc->name }}", lat: {!! json_encode((float)($loc->lat ?? 0)) !!}, lng: {!! json_encode((float)($loc->lng ?? 0)) !!} },
    @endforeach
};
// Fallback tọa độ tĩnh nếu DB chưa có lat/lng
const _staticCoords = { 1: { lat:14.8094, lng:108.0225 }, 2: { lat:14.3544, lng:107.9927 }, 3: { lat:15.1214, lng:108.8054 } };
function _haversine(lat1, lng1, lat2, lng2) {
    const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLng = (lng2-lng1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}
function findNearbyHotels() {
    const spinner = document.getElementById('hsbNearbySpinner');
    const status  = document.getElementById('hsbNearbyStatus');
    const btn     = document.getElementById('hsbNearbyBtn');
    if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        status.textContent = 'Cần HTTPS để dùng (thử qua ngrok)';
        if (btn) btn.classList.add('is-unavailable');
        return;
    }
    if (!navigator.geolocation) { status.textContent = 'Trình duyệt không hỗ trợ vị trí'; return; }
    if (spinner) spinner.style.display = 'block';
    status.textContent = 'Đang xác định vị trí...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        const uLat = pos.coords.latitude, uLng = pos.coords.longitude;
        let nearest = null, minDist = Infinity;
        document.querySelectorAll('.hsb-loc-item').forEach(el => {
            const id = parseInt(el.dataset.id);
            const coord = _staticCoords[id] || { lat: 0, lng: 0 };
            const dist = _haversine(uLat, uLng, coord.lat, coord.lng);
            if (dist < minDist) { minDist = dist; nearest = el; }
        });
        if (spinner) spinner.style.display = 'none';
        if (nearest) {
            const locName = nearest.querySelector('.hsb-loc-name').textContent.trim();
            const locId   = nearest.dataset.id;
            document.getElementById('hsb-keyword-input').value = locName;
            document.getElementById('hsb-location-id').value   = locId;
            dropdown.style.display = 'none';
            status.textContent = '✓ Đã chọn: ' + locName + ' (~' + Math.round(minDist) + ' km)';
        }
    }, function(err) {
        if (spinner) spinner.style.display = 'none';
        if (err.code === 1) status.textContent = 'Hãy cho phép vị trí trong trình duyệt';
        else if (err.code === 2) status.textContent = 'Bật Location trong Settings → Privacy → Location';
        else status.textContent = 'Hết thời gian, hãy thử lại';
    }, { timeout: 12000, enableHighAccuracy: false });
}

// Hover + touch effect cho items
document.querySelectorAll('.hsb-loc-item').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = '#fdf2f8');
    el.addEventListener('mouseleave', () => el.style.background = '');
    el.addEventListener('touchstart', () => el.style.background = '#fdf2f8', { passive: true });
    el.addEventListener('touchend',   () => el.style.background = '');
});

// Guests & Children popup
let _adults = 1, _children = 0, _rooms = 1;
function toggleGuestsPopup() {
    const popup = document.getElementById('hsbGuestsPopup');
    const isOpen = popup.style.display !== 'none';
    popup.style.display = isOpen ? 'none' : 'block';
    // Overlay on mobile
    let overlay = document.getElementById('hsbGuestsOverlay');
    if (!isOpen) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'hsbGuestsOverlay';
            overlay.style.cssText = 'position:fixed;inset:0;background:transparent;z-index:9998;display:none;';
            overlay.addEventListener('click', () => toggleGuestsPopup());
            overlay.addEventListener('touchend', (e) => { e.preventDefault(); toggleGuestsPopup(); });
            document.body.appendChild(overlay);
        }
        overlay.style.display = window.innerWidth <= 1024 ? 'block' : 'none';
    } else {
        if (overlay) overlay.style.display = 'none';
    }
}
function updateGuestsDisplay() {
    let txt = _adults + ' người lớn';
    if (_children > 0) txt += ' · ' + _children + ' trẻ em';
    if (_rooms > 1) txt += ' · ' + _rooms + ' phòng';
    document.getElementById('hsbGuestsDisplay').textContent = txt;
    document.getElementById('h_guests').value = _adults;
    document.getElementById('h_children').value = _children;
    if (document.getElementById('h_rooms')) document.getElementById('h_rooms').value = _rooms;
}
function changeGuests(d) {
    _adults = Math.max(1, Math.min(8, _adults + d));
    document.getElementById('hsbAdultsVal').textContent = _adults;
    updateGuestsDisplay();
}
function changeChildren(d) {
    _children = Math.max(0, Math.min(6, _children + d));
    document.getElementById('hsbChildrenVal').textContent = _children;
    updateGuestsDisplay();
    renderChildAges();
}
function renderChildAges() {
    const wrap = document.getElementById('hsbChildAges');
    if (!wrap) return;
    if (_children === 0) { wrap.style.display = 'none'; wrap.innerHTML = ''; return; }
    const ageOptions = ['Dưới 1 tuổi', ...Array.from({length:17}, (_,i) => (i+1)+' tuổi')];
    let html = '<p style="font-size:12px;color:#64748b;margin:6px 0 10px">Vui lòng chọn tuổi của trẻ vào thời điểm nhận phòng.</p><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">';
    for (let i = 0; i < _children; i++) {
        html += `<div><div style="font-size:12px;font-weight:700;margin-bottom:4px;color:#1a202c">Trẻ em ${i+1}</div>
        <select name="child_ages[]" class="hsb-gp-age-select" style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;color:#1a202c;">
            <option value="">Chọn tuổi</option>
            ${ageOptions.map((a,v)=>`<option value="${v}">${a}</option>`).join('')}
        </select></div>`;
    }
    html += '</div>';
    wrap.innerHTML = html;
    wrap.style.display = 'block';
}
function changeRooms(d) {
    _rooms = Math.max(1, Math.min(5, _rooms + d));
    document.getElementById('hsbRoomsVal').textContent = _rooms;
    updateGuestsDisplay();
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.hsb-guests') && !e.target.closest('#hsbGuestsOverlay')) {
        const p = document.getElementById('hsbGuestsPopup');
        const overlay = document.getElementById('hsbGuestsOverlay');
        if (p) p.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
    }
});

// Search type tabs — set stay_type
function setStayType(type, btn) {
    document.querySelectorAll('.hsb-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('hsb-stay-type').value = type;
    const checkoutField = document.querySelector('.hsb-field.hsb-date:last-of-type');
    if (type === 'oneday') {
        if (checkoutField) checkoutField.style.display = 'none';
        autoSetOneDay();
    } else {
        if (checkoutField) checkoutField.style.display = '';
    }
}
function autoSetOneDay() {
    const checkinVal = document.getElementById('h_checkin').value;
    if (checkinVal) {
        const d = new Date(checkinVal);
        d.setDate(d.getDate() + 1);
        document.getElementById('h_checkout').value = d.toISOString().split('T')[0];
    }
}

function setHomeRating(val, btn) {
    btn.closest('.hsb-fr-chips').querySelectorAll('.hsb-fr-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('hsb-rating-input').value = val;
}
function setHomePrice(min, max, btn) {
    btn.closest('.hsb-fr-chips').querySelectorAll('.hsb-fr-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('hsb-minprice-input').value = min;
    document.getElementById('hsb-maxprice-input').value = max;
}
function setHomeType(val, btn) {
    btn.closest('.hsb-fr-group').querySelectorAll('.hsb-fr-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('hsb-type-input').value = val;
}

// Date picker: chỉ dùng showPicker trên desktop, mobile dùng native tap
if (!('ontouchstart' in window)) {
    document.querySelectorAll('.hsb-field input[type="date"]').forEach(function(input) {
        input.addEventListener('click', function() {
            try { this.showPicker(); } catch(e) {}
        });
    });
}

// Featured hotels location tab switching
function switchFhrTab(btn, paneId) {
    document.querySelectorAll('.fhr-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.fhr-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const pane = document.getElementById(paneId);
    if (pane) pane.classList.add('active');
}
</script>
@endpush
