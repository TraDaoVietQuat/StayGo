@extends('layouts.app')
@section('title', 'Khách sạn')
@section('header_class', '')

@section('content')

{{-- Hero fullscreen — giống trang chủ, dùng ảnh biển --}}
<div class="hero-search-section">

    <div class="hero-slides">
        <div class="hero-slide" style="--delay:0s">
            <img src="{{ asset('assets/images/ks-bien1.jpg') }}" alt="" fetchpriority="high">
        </div>
        <div class="hero-slide" style="--delay:2s">
            <img src="{{ asset('assets/images/ks-bien2.jpg') }}" alt="" loading="lazy">
        </div>
    </div>

    <div class="hero-content">
        <div class="hero-search-title">Tìm khách sạn hoàn hảo cho bạn</div>
        <div class="hero-search-sub">Hàng trăm khách sạn tại Đà Lạt, Nha Trang, Vũng Tàu &amp; Đà Nẵng</div>

        <div class="hsb-tabs-wrap">
            <button type="button" class="hsb-tab-btn {{ request('stay_type','night')==='night' ? 'active' : '' }}" onclick="setStayType('night', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Qua đêm
            </button>
            <button type="button" class="hsb-tab-btn {{ request('stay_type')==='oneday' ? 'active' : '' }}" onclick="setStayType('oneday', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>
                1 ngày
            </button>
        </div>
    </div>

    <div class="hero-booking-bar">
        <form action="{{ route('hotels.index') }}" method="GET" class="hero-search-box has-filter-row" id="hsbForm">
            <input type="hidden" name="location" id="hsb-location-id" value="{{ request('location') }}">
            <input type="hidden" name="stay_type" id="hsb-stay-type" value="{{ request('stay_type','night') }}">

            <div class="hsb-field hsb-keyword">
                <span class="hsb-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Điểm đến / Tên khách sạn
                </span>
                <input class="hsb-input" type="text" name="keyword" id="hsb-keyword-input"
                    value="{{ request('keyword') }}"
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

            <div class="hsb-field hsb-date">
                <span class="hsb-label hsb-label-checkin">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Nhận phòng
                </span>
                <input class="hsb-input" type="date" name="checkin" id="h_checkin"
                    value="{{ request('checkin', $checkin ?? '') }}"
                    min="{{ date('Y-m-d') }}"
                    onchange="updateMinCheckout(this.value)">
            </div>

            <div class="hsb-field hsb-date">
                <span class="hsb-label hsb-label-checkout">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Trả phòng
                </span>
                <input class="hsb-input" type="date" name="checkout" id="h_checkout"
                    value="{{ request('checkout', $checkout ?? '') }}"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>

            <div class="hsb-field hsb-guests" style="position:relative;">
                <span class="hsb-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    Số khách
                </span>
                @php $initGuests = request('guests', $guests ?? 1); $initChildren = request('children', 0); @endphp
                <input type="hidden" name="guests" id="h_guests" value="{{ $initGuests }}">
                <input type="hidden" name="children" id="h_children" value="{{ $initChildren }}">
                <div class="hsb-input hsb-guests-display" id="hsbGuestsDisplay" onclick="toggleGuestsPopup()" style="cursor:pointer;user-select:none;">
                    {{ $initGuests }} người lớn{{ $initChildren > 0 ? ' · '.$initChildren.' trẻ em' : '' }}
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
                            <span id="hsbAdultsVal">{{ $initGuests }}</span>
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
                            <span id="hsbChildrenVal">{{ $initChildren }}</span>
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

        {{-- Filter row thứ 2 bên trong booking bar --}}
        <div class="hsb-filter-row">
            {{-- Đánh giá --}}
            <div class="hsb-fr-group">
                <span class="hsb-fr-label">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Đánh giá
                </span>
                <div class="hsb-fr-chips">
                    @foreach([['','Tất cả'],['7','7+'],['8','8+'],['9','9+']] as [$val,$lbl])
                    <a href="?{{ http_build_query(array_merge(request()->except('rating'), $val ? ['rating'=>$val] : [])) }}"
                       class="hsb-fr-chip {{ request('rating')==$val && $val ? 'active' : ((!$val && !request('rating')) ? 'active' : '') }}">{{ $lbl }}</a>
                    @endforeach
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
                    <a href="?{{ http_build_query(array_merge(request()->except(['min_price','max_price']), ['max_price'=>500000])) }}"
                       class="hsb-fr-chip {{ request('max_price')==500000 && !request('min_price') ? 'active' : '' }}">Dưới 500k</a>
                    <a href="?{{ http_build_query(array_merge(request()->except(['min_price','max_price']), ['min_price'=>500000,'max_price'=>1000000])) }}"
                       class="hsb-fr-chip {{ request('min_price')==500000 && request('max_price')==1000000 ? 'active' : '' }}">500k–1tr</a>
                    <a href="?{{ http_build_query(array_merge(request()->except(['min_price','max_price']), ['min_price'=>1000000])) }}"
                       class="hsb-fr-chip {{ request('min_price')==1000000 && !request('max_price') ? 'active' : '' }}">Trên 1tr</a>
                </div>
            </div>

            <div class="hsb-fr-sep"></div>

            {{-- Sắp xếp --}}
            <div class="hsb-fr-group">
                <span class="hsb-fr-label">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                    Sắp xếp
                </span>
                <form method="GET" action="{{ route('hotels.index') }}" id="sortForm" style="display:inline">
                    @foreach(request()->except('sort') as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $i => $val)
                    <input type="hidden" name="{{ $k }}[{{ $i }}]" value="{{ $val }}">
                            @endforeach
                        @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <select name="sort" class="hsb-fr-select" onchange="document.getElementById('sortForm').submit()">
                        <option value="rating"    {{ request('sort','rating')=='rating'    ? 'selected':'' }}>Đánh giá cao nhất</option>
                        <option value="price_asc" {{ request('sort')=='price_asc'          ? 'selected':'' }}>Giá thấp → cao</option>
                        <option value="price_desc"{{ request('sort')=='price_desc'         ? 'selected':'' }}>Giá cao → thấp</option>
                        <option value="popular"   {{ request('sort')=='popular'            ? 'selected':'' }}>Phổ biến nhất</option>
                    </select>
                </form>
            </div>

            @if(request()->hasAny(['keyword','location','min_price','max_price','rating','checkin','checkout','type']))
            <a href="{{ route('hotels.index') }}" class="hsb-fr-clear">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Xóa bộ lọc
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Main content --}}
<div class="hl-page-wrap" style="padding-top:24px;">

    {{-- Result header --}}
    <div class="hl-result-header">
        <div class="hl-result-title">
            <h2>
                @if(request('keyword'))
                    Kết quả cho "<em>{{ request('keyword') }}</em>"
                @elseif(request('location'))
                    Khách sạn tại {{ $locations->firstWhere('id', request('location'))?->name ?? '' }}
                @else
                    Tất cả khách sạn
                @endif
            </h2>
            <span class="hl-result-count">{{ number_format($hotels->total()) }} kết quả</span>
        </div>

        @php $hasFilter = request()->hasAny(['keyword','location','min_price','max_price','rating','checkin','checkout']); @endphp
        @if($hasFilter)
        <div class="hl-active-filters">
            @if(request('checkin'))<span class="hl-af-tag">📅 {{ request('checkin') }} → {{ request('checkout','?') }} <a href="?{{ http_build_query(request()->except(['checkin','checkout'])) }}">×</a></span>@endif
            @if(request('location'))<span class="hl-af-tag">📍 {{ $locations->firstWhere('id', request('location'))?->name }} <a href="?{{ http_build_query(request()->except('location')) }}">×</a></span>@endif
            @if(request('rating'))<span class="hl-af-tag">⭐ {{ request('rating') }}+ điểm <a href="?{{ http_build_query(request()->except('rating')) }}">×</a></span>@endif
            @if(request('min_price')||request('max_price'))<span class="hl-af-tag">💰 {{ request('min_price') ? number_format(request('min_price')).'đ' : '0' }} – {{ request('max_price') ? number_format(request('max_price')).'đ' : '∞' }} <a href="?{{ http_build_query(request()->except(['min_price','max_price'])) }}">×</a></span>@endif
        </div>
        @endif
    </div>

    {{-- Tab lọc loại hình nổi bật --}}
    <div class="hl-type-tabs">
        @foreach([
            [null,              '🏠 Tất cả',             null],
            ['hotel',           '🏨 Khách sạn',          'hotel'],
            ['homestay-resort', '🏨 Khách sạn & Resort',   'homestay-resort'],
        ] as [$val, $lbl, $check])
        <a href="?{{ http_build_query(array_merge(request()->except('type'), $val ? ['type' => $val] : [])) }}"
           class="hl-type-tab {{ (request('type') === $check || (!request('type') && $check === null)) ? 'active' : '' }}">
            {{ $lbl }}
        </a>
        @endforeach
    </div>

    @if($hotels->isEmpty())
    <div class="hl-empty">
        <div class="hl-empty-icon">🏨</div>
        <h3>Không tìm thấy khách sạn</h3>
        <p>Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
        <a href="{{ route('hotels.index') }}" class="hl-btn-search" style="display:inline-flex;width:auto;padding:11px 28px;margin-top:8px;">Xem tất cả khách sạn</a>
    </div>
    @else
    <div class="hotels-grid">
        @foreach($hotels as $hotel)
        @include('components.hotel-card', ['hotel' => $hotel, 'favHotelIds' => $favHotelIds ?? []])
        @endforeach
    </div>
    <div class="hl-pagination">
        {{ $hotels->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const dropdown = document.getElementById('hsb-location-dropdown');
function showLocationDropdown() {
    document.querySelectorAll('.hsb-loc-item').forEach(el => el.style.display = 'flex');
    if (dropdown) dropdown.style.display = 'block';
}
function filterLocations(val) {
    const q = val.toLowerCase();
    document.getElementById('hsb-location-id').value = '';
    document.querySelectorAll('.hsb-loc-item').forEach(el => {
        el.style.display = (q === '' || el.dataset.name.includes(q)) ? 'flex' : 'none';
    });
    if (dropdown) dropdown.style.display = 'block';
}
function selectLocation(name, id) {
    document.getElementById('hsb-keyword-input').value = name;
    document.getElementById('hsb-location-id').value = id;
    if (dropdown) dropdown.style.display = 'none';
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
// Guests & Children popup
let _adults = {{ request('guests', $guests ?? 1) }}, _children = {{ request('children', 0) }}, _rooms = 1;
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
document.addEventListener('click', function(e) {
    if (dropdown && !e.target.closest('.hsb-keyword')) dropdown.style.display = 'none';
});
// Date picker: chỉ dùng showPicker trên desktop, mobile dùng native tap
if (!('ontouchstart' in window)) {
    document.querySelectorAll('.hsb-field input[type="date"]').forEach(function(input) {
        input.addEventListener('click', function() {
            try { this.showPicker(); } catch(e) {}
        });
    });
}
document.querySelectorAll('.hsb-loc-item').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = '#fdf2f8');
    el.addEventListener('mouseleave', () => el.style.background = '');
});

// Gần tôi — geolocation + Haversine distance
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
            if (dropdown) dropdown.style.display = 'none';
            status.textContent = '✓ Đã chọn: ' + locName + ' (~' + Math.round(minDist) + ' km)';
        }
    }, function(err) {
        if (spinner) spinner.style.display = 'none';
        if (err.code === 1) status.textContent = 'Hãy cho phép vị trí trong trình duyệt';
        else if (err.code === 2) status.textContent = 'Bật Location trong Settings → Privacy → Location';
        else status.textContent = 'Hết thời gian, hãy thử lại';
    }, { timeout: 12000, enableHighAccuracy: false });
}

// Restore UI nếu stay_type=oneday khi page load
@if(request('stay_type') === 'oneday')
(function(){
    const checkoutField = document.querySelector('.hsb-field.hsb-date:last-of-type');
    if (checkoutField) checkoutField.style.display = 'none';
})();
@endif

// Pre-select location nếu có trong query string
@if(request('location'))
const preLocItem = document.querySelector('.hsb-loc-item[data-id="{{ request('location') }}"]');
if (preLocItem) {
    document.getElementById('hsb-keyword-input').value = preLocItem.querySelector('.hsb-loc-name').textContent;
}
@endif
</script>
@endpush

