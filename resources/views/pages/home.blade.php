@extends('layouts.app')
@section('title', 'Trang chủ')
@section('body_class', 'page-home')
@section('header_class', '')

@section('preload_assets')
<link rel="preload" as="image" href="{{ asset('assets/images/hero-bg.jpg') }}" fetchpriority="high">
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════════
     HERO — Full‑screen slideshow + search bar
     ══════════════════════════════════════════════════════ --}}
<div class="hero-search-section">

    <div class="hero-slides">
        <div class="hero-slide" style="--delay:0s">
            <img src="{{ asset('assets/images/hero-bg.jpg') }}" alt="" fetchpriority="high">
        </div>
        <div class="hero-slide" style="--delay:2s">
            <img src="{{ asset('assets/images/anhbien.jpg') }}" alt="" loading="lazy">
        </div>
    </div>

    <div class="hero-content">
        <div class="hero-search-title">Chúng tôi cung cấp không gian nghỉ dưỡng<br>sang trọng dành cho bạn</div>
        <div class="hero-search-sub">Hàng trăm khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</div>
    </div>

    <div class="hero-booking-bar">
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
                    <div class="hsb-loc-nearby" id="hsbNearbyBtn" onclick="findNearbyHotels()">
                        <div class="hsb-loc-nearby-icon">
                            <svg width="15" height="15" fill="none" stroke="#0066cc" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div class="hsb-loc-name" style="color:#0066cc;font-weight:700;">Gần tôi</div>
                            <div class="hsb-loc-count" id="hsbNearbyStatus">Dùng vị trí của bạn để tìm khách sạn</div>
                        </div>
                        <svg id="hsbNearbySpinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2" style="display:none;margin-left:auto;animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-dasharray="40" stroke-dashoffset="10"/></svg>
                    </div>
                    <div class="hsb-dropdown-divider"></div>
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

            {{-- Guests popup --}}
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

        {{-- Filter row 1 --}}
        <div class="hsb-filter-row" id="home-filter-row">
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
                <a href="javascript:void(0)" onclick="setHomeType('homestay-resort', this)" class="hsb-fr-chip">🏝️ Resort</a>
            </div>
        </div>

        <input type="hidden" name="type"      id="hsb-type-input"     form="hsbForm">
        <input type="hidden" name="rating"    id="hsb-rating-input"   form="hsbForm">
        <input type="hidden" name="min_price" id="hsb-minprice-input" form="hsbForm">
        <input type="hidden" name="max_price" id="hsb-maxprice-input" form="hsbForm">
        <input type="hidden" name="sort"      id="hsb-sort-input"     form="hsbForm" value="rating">
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     TRUST STRIP
     ══════════════════════════════════════════════════════ --}}
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


{{-- ══════════════════════════════════════════════════════
     HOTEL SHOWCASE — Editorial picks
     ══════════════════════════════════════════════════════ --}}
<section class="sg-hotel-showcase">
    <div class="container">
        <div class="home-section-head home-section-head--center" style="margin-bottom:32px;">
            <div>
                <span class="tz-about-label" style="display:block;margin-bottom:8px;">ĐIỂM NỔI BẬT</span>
                <h2 class="home-section-title" style="color:#1B3A6B;">Không gian nghỉ dưỡng được yêu thích nhất</h2>
                <p class="home-section-sub">Những khách sạn &amp; resort được khách hàng đánh giá cao nhất</p>
            </div>
        </div>

        <div class="sg-showcase-grid">
            <div class="sg-showcase-card">
                <div class="sg-showcase-img-wrap">
                    <img src="{{ asset('assets/images/hotels/01KQ7RMTP55JMKGPP4AYY9VA61.jpg') }}" alt="The Imperial Vung Tau Hotel & Resort" loading="lazy">
                </div>
                <div class="sg-showcase-info">
                    <span class="sg-showcase-badge">Khách sạn 5 sao · Vũng Tàu</span>
                    <h3 class="sg-showcase-title">The Imperial Vung Tau Hotel &amp; Resort</h3>
                    <p class="sg-showcase-desc">Tọa lạc tại vị trí đắc địa giữa trái tim phường Thắng Tam, The Imperial Vung Tau là biểu tượng kiến trúc Victoria kiêu sa bên bờ biển Vũng Tàu. Không gian sự kiện hoàng gia, khu Spa cao cấp và dịch vụ tinh tế — tất cả cộng hưởng để mang đến một kỳ nghỉ dưỡng đẳng cấp khó quên.</p>
                    <a href="{{ route('hotels.show', 7) }}" class="sg-showcase-btn">Đặt phòng ngay</a>
                </div>
            </div>

            <div class="sg-showcase-card sg-showcase-card--reverse">
                <div class="sg-showcase-img-wrap">
                    <img src="{{ asset('assets/images/hotels/01KQ658AECY4YJ9AGAF4J1VAGJ.jpg') }}" alt="Marina Bay Vung Tau Resort" loading="lazy">
                </div>
                <div class="sg-showcase-info">
                    <span class="sg-showcase-badge">Resort 4 sao · Vũng Tàu</span>
                    <h3 class="sg-showcase-title">Marina Bay Vung Tau Resort &amp; Spa</h3>
                    <p class="sg-showcase-desc">Tọa lạc ngay mặt tiền biển Bãi Trước trên tuyến đường Trần Phú sầm uất, Marina Bay Resort sở hữu tầm nhìn panorama trực diện ra biển. Hồ bơi ngoài trời view biển, quầy bar tầng thượng và trung tâm Spa hơn 20 liệu trình — điểm đến lý tưởng cho kỳ nghỉ cuối tuần chỉ 2 giờ từ TP.HCM.</p>
                    <a href="{{ route('hotels.show', 9) }}" class="sg-showcase-btn">Đặt phòng ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     NHIỀU LỰA CHỌN KHÁCH SẠN — Tabs by city
     ══════════════════════════════════════════════════════ --}}
@if($featuredByLocation->isNotEmpty())
<section class="hlc-section">
    <div class="container">
        <div class="home-section-head home-section-head--center" style="margin-bottom:28px;">
            <div>
                <span class="tz-about-label" style="display:block;margin-bottom:8px;">PHÒNG NỔI BẬT</span>
                <h2 class="home-section-title" style="color:#1B3A6B;">Nhiều lựa chọn khách sạn & Resort</h2>
                <p class="home-section-sub">Hàng trăm phòng nghỉ tại các điểm đến hàng đầu Việt Nam</p>
            </div>
        </div>

        <div class="hlc-tabs-wrap">
            @foreach($featuredByLocation as $i => $loc)
            <button class="hlc-tab {{ $i === 0 ? 'active' : '' }}" data-target="hlc-{{ $loc['id'] }}">{{ $loc['name'] }}</button>
            @endforeach
            <a href="{{ route('hotels.index') }}" class="hlc-see-all">Tất cả →</a>
        </div>

        @foreach($featuredByLocation as $i => $loc)
        <div class="hlc-grid {{ $i > 0 ? 'hlc-hidden' : '' }}" id="hlc-{{ $loc['id'] }}">
            @foreach($loc['hotels'] as $hotel)
            @php
                $discount = ($hotel->old_price && $hotel->old_price > $hotel->price)
                    ? round((1 - $hotel->price / $hotel->old_price) * 100) : null;
                $ratingLabel = match(true) {
                    $hotel->rating >= 9.0 => 'Trên cả tuyệt vời',
                    $hotel->rating >= 8.5 => 'Xuất sắc',
                    $hotel->rating >= 8.0 => 'Tuyệt vời',
                    $hotel->rating >= 7.0 => 'Rất tốt',
                    default               => 'Tốt',
                };
                $locationBadge = $hotel->location->name ?? null;
            @endphp
            <a href="{{ route('hotels.show', $hotel) }}" class="hlc-card">
                <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" loading="lazy">
                @if($discount)
                <span class="hlc-badge-save">-{{ $discount }}%</span>
                @endif
                @if($hotel->type === 'resort')
                <span class="hlc-badge-type hlc-badge-resort">Resort</span>
                @else
                <span class="hlc-badge-type hlc-badge-hotel">Khách sạn</span>
                @endif
                <div class="hlc-card-info">
                    <div class="hlc-card-meta">
                        @if($hotel->stars)
                        <span class="hlc-stars">{{ str_repeat('★', (int)$hotel->stars) }}</span>
                        @endif
                        <span class="hlc-score">{{ number_format($hotel->rating, 1) }}</span>
                        <span class="hlc-score-label">{{ $ratingLabel }}</span>
                    </div>
                    <div class="hlc-name">{{ $hotel->name }}</div>
                    <div class="hlc-price-row">
                        @if($hotel->old_price && $hotel->old_price > $hotel->price)
                        <span class="hlc-old-price">{{ number_format($hotel->old_price) }}đ</span>
                        @endif
                        <span class="hlc-price">{{ number_format($hotel->price) }}đ</span>
                        <span class="hlc-per-night">/đêm</span>
                    </div>
                    @if($locationBadge)
                    <div class="hlc-location-tag">📍 {{ $locationBadge }}</div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endforeach
    </div>
</section>

@push('styles')
<style>
.hlc-section { padding: 48px 0 56px; background: #fff; }

.hlc-tabs-wrap { display: flex; gap: 0; flex-wrap: wrap; margin-bottom: 28px; border-bottom: 2px solid #e2e8f0; align-items: center; }
.hlc-tab { padding: 10px 24px; border: none; border-bottom: 3px solid transparent; background: transparent; color: #718096; font-size: 14px; font-weight: 500; cursor: pointer; transition: all .18s; margin-bottom: -2px; }
.hlc-tab:hover { color: #1B3A6B; }
.hlc-tab.active { color: #1B3A6B; border-bottom-color: #1B3A6B; font-weight: 700; }
.hlc-see-all { margin-left: auto; font-size: 13px; font-weight: 600; color: #2D5BE3; text-decoration: none; padding: 8px 0; }
.hlc-see-all:hover { color: #1B3A6B; }

/* Horizontal scroll row — like location destination cards */
.hlc-grid { display: flex; gap: 20px; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 16px; scrollbar-width: none; -ms-overflow-style: none; }
.hlc-grid::-webkit-scrollbar { display: none; }
.hlc-hidden { display: none !important; }

/* Portrait card — full-bleed image */
.hlc-card { flex: 0 0 280px; height: 400px; border-radius: 18px; overflow: hidden; position: relative; display: block; text-decoration: none; color: #fff; scroll-snap-align: start; transition: transform .32s cubic-bezier(.25,.8,.25,1), box-shadow .32s; }
.hlc-card:hover { transform: translateY(-10px) scale(1.03); box-shadow: 0 28px 72px rgba(0,0,0,.32); }
.hlc-grid:has(.hlc-card:hover) .hlc-card:not(:hover) { filter: brightness(0.72) scale(0.97); transition: filter .22s, transform .22s; }

/* Full-bleed image */
.hlc-card > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .45s; }
.hlc-card:hover > img { transform: scale(1.07); }

/* Discount badge */
.hlc-badge-save { position: absolute; top: 14px; right: 14px; background: #f97316; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; z-index: 2; }

/* Property type badge */
.hlc-badge-type { position: absolute; top: 14px; left: 14px; font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; z-index: 2; letter-spacing: .3px; backdrop-filter: blur(4px); }
.hlc-badge-resort { background: rgba(16,185,129,.85); color: #fff; }
.hlc-badge-hotel  { background: rgba(30,58,107,.75); color: #fff; }

/* Info overlay at bottom */
.hlc-card-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 56px 18px 20px; background: linear-gradient(to top, rgba(0,8,32,.92) 55%, transparent); z-index: 1; }
.hlc-card-meta { display: flex; align-items: center; gap: 7px; margin-bottom: 8px; }
.hlc-stars { color: #f59e0b; font-size: 12px; letter-spacing: 1px; }
.hlc-score { background: #1B3A6B; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 5px; flex-shrink: 0; }
.hlc-score-label { font-size: 11px; color: rgba(255,255,255,.75); font-weight: 500; }
.hlc-name { font-size: 15px; font-weight: 700; line-height: 1.4; margin-bottom: 10px; text-shadow: 0 1px 4px rgba(0,0,0,.5); }
.hlc-price-row { display: flex; align-items: baseline; gap: 6px; }
.hlc-price { font-size: 15px; font-weight: 700; color: #fff; }
.hlc-per-night { font-size: 11px; color: rgba(255,255,255,.7); }
.hlc-old-price { font-size: 11px; color: rgba(255,255,255,.5); text-decoration: line-through; }
.hlc-location-tag { display: inline-flex; align-items: center; gap: 3px; font-size: 11px; color: rgba(255,255,255,.75); margin-top: 8px; }

@media(max-width: 720px) { .hlc-card { flex: 0 0 240px; height: 360px; } }
@media(max-width: 440px) { .hlc-card { flex: 0 0 210px; height: 320px; } }
</style>
@endpush

<script>
(function(){
    document.querySelectorAll('.hlc-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.hlc-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.hlc-grid').forEach(g => g.classList.add('hlc-hidden'));
            this.classList.add('active');
            document.getElementById(this.dataset.target)?.classList.remove('hlc-hidden');
        });
    });
})();
</script>
@endif

{{-- ══════════════════════════════════════════════════════
     DESTINATIONS — Popular locations carousel
     ══════════════════════════════════════════════════════ --}}
@if($locations->count())
<section class="home-section" style="background:#fff;">
    <div class="container">
        <div class="home-section-head home-section-head--center">
            <div>
                <span class="tz-about-label" style="display:block;margin-bottom:8px;">ĐIỂM ĐẾN</span>
                <h2 class="home-section-title" style="color:#1B3A6B;">Địa điểm đang thịnh hành</h2>
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
            let isDown = false, startX, scrollLeft;
            grid.addEventListener('mousedown', e => { isDown = true; grid.classList.add('is-dragging'); startX = e.pageX - grid.offsetLeft; scrollLeft = grid.scrollLeft; });
            grid.addEventListener('mouseleave', () => { isDown = false; grid.classList.remove('is-dragging'); });
            grid.addEventListener('mouseup',    () => { isDown = false; grid.classList.remove('is-dragging'); });
            grid.addEventListener('mousemove',  e => { if (!isDown) return; e.preventDefault(); const x = e.pageX - grid.offsetLeft; grid.scrollLeft = scrollLeft - (x - startX) * 1.4; });
            grid.addEventListener('click', e => { if (Math.abs(grid.scrollLeft - scrollLeft) > 5) e.preventDefault(); }, true);
        })();
        </script>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════
     TESTIMONIALS
     ══════════════════════════════════════════════════════ --}}
<section class="sg-testimonials-section">
    <div class="container">
        <div class="home-section-head home-section-head--center">
            <div>
                <span class="tz-about-label" style="display:block;margin-bottom:8px;">ĐÁNH GIÁ TRẢI NGHIỆM</span>
                <h2 class="home-section-title" style="color:#1B3A6B;">Khách hàng nói gì về chúng tôi</h2>
            </div>
        </div>

        @php
        $staticReviews = [
            ['name'=>'Minh Tuấn', 'hotel'=>'The Imperial Vung Tau Hotel & Resort', 'rating'=>9.3, 'initials'=>'MT', 'color'=>'#2D5BE3',
             'comment'=>'Kiến trúc Victoria tráng lệ, phòng rộng rãi và sạch sẽ. Nhân viên phục vụ rất chuyên nghiệp, vị trí gần biển tiện lợi. Kỳ nghỉ cuối tuần tuyệt vời!'],
            ['name'=>'Thu Hà', 'hotel'=>'Marina Bay Vung Tau Resort & Spa', 'rating'=>8.9, 'initials'=>'TH', 'color'=>'#1B3A6B',
             'comment'=>'Hồ bơi view biển cực đẹp, hoàng hôn nhìn từ tầng thượng không thể tuyệt hơn. Spa thư giãn, nhân viên nhiệt tình. Chỉ 2 tiếng từ Sài Gòn mà như đến thiên đường!'],
            ['name'=>'Vy Hà', 'hotel'=>'Merperle Hon Tam Resort Nha Trang', 'rating'=>9.0, 'initials'=>'VH', 'color'=>'#7c3aed',
             'comment'=>'Resort trên đảo Hòn Tằm rất đặc biệt, biển xanh ngọc bích, không khí trong lành. Dịch vụ spa tuyệt vời, nhân viên hỗ trợ nhiệt tình. Cực kỳ hài lòng!'],
            ['name'=>'Tuấn Anh', 'hotel'=>'Ana Mandara Villas Dalat Resort & Spa', 'rating'=>8.5, 'initials'=>'TA', 'color'=>'#059669',
             'comment'=>'Những biệt thự Pháp cổ điển giữa vườn hoa Đà Lạt thật lãng mạn. Không khí mát mẻ, view đẹp, ẩm thực tinh tế. Sẽ giới thiệu cho bạn bè và quay lại!'],
        ];
        @endphp

        @php $avatarColors = ['#2D5BE3','#1B3A6B','#7c3aed','#059669']; @endphp
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

{{-- ══════════════════════════════════════════════════════
     CẨM NANG DU LỊCH
     ══════════════════════════════════════════════════════ --}}
@if(isset($blogPosts) && $blogPosts->count())
<section class="cnd-section">
    <div class="cnd-container">
        <div class="cnd-header">
            <div class="cnd-header-left">
                <span class="cnd-icon">🗺️</span>
                <h2 class="cnd-title">Cẩm nang du lịch</h2>
            </div>
        </div>

        <div class="cnd-carousel-wrap">
            <button type="button" class="cnd-arrow cnd-arrow--prev" id="cndPrev" aria-label="Trước">&#8592;</button>
            <div class="cndSwiper" id="cndTrack">
                <div class="cnd-track">
                    @foreach($blogPosts as $post)
                    <div class="cnd-slide">
                        <a href="{{ route('blog.show', $post) }}" class="cnd-card" draggable="false">
                            @if($post->thumb)
                            <img class="cnd-card-img" src="{{ str_starts_with($post->thumb,'http') ? $post->thumb : asset('storage/'.$post->thumb) }}" alt="{{ $post->category }}" loading="lazy" draggable="false"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='none';this.parentElement.style.background='linear-gradient(135deg,#1B3A6B,#2D5BE3)';">
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
            <button type="button" class="cnd-arrow cnd-arrow--next" id="cndNext" aria-label="Tiếp">&#8594;</button>
        </div>

        <div class="cnd-more-wrap">
            <a href="{{ route('blog.index') }}" class="cnd-more-btn">Xem thêm →</a>
        </div>
    </div>
</section>
<script>
(function(){
    var el   = document.getElementById('cndTrack');
    var prev = document.getElementById('cndPrev');
    var next = document.getElementById('cndNext');
    if (!el) return;

    /* Force scroll — override mọi CSS conflict */
    el.style.overflowX = 'scroll';
    el.style.overflowY = 'hidden';

    function step() {
        var s = el.querySelector('.cnd-slide');
        return (s && s.offsetWidth > 50 ? s.offsetWidth : 280) + 20;
    }

    /* Buttons — dùng addEventListener như drag (KHÔNG dùng onclick) */
    if (prev) prev.addEventListener('click', function(e){
        e.stopPropagation();
        el.scrollLeft = Math.max(0, el.scrollLeft - step());
    });
    if (next) next.addEventListener('click', function(e){
        e.stopPropagation();
        el.scrollLeft = Math.min(el.scrollWidth - el.clientWidth, el.scrollLeft + step());
    });

    /* Drag chuột */
    var down = false, sx = 0, ss = 0, moved = false;
    el.addEventListener('mousedown', function(e){
        if (e.button !== 0) return;
        down = true; moved = false;
        sx = e.clientX; ss = el.scrollLeft;
        el.style.cursor = 'grabbing';
        e.preventDefault();
    });
    el.addEventListener('dragstart', function(e){ e.preventDefault(); });
    document.addEventListener('mouseup', function(){ down = false; el.style.cursor = ''; });
    document.addEventListener('mousemove', function(e){
        if (!down) return;
        var dx = e.clientX - sx;
        if (Math.abs(dx) > 3) { moved = true; el.scrollLeft = ss - dx; }
    });
    el.addEventListener('click', function(e){
        if (moved) { moved = false; e.preventDefault(); e.stopPropagation(); }
    }, true);

    /* Touch swipe */
    var tx = 0, ts = 0;
    el.addEventListener('touchstart', function(e){ tx = e.touches[0].clientX; ts = el.scrollLeft; }, {passive:true});
    el.addEventListener('touchmove',  function(e){ el.scrollLeft = ts - (e.touches[0].clientX - tx); }, {passive:true});
})();
</script>
@endif

{{-- ══════════════════════════════════════════════════════
     WHY CHOOSE US (2 cột)
     ══════════════════════════════════════════════════════ --}}
<section class="tz-why-section">
    <div class="tz-why-inner">

        {{-- Left: image --}}
        <div class="tz-why-img-wrap">
            <img src="{{ asset('assets/images/amiana-resort-nha-trang-4.webp') }}" alt="Resort Nha Trang" loading="lazy">
        </div>

        {{-- Right: features --}}
        <div>
            <span class="tz-why-label">TẠI SAO CHỌN STAYGO</span>
            <h2 class="tz-why-title">Trải nghiệm nghỉ dưỡng thượng hạng với sự an tâm tuyệt đối</h2>
            <p class="tz-why-desc">StayGo được xây dựng với triết lý đặt khách hàng làm trung tâm. Chúng tôi không chỉ cung cấp nơi lưu trú — chúng tôi kiến tạo những kỷ niệm khó quên cho mỗi hành trình của bạn.</p>

            <div class="tz-why-features">
                <div class="tz-why-feat">
                    <div class="tz-why-icon">🌟</div>
                    <div class="tz-why-feat-content">
                        <div class="tz-why-feat-title">Cuộc sống thư giãn, đẳng cấp</div>
                        <div class="tz-why-feat-desc">Mỗi khách sạn và resort tại StayGo đều được kiểm duyệt kỹ lưỡng về chất lượng phòng ở, dịch vụ và vệ sinh — đảm bảo trải nghiệm xứng đáng với từng đồng bạn bỏ ra.</div>
                    </div>
                </div>
                <div class="tz-why-feat">
                    <div class="tz-why-icon">🔒</div>
                    <div class="tz-why-feat-content">
                        <div class="tz-why-feat-title">An toàn thanh toán cao cấp</div>
                        <div class="tz-why-feat-desc">Hệ thống thanh toán bảo mật đa lớp với VNPay, MoMo và thẻ quốc tế. Thông tin cá nhân và giao dịch của bạn luôn được mã hóa và bảo vệ tuyệt đối.</div>
                    </div>
                </div>
                <div class="tz-why-feat">
                    <div class="tz-why-icon">💬</div>
                    <div class="tz-why-feat-content">
                        <div class="tz-why-feat-title">Hỗ trợ 24/7, tận tâm</div>
                        <div class="tz-why-feat-desc">Đội ngũ hỗ trợ khách hàng của StayGo luôn sẵn sàng giải đáp mọi thắc mắc, hỗ trợ đặt phòng và xử lý sự cố nhanh chóng — bất kể ngày hay đêm.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
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
const _locationCoords = {
    @foreach($locations as $loc)
    {{ $loc->id }}: { name: "{{ $loc->name }}", lat: {!! json_encode((float)($loc->lat ?? 0)) !!}, lng: {!! json_encode((float)($loc->lng ?? 0)) !!} },
    @endforeach
};
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

document.querySelectorAll('.hsb-loc-item').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = '#f0f4ff');
    el.addEventListener('mouseleave', () => el.style.background = '');
    el.addEventListener('touchstart', () => el.style.background = '#f0f4ff', { passive: true });
    el.addEventListener('touchend',   () => el.style.background = '');
});

let _adults = 1, _children = 0, _rooms = 1;
function toggleGuestsPopup() {
    const popup = document.getElementById('hsbGuestsPopup');
    const isOpen = popup.style.display !== 'none';
    popup.style.display = isOpen ? 'none' : 'block';
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
if (!('ontouchstart' in window)) {
    document.querySelectorAll('.hsb-field input[type="date"]').forEach(function(input) {
        input.addEventListener('click', function() {
            try { this.showPicker(); } catch(e) {}
        });
    });
}
</script>
@endpush
