@extends('layouts.app')
@section('title', $hotel->name)
@section('meta_description', Str::limit($hotel->description, 155))
@section('og_title', $hotel->name . ' – StayGo')
@section('og_description', Str::limit($hotel->description, 200))
@section('og_image', asset('assets/images/' . ($hotel->image ?? 'StayGo.png')))

@php
    $minPrice = $hotel->rooms->min('price') ?? $hotel->price;
    $oldPrice = $hotel->old_price ?? 0;
    $discount = ($oldPrice > $minPrice) ? round((1 - $minPrice / $oldPrice) * 100) : 0;

    $ratingVal = $hotel->rating;
    [$ratingLbl, $ratingColor, $ratingClass] = match(true) {
        $ratingVal >= 9 => ['Tuyệt vời',   '#16a34a', 'rc-green'],
        $ratingVal >= 8 => ['Rất tốt',     '#0071c2', 'rc-blue'],
        $ratingVal >= 7 => ['Tốt',         '#2d9cdb', 'rc-sky'],
        default         => ['Bình thường', '#718096', 'rc-gray'],
    };

    // Build photos array
    $photos = [];
    $photos[] = ['src' => asset('assets/images/' . ($hotel->image ?? 'placeholder.jpg')), 'caption' => $hotel->name];
    foreach ($hotel->images as $img) {
        $photos[] = ['src' => asset('assets/images/' . $img->image), 'caption' => $img->caption ?? ''];
    }

    $roomConfig = [
        'Phòng Tiêu Chuẩn' => ['icon' => '🛏️', 'size' => '20m²'],
        'Phòng Cao Cấp'    => ['icon' => '⭐',  'size' => '25m²'],
        'Phòng Hạng Sang'  => ['icon' => '👑',  'size' => '35m²'],
    ];

    $activeReviews = $hotel->reviews->where('is_active', true);
@endphp

@section('content')

{{-- STICKY NAV --}}
<div class="hd-nav-bar" id="hdNavBar">
    <div class="hd-nav-container">
        <div class="hd-nav-tabs">
            <a href="#overview"  class="hd-nav-tab active">Tổng quan</a>
            <a href="#amenities" class="hd-nav-tab">Tiện nghi</a>
            <a href="#rooms"     class="hd-nav-tab">Phòng trống</a>
            <a href="#rules"     class="hd-nav-tab">Quy tắc</a>
            <a href="#reviews"   class="hd-nav-tab">Đánh giá</a>
        </div>
        <a href="#rooms" class="hd-nav-book">Đặt ngay →</a>
    </div>
</div>

<div class="hd-wrapper" id="overview">
<div class="hd-cont">

    {{-- BREADCRUMB + HEADER --}}
    <div class="hd-header">
        <div class="hd-title-wrap">
            <div class="hd-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a> /
                <a href="{{ route('hotels.index') }}">Khách sạn</a> /
                {{ $hotel->name }}
            </div>
            <div class="hd-badges-row">
                @if($hotel->is_weekend_deal)
                    <span class="hd-tag-deal">🔥 Deal cuối tuần</span>
                @endif
                @if($discount > 0)
                    <span class="hd-tag-discount">💰 Giảm {{ $discount }}%</span>
                @endif
            </div>
            <div class="hd-name-row">
                <h1 class="hd-name">{{ $hotel->name }}</h1>
                @if($hotel->stars > 0)
                <span class="hd-stars">{{ str_repeat('⭐', $hotel->stars) }}</span>
                @endif
            </div>
            @if($hotel->ranking_title)
            <div class="hd-ranking-badge">🏆 {{ $hotel->ranking_title }}</div>
            @endif
            <div class="hd-sub-row">
                <span class="hd-loc">📍 {{ $hotel->address }}</span>
                <div class="hd-score-wrap">
                    <span class="hd-score-box">{{ number_format($hotel->rating, 1) }}</span>
                    <span class="hd-score-lbl {{ $ratingClass }}">{{ $ratingLbl }}</span>
                    <span class="hd-review-n">{{ $hotel->review_count }} đánh giá</span>
                </div>
            </div>
        </div>
        <div class="hd-header-cta">
            <a href="#rooms" class="hd-cta-btn">Xem phòng & Đặt ngay</a>
        </div>
    </div>

    {{-- GALLERY --}}
    <div class="hd-gallery">
        <div class="hd-gallery-main" onclick="openGallery(0)">
            <img id="hd-main-img"
                src="{{ $photos[0]['src'] }}"
                alt="{{ $hotel->name }}"
                style="object-position: {{ $hotel->cover_position ?? 'center center' }}">
            @if($discount > 0)
                <span class="hd-gallery-disc">-{{ $discount }}%</span>
            @endif
            @if(count($photos) > 1)
                <div class="hd-gallery-count-badge">📷 {{ count($photos) }} ảnh</div>
            @endif
        </div>
        <div class="hd-gallery-grid">
            @for($i = 1; $i <= 4; $i++)
                @php $photo = $photos[$i] ?? $photos[0]; $isLast = $i === 4 && count($photos) > 5; @endphp
                <div class="hd-thumb {{ $i===1?'hd-thumb-active':'' }} {{ $isLast?'hd-thumb-more':'' }}"
                    data-src="{{ $photo['src'] }}" data-idx="{{ $i }}">
                    <img src="{{ $photo['src'] }}" alt="Ảnh {{ $i }}" loading="lazy">
                    @if($isLast)
                        <div class="hd-thumb-overlay">+{{ count($photos) - 4 }} ảnh</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    {{-- Photos data for JS --}}
    <div id="hd-photos-data" data-photos='@json($photos)' style="display:none"></div>

    {{-- LIGHTBOX --}}
    <div class="hd-lightbox" id="hdLightbox">
        <div class="hd-lb-backdrop" onclick="closeGallery()"></div>
        <div class="hd-lb-inner">
            <div class="hd-lb-header">
                <span class="hd-lb-title">{{ $hotel->name }}</span>
                <span class="hd-lb-counter" id="hdLbCounter"></span>
                <button class="hd-lb-close" onclick="closeGallery()">✕</button>
            </div>
            <div class="hd-lb-stage">
                <button class="hd-lb-prev" onclick="lbNav(-1)">&#8249;</button>
                <div class="hd-lb-img-wrap"><img id="hdLbImg" src="" alt=""></div>
                <button class="hd-lb-next" onclick="lbNav(1)">&#8250;</button>
            </div>
            <div class="hd-lb-caption" id="hdLbCaption"></div>
            <div class="hd-lb-strip" id="hdLbStrip">
                @foreach($photos as $k => $ph)
                <img src="{{ $ph['src'] }}" class="hd-lb-tn" data-index="{{ $k }}"
                    loading="lazy" alt="">
                @endforeach
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="hd-grid">

        {{-- CỘT TRÁI --}}
        <div class="hd-left">

            {{-- Giới thiệu --}}
            <div class="hd-card" id="about">
                <h2 class="hd-card-title">📖 Giới thiệu</h2>
                <div class="hd-intro-wrap">
                    <div class="hd-intro-preview">{!! nl2br(e(Str::limit($hotel->description, 280))) !!}</div>
                    @if(strlen($hotel->description) > 280)
                    <button class="hd-intro-toggle" onclick="document.getElementById('hdIntroModal').classList.add('open')">Xem thêm ›</button>
                    @endif
                </div>
            </div>

            {{-- Modal Giới thiệu --}}
            <div class="hd-intro-modal" id="hdIntroModal">
                <div class="hd-im-backdrop" onclick="document.getElementById('hdIntroModal').classList.remove('open')"></div>
                <div class="hd-im-box">
                    <div class="hd-im-header">
                        <span class="hd-im-title">Giới thiệu {{ $hotel->name }}</span>
                        <button class="hd-im-close" onclick="document.getElementById('hdIntroModal').classList.remove('open')">✕</button>
                    </div>
                    <div class="hd-im-body">{!! nl2br(e($hotel->description)) !!}</div>
                </div>
            </div>

            {{-- Tiện nghi --}}
            <div class="hd-card" id="amenities">
                <h2 class="hd-card-title">⭐ Tiện nghi nổi bật</h2>
                @php
                $amenityMap = [
                    'wifi'             => ['📶','WiFi miễn phí','Toàn bộ khách sạn'],
                    'parking'          => ['🚗','Đỗ xe miễn phí','Trong khuôn viên'],
                    'ac'               => ['❄️','Điều hòa','Tất cả các phòng'],
                    'breakfast'        => ['🍳','Bữa sáng','Phục vụ hàng ngày'],
                    'reception_24h'    => ['🛎️','Lễ tân 24/7','Hỗ trợ mọi lúc'],
                    'private_bathroom' => ['🚿','Phòng tắm riêng','Trong từng phòng'],
                    'tv'               => ['📺','TV màn hình phẳng','Tất cả các phòng'],
                    'cleaning'         => ['🧹','Dọn phòng','Hàng ngày'],
                    'no_smoking'       => ['🚭','Không hút thuốc','Toàn khu vực'],
                    'airport_shuttle'  => ['🚐','Đưa đón sân bay','Theo yêu cầu'],
                    'pool'             => ['🏊','Hồ bơi','Mở 6:00–22:00'],
                    'child_friendly'   => ['👶','Thân thiện trẻ em','Mọi lứa tuổi'],
                    'restaurant'       => ['🍽️','Nhà hàng','Mở cả ngày'],
                    'gym'              => ['💪','Phòng gym','24/7'],
                    'spa'              => ['💆','Spa & Massage','Theo đặt lịch'],
                    'bar'              => ['🍹','Quầy bar','Mở cả ngày'],
                ];
                $hotelAmenities = $hotel->amenities ?? [];
                $displayAmenities = !empty($hotelAmenities)
                    ? array_intersect_key($amenityMap, array_flip($hotelAmenities))
                    : $amenityMap;
                @endphp
                <div class="hd-amenities-grid">
                    @foreach($displayAmenities as [$icon,$name,$note])
                    <div class="hd-amenity-item">
                        <span class="hd-amen-icon">{{ $icon }}</span>
                        <div>
                            <div class="hd-amen-name">{{ $name }}</div>
                            <div class="hd-amen-note">{{ $note }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Phòng trống --}}
            <div class="hd-card" id="rooms">
                <h2 class="hd-card-title">🛏️ Danh sách phòng</h2>
                @if($checkin && $checkout)
                <div class="hd-avail-active">
                    📅 Đang xem: <strong>{{ \Carbon\Carbon::parse($checkin)->format('d/m/Y') }}</strong>
                    → <strong>{{ \Carbon\Carbon::parse($checkout)->format('d/m/Y') }}</strong>
                    ({{ \Carbon\Carbon::parse($checkin)->diffInDays($checkout) }} đêm · {{ $guests }} khách)
                    <a href="{{ request()->url() }}">✕ Xóa</a>
                </div>
                @endif

                @php
                    $isDay      = ($stayType ?? 'night') === 'day';
                    $roomGroups = $rooms->sortBy('price')->groupBy('room_name');
                    $amenityDefs = [
                        'sea_view'           => ['label'=>'View biển',              'icon'=>'🌊','cat'=>'Điểm nổi bật'],
                        'balcony'            => ['label'=>'Ban công',               'icon'=>'🌅','cat'=>'Điểm nổi bật'],
                        'pool_access'        => ['label'=>'Hồ bơi riêng',          'icon'=>'🏊','cat'=>'Điểm nổi bật'],
                        'waiting_area'       => ['label'=>'Khu vực chờ',           'icon'=>'🛋️','cat'=>'Tiện nghi cơ bản'],
                        'non_smoking'        => ['label'=>'Phòng không hút thuốc', 'icon'=>'🚭','cat'=>'Tiện nghi cơ bản'],
                        'breakfast'          => ['label'=>'Bữa sáng',              'icon'=>'🍳','cat'=>'Tiện nghi cơ bản'],
                        'ac'                 => ['label'=>'Máy lạnh',              'icon'=>'❄️','cat'=>'Tiện nghi phòng'],
                        'fan'                => ['label'=>'Quạt',                  'icon'=>'💨','cat'=>'Tiện nghi phòng'],
                        'wifi'               => ['label'=>'WiFi miễn phí',         'icon'=>'📶','cat'=>'Tiện nghi phòng'],
                        'tv'                 => ['label'=>'TV',                    'icon'=>'📺','cat'=>'Tiện nghi phòng'],
                        'minibar'            => ['label'=>'Quầy bar mini',         'icon'=>'🍹','cat'=>'Tiện nghi phòng'],
                        'fridge'             => ['label'=>'Tủ lạnh',              'icon'=>'🧊','cat'=>'Tiện nghi phòng'],
                        'safe'               => ['label'=>'Két an toàn',           'icon'=>'🔒','cat'=>'Tiện nghi phòng'],
                        'desk'               => ['label'=>'Bàn làm việc',          'icon'=>'💼','cat'=>'Tiện nghi phòng'],
                        'wardrobe'           => ['label'=>'Tủ quần áo',           'icon'=>'👗','cat'=>'Tiện nghi phòng'],
                        'kettle'             => ['label'=>'Ấm đun nước',           'icon'=>'☕','cat'=>'Tiện nghi phòng'],
                        'ironing'            => ['label'=>'Bàn ủi',               'icon'=>'👔','cat'=>'Tiện nghi phòng'],
                        'blackout_curtains'  => ['label'=>'Rèm che sáng',          'icon'=>'🪟','cat'=>'Tiện nghi phòng'],
                        'free_bottled_water' => ['label'=>'Nước đóng chai',        'icon'=>'💧','cat'=>'Tiện nghi phòng'],
                        'private_bathroom'   => ['label'=>'Phòng tắm riêng',       'icon'=>'🚽','cat'=>'Tiện nghi phòng tắm'],
                        'hot_water'          => ['label'=>'Nước nóng',             'icon'=>'🌡️','cat'=>'Tiện nghi phòng tắm'],
                        'shower'             => ['label'=>'Vòi tắm',              'icon'=>'🚿','cat'=>'Tiện nghi phòng tắm'],
                        'shower_standing'    => ['label'=>'Vòi tắm đứng',         'icon'=>'🚿','cat'=>'Tiện nghi phòng tắm'],
                        'bathtub'            => ['label'=>'Bồn tắm',              'icon'=>'🛁','cat'=>'Tiện nghi phòng tắm'],
                        'toiletries'         => ['label'=>'Bộ vệ sinh cá nhân',   'icon'=>'🧴','cat'=>'Tiện nghi phòng tắm'],
                        'hair_dryer'         => ['label'=>'Máy sấy tóc',          'icon'=>'💇','cat'=>'Tiện nghi phòng tắm'],
                        'bathrobe'           => ['label'=>'Áo choàng tắm',        'icon'=>'🩱','cat'=>'Tiện nghi phòng tắm'],
                        'slippers'           => ['label'=>'Dép đi trong phòng',   'icon'=>'🥿','cat'=>'Tiện nghi phòng tắm'],
                        'city_view'          => ['label'=>'View thành phố',         'icon'=>'🏙️','cat'=>'Thông tin phòng'],
                        'garden_view'        => ['label'=>'View vườn/cây xanh',     'icon'=>'🌿','cat'=>'Thông tin phòng'],
                        'pool_view'          => ['label'=>'View hồ bơi',            'icon'=>'🏊','cat'=>'Thông tin phòng'],
                        'mountain_view'      => ['label'=>'View núi',               'icon'=>'⛰️','cat'=>'Thông tin phòng'],
                        'high_floor'         => ['label'=>'Tầng cao',               'icon'=>'🏢','cat'=>'Thông tin phòng'],
                        'private_entrance'   => ['label'=>'Lối vào riêng',          'icon'=>'🚪','cat'=>'Thông tin phòng'],
                    ];
                @endphp
                @forelse($roomGroups as $groupName => $groupRooms)
                @php
                    $rc         = $roomConfig[$groupName] ?? ['icon' => '🛏️', 'size' => '20m²'];
                    $firstRoom  = $groupRooms->first();
                    $slideRoom  = $groupRooms->first(fn($r) => count($r->images_list) > 0) ?? $firstRoom;
                    $roomSlides = array_values(array_map(
                        fn($img) => asset('assets/images/' . $img),
                        $slideRoom->images_list
                    ));
                @endphp
                @php
                    $groupAmenities = (array)($firstRoom->room_amenities ?? []);
                    $highlightAmens = array_slice($groupAmenities, 0, 5);
                    $roomDetailData = json_encode([
                        'name'      => $groupName,
                        'area'      => $firstRoom->area ? (int)$firstRoom->area : null,
                        'guests'    => (int)$firstRoom->max_guests,
                        'children'  => (int)($firstRoom->max_children ?? 0),
                        'bed'       => $firstRoom->bed_type,
                        'images'    => $roomSlides,
                        'amenities' => $groupAmenities,
                        'notes'     => array_values(array_filter(array_map(
                            fn($n) => is_array($n) ? ($n['text'] ?? '') : (string)$n,
                            (array)($firstRoom->room_notes ?? [])
                        ))),
                        'price'     => (int)($groupRooms->min('price')),
                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
                @endphp
                <div class="hd-room-row">
                    {{-- Content: ảnh trái + bảng phải --}}
                    <div class="hd-room-content">
                        {{-- Cột trái: tên phòng + ảnh + thông tin nhanh --}}
                        <div class="hd-room-left">
                            {{-- Tên phòng ngay trên ảnh --}}
                            <div class="hd-room-name">{{ $groupName }}</div>
                            @if(count($roomSlides) > 0)
                            <div class="hd-room-carousel">
                                <div class="hd-rca-slides">
                                    @foreach($roomSlides as $k => $src)
                                    <div class="hd-rca-slide {{ $k === 0 ? 'active' : '' }}">
                                        <img src="{{ $src }}" alt="{{ $groupName }}" loading="{{ $k === 0 ? 'eager' : 'lazy' }}">
                                    </div>
                                    @endforeach
                                </div>
                                @if(count($roomSlides) > 1)
                                <button class="hd-rca-btn hd-rca-prev" type="button" aria-label="Ảnh trước">&#8249;</button>
                                <button class="hd-rca-btn hd-rca-next" type="button" aria-label="Ảnh tiếp">&#8250;</button>
                                <div class="hd-rca-dots">
                                    @foreach($roomSlides as $k => $src)
                                    <span class="hd-rca-dot {{ $k === 0 ? 'active' : '' }}"></span>
                                    @endforeach
                                </div>
                                @endif
                                @if($firstRoom->room_badge)
                                <div class="hd-rca-badge">{{ $firstRoom->room_badge }}</div>
                                @endif
                            </div>
                            @endif
                            {{-- Features nhanh bên dưới ảnh --}}
                            <div class="hd-room-quickinfo">
                                @if($firstRoom->area)
                                <div class="hd-rqi-area">📐 {{ (int)$firstRoom->area }} m²</div>
                                @endif
                                @if(!empty($highlightAmens))
                                <div class="hd-rqi-tags">
                                    @foreach($highlightAmens as $am)
                                    <span class="hd-rqi-tag">{{ $amenityDefs[$am]['icon'] ?? '✓' }} {{ $amenityDefs[$am]['label'] ?? $am }}</span>
                                    @endforeach
                                </div>
                                @endif
                                <a href="#" class="hd-detail-link"
                                   data-room='{{ $roomDetailData }}'>
                                    🔍 Xem chi tiết phòng
                                </a>
                            </div>
                        </div>

                        {{-- Cột phải: bảng lựa chọn phòng --}}
                        <div class="hd-room-body">
                            <div class="hd-pkg-wrap">
                                <table class="hd-pkg-table">
                                    <thead>
                                        <tr>
                                            <th>Lựa chọn phòng</th>
                                            <th class="hd-th-center">Khách</th>
                                            <th>Giá/đêm</th>
                                            <th class="hd-th-center">Phòng</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $groupTotal = $groupRooms->count(); @endphp
                                    @foreach($groupRooms->sortBy('price')->values() as $roomIdx => $room)
                                    @php
                                        $available = $room->available_count;
                                        $soldOut   = $available <= 0;
                                        $urgent    = !$soldOut && $available <= 2;
                                        $unitPrice = ($isDay && $room->day_price) ? $room->day_price : $room->price;
                                        $oldP      = round($unitPrice * 1.25);
                                        $unitLabel = $isDay ? 'ngày' : 'đêm';
                                        $baseBookUrl = route('booking.create', $room) . '?' . http_build_query(array_filter([
                                            'guests'    => $guests > 1 ? $guests : null,
                                            'stay_type' => $stayType ?? 'night',
                                        ]));
                                        $bookUrl   = route('booking.create', $room) . '?' . http_build_query(array_filter([
                                            'checkin'   => $checkin,
                                            'checkout'  => $checkout,
                                            'guests'    => $guests > 1 ? $guests : null,
                                            'stay_type' => $stayType ?? 'night',
                                        ]));
                                    @endphp
                                    <tr class="hd-pkg-row {{ $soldOut ? 'hd-pkg-soldout' : '' }}{{ $roomIdx >= 3 ? ' hd-pkg-extra' : '' }}"
                                        data-room-id="{{ $room->id }}"
                                        data-quantity="{{ $room->quantity ?? 1 }}">

                                        {{-- Lựa chọn phòng --}}
                                        <td class="hd-pkg-option">
                                            @if($room->package_name)
                                            <div class="hd-pkg-name">{{ $room->package_name }}</div>
                                            @endif
                                            @if($room->bed_type)
                                            <div class="hd-pkg-row-info">
                                                <svg class="hd-pkg-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>
                                                {{ $room->bed_type }}
                                            </div>
                                            @endif
                                            @if(!empty($room->benefits))
                                                @foreach($room->benefits as $benefit)
                                                <div class="hd-pkg-row-info hd-pkg-benefit">
                                                    <svg class="hd-pkg-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-2.18c.07-.33.18-.65.18-1a4 4 0 0 0-6.65-3l-.35.34-.35-.34A4 4 0 0 0 4 5c0 .35.11.67.18 1H2v2h.01L2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2l-.01-12H22V6zM12 4a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM7 4a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM4 20V8h16l.01 12H4z"/></svg>
                                                    {{ $benefit['text'] ?? $benefit }}
                                                </div>
                                                @endforeach
                                            @endif
                                            @if(!$room->is_refundable)
                                            <div class="hd-pkg-row-info hd-refund-no">
                                                <svg class="hd-pkg-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                                Không hoàn tiền
                                            </div>
                                            @elseif($room->cancellation_policy)
                                            <div class="hd-pkg-row-info hd-refund-policy">
                                                <svg class="hd-pkg-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                                Áp dụng chính sách hủy phòng
                                                <span class="hd-refund-tip" data-tip="{{ $room->cancellation_policy }}">i</span>
                                            </div>
                                            @else
                                            <div class="hd-pkg-row-info hd-refund-yes">
                                                <svg class="hd-pkg-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                Được hoàn tiền
                                            </div>
                                            @endif
                                        </td>

                                        {{-- Khách (SVG person icons) --}}
                                        <td class="hd-pkg-guests">
                                            @php
                                                $tipAdults   = $room->max_guests . ' người lớn';
                                                $tipChildren = ($room->max_children ?? 0) > 0 ? ', ' . $room->max_children . ' trẻ em' : '';
                                            @endphp
                                            <div class="hd-person-wrap" data-tooltip="{{ $tipAdults . $tipChildren }}">
                                                @php $showAdults = min($room->max_guests, 4); @endphp
                                                @for($pi = 0; $pi < $showAdults; $pi++)
                                                <span class="hd-itip" data-tip="Người lớn">
                                                    <svg class="hd-person-icon" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                    </svg>
                                                </span>
                                                @endfor
                                                @if($room->max_guests > 4)
                                                <span class="hd-person-more">+{{ $room->max_guests - 4 }}</span>
                                                @endif
                                                @if(($room->max_children ?? 0) > 0)
                                                    @php $showKids = min($room->max_children, 3); @endphp
                                                    @for($ci = 0; $ci < $showKids; $ci++)
                                                    <span class="hd-itip" data-tip="Trẻ em">
                                                        <svg class="hd-person-icon hd-child-icon" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 4c1.38 0 2.5 1.12 2.5 2.5S13.38 9 12 9s-2.5-1.12-2.5-2.5S10.62 4 12 4zm0 6c2.67 0 8 1.34 8 4v2H4v-2c0-2.66 5.33-4 8-4z"/>
                                                        </svg>
                                                    </span>
                                                    @endfor
                                                    @if($room->max_children > 3)
                                                    <span class="hd-person-more">+{{ $room->max_children - 3 }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Giá --}}
                                        <td class="hd-pkg-price">
                                            @if($room->is_sale)
                                            <span class="hd-sale-badge">Sale Lễ</span>
                                            @endif
                                            <div class="hd-pkg-old">{{ number_format($oldP) }}đ</div>
                                            <div class="hd-pkg-cur">{{ number_format($unitPrice) }}đ</div>
                                            <div class="hd-pkg-per">/{{ $unitLabel }}</div>
                                            <div class="hd-pkg-tax {{ $room->is_tax_included ? '' : 'hd-pkg-tax-excl' }}">
                                                {{ $room->is_tax_included ? 'Đã gồm thuế & phí' : 'Chưa bao gồm thuế & phí' }}
                                            </div>
                                        </td>

                                        {{-- Số phòng + urgency --}}
                                        <td class="hd-pkg-avail">
                                            <div class="hd-avail-badge"
                                                data-soldout="{{ $soldOut ? '1' : '0' }}"
                                                data-urgent="{{ $urgent ? '1' : '0' }}"
                                                data-available="{{ $available }}">
                                                @if($soldOut)
                                                    <span class="hd-avail-num hd-avail-sold">Hết</span>
                                                @else
                                                    <span class="hd-avail-num">x{{ $available }}</span>
                                                    @if($urgent)
                                                    <span class="hd-avail-urgent">Chỉ còn {{ $available }} phòng!</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Nút đặt --}}
                                        <td class="hd-pkg-action">
                                            <div class="hd-room-action" data-book-url="{{ $bookUrl }}" data-base-url="{{ $baseBookUrl }}">
                                                @if($soldOut)
                                                    <span class="hd-btn-soldout">Hết phòng</span>
                                                @else
                                                    <a href="{{ $bookUrl }}" class="hd-btn-reserve">Đặt ngay</a>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @if($groupTotal > 3)
                                <div class="hd-pkg-more-wrap">
                                    <button class="hd-pkg-showmore" type="button" data-open="0"
                                        data-more-label="Xem thêm {{ $groupTotal - 3 }} lựa chọn ▾"
                                        onclick="hdToggleMore(this)">
                                        Xem thêm {{ $groupTotal - 3 }} lựa chọn ▾
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>{{-- /.hd-room-content --}}
                </div>
                @empty
                <p class="hd-no-rooms">Chưa có thông tin phòng.</p>
                @endforelse

                {{-- Modal chi tiết phòng --}}
                <div id="hd-room-modal" class="hd-modal-overlay" onclick="if(event.target===this)hdCloseModal()">
                    <div class="hd-modal-box">
                        <button class="hd-modal-close" onclick="hdCloseModal()">✕</button>
                        <div class="hd-modal-inner">
                            {{-- Cột trái: gallery --}}
                            <div class="hd-modal-gallery">
                                <div class="hd-mg-main" id="hd-mg-main">
                                    <img id="hd-mg-img" src="" alt="">
                                    <button class="hd-mg-nav hd-mg-prev" onclick="hdGallery(-1)">&#8249;</button>
                                    <button class="hd-mg-nav hd-mg-next" onclick="hdGallery(1)">&#8250;</button>
                                    <div class="hd-mg-counter" id="hd-mg-counter"></div>
                                </div>
                                <div class="hd-mg-thumbs" id="hd-mg-thumbs"></div>
                            </div>
                            {{-- Cột phải: thông tin --}}
                            <div class="hd-modal-info">
                                <h3 class="hd-mi-name" id="hd-mi-name"></h3>
                                <div id="hd-mi-amenities"></div>
                                <div id="hd-mi-notes" class="hd-mi-notes"></div>
                                <div class="hd-mi-footer">
                                    <div class="hd-mi-price-lbl">Khởi điểm từ:</div>
                                    <div class="hd-mi-price" id="hd-mi-price"></div>
                                    <a href="#rooms" class="hd-mi-btn" onclick="hdCloseModal()">Thêm lựa chọn phòng</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quy tắc --}}
            <div class="hd-card" id="rules">
                <h2 class="hd-card-title">📋 Quy tắc chung</h2>
                <div class="hd-rules">
                    @foreach([
                        ['🕐','Nhận phòng',  'Từ ' . ($hotel->checkin_time  ?? '14:00')],
                        ['🕛','Trả phòng',   'Trước ' . ($hotel->checkout_time ?? '12:00')],
                        ['🚭','Hút thuốc',   'Không được phép'],
                        ['🐾','Vật nuôi',    'Theo yêu cầu'],
                        ['💳','Thanh toán',  'VNPay · MoMo · Chuyển khoản'],
                        ['👶','Trẻ em',      'Phù hợp mọi lứa tuổi'],
                    ] as [$icon,$label,$val])
                    <div class="hd-rule-row">
                        <span class="hd-rule-icon">{{ $icon }}</span>
                        <span class="hd-rule-label">{{ $label }}</span>
                        <span class="hd-rule-val">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
                @if($hotel->cancellation_policy)
                <div style="margin-top:14px;padding:12px 14px;background:#f0fdf4;border-left:3px solid #22c55e;border-radius:0 8px 8px 0;font-size:13px;color:#166534;">
                    <strong>✅ Chính sách hủy phòng:</strong> {{ $hotel->cancellation_policy }}
                </div>
                @else
                <div style="margin-top:14px;padding:12px 14px;background:#f0fdf4;border-left:3px solid #22c55e;border-radius:0 8px 8px 0;font-size:13px;color:#166534;">
                    ✅ <strong>Hủy miễn phí trước 24 giờ.</strong> Hủy trong 24h tính phí 1 đêm đầu tiên.
                </div>
                @endif
            </div>

            {{-- Đánh giá --}}
            <div class="hd-card" id="reviews">
                <h2 class="hd-card-title">💬 Đánh giá ({{ $activeReviews->count() }})</h2>
                @forelse($activeReviews->take(5) as $review)
                <div class="hd-review-item">
                    <div class="hd-review-top">
                        <div class="hd-review-avatar">{{ mb_strtoupper(mb_substr($review->user?->full_name ?? 'K', 0, 1)) }}</div>
                        <div>
                            <div class="hd-review-name">{{ $review->user?->full_name ?? 'Khách ẩn danh' }}</div>
                            <div class="hd-review-date">{{ $review->created_at?->format('d/m/Y') }}</div>
                        </div>
                        <div class="hd-review-stars">{{ str_repeat('⭐', $review->rating) }}</div>
                    </div>
                    <p class="hd-review-text">{{ $review->comment }}</p>
                </div>
                @empty
                <p class="hd-no-reviews">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                @endforelse
            </div>

        </div>{{-- /.hd-left --}}

        {{-- SIDEBAR --}}
        <aside class="hd-sidebar">

            {{-- Price card --}}
            <div class="hd-price-card">
                <div class="hd-pc-label">Giá chỉ từ</div>
                @if($oldPrice > $minPrice)
                    <div class="hd-pc-old">{{ number_format($oldPrice) }}đ</div>
                @endif
                <div class="hd-pc-price">{{ number_format($minPrice) }}đ<span>/đêm</span></div>
                @if($discount > 0)
                    <div class="hd-pc-save">🎉 Tiết kiệm {{ $discount }}%</div>
                @endif
                <a href="#rooms" class="hd-pc-btn" id="hdPcBtn">Chọn phòng & Đặt ngay</a>
                <div class="hd-pc-notes">
                    <div>✅ Hủy miễn phí trước 24h</div>
                    <div>✅ Không cần thẻ tín dụng</div>
                    <div>✅ Xác nhận tức thì</div>
                </div>
            </div>

            {{-- Score card --}}
            <div class="hd-score-card">
                <div class="hd-sc-top">
                    <span class="hd-sc-num">{{ number_format($hotel->rating, 1) }}</span>
                    <div>
                        <div class="hd-sc-lbl {{ $ratingClass }}">{{ $ratingLbl }}</div>
                        <div class="hd-sc-reviews">{{ $hotel->review_count }} đánh giá</div>
                    </div>
                </div>
                <div class="hd-sc-bars">
                    @foreach([
                        'Nhân viên' => min(10, round($ratingVal + 0.5, 1)),
                        'Vị trí'    => min(10, round($ratingVal + 0.3, 1)),
                        'Sạch sẽ'   => min(10, round($ratingVal - 0.2, 1)),
                        'Tiện nghi' => min(10, round($ratingVal - 0.4, 1)),
                        'Đáng giá'  => min(10, round($ratingVal - 0.1, 1)),
                    ] as $cat => $val)
                    <div class="hd-sc-row">
                        <span class="hd-sc-cat">{{ $cat }}</span>
                        <div class="hd-sc-bar"><div class="hd-sc-fill" data-w="{{ $val * 10 }}"></div></div>
                        <span class="hd-sc-val">{{ number_format($val, 1) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Location card --}}
            <div class="hd-loc-card">
                <div class="hd-lc-title">📍 Vị trí</div>
                <div class="hd-lc-addr">{{ $hotel->address }}</div>
                @if($hotel->location)
                <div class="hd-lc-region">🗺️ {{ $hotel->location->name }}</div>
                @endif
                <div class="hd-lc-times">
                    <span>🕐 Nhận phòng: {{ $hotel->checkin_time ?? '14:00' }}</span>
                    <span>🕛 Trả phòng: {{ $hotel->checkout_time ?? '12:00' }}</span>
                </div>
            </div>

            {{-- Highlights --}}
            <div class="hd-quick-card">
                <div class="hd-qc-title">⚡ Điểm nổi bật</div>
                <div class="hd-qc-list">
                    <div class="hd-qc-item">✅ Địa điểm được đánh giá cao</div>
                    <div class="hd-qc-item">🍳 Có phục vụ bữa sáng</div>
                    <div class="hd-qc-item">🚗 Đỗ xe riêng miễn phí</div>
                    <div class="hd-qc-item">📶 WiFi miễn phí toàn khu</div>
                </div>
            </div>

            {{-- Related hotels in sidebar --}}
            @if($relatedHotels->count())
            <div class="hd-related-card">
                <div class="hd-rc-title">🏨 Khách sạn tại {{ $hotel->location?->name }}</div>
                @foreach($relatedHotels as $related)
                <a href="{{ route('hotels.show', $related) }}" class="hd-rc-item">
                    <img src="{{ asset('assets/images/' . $related->image) }}" class="hd-rc-img" alt="{{ $related->name }}">
                    <div class="hd-rc-info">
                        <div class="hd-rc-name">{{ $related->name }}</div>
                        <div class="hd-rc-price">{{ number_format($related->rooms->min('price') ?? $related->price) }}đ<span>/đêm</span></div>
                        @if($related->rating)
                        <div class="hd-rc-rating">⭐ {{ number_format($related->rating, 1) }}</div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Bản đồ + Khu vực lân cận trong sidebar --}}
            @if($hotel->latitude && $hotel->longitude || $hotel->address)
            @php
                $mapsUrl = $hotel->latitude && $hotel->longitude
                    ? 'https://www.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude
                    : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($hotel->address ?? $hotel->name);
                $embedUrl = $hotel->latitude && $hotel->longitude
                    ? 'https://maps.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude . '&z=16&output=embed'
                    : 'https://maps.google.com/maps?q=' . urlencode($hotel->address ?? $hotel->name) . '&z=15&output=embed';
            @endphp
            <div class="hd-sidebar-map">
                <div class="hd-sm-title">📍 Vị trí trên bản đồ</div>
                <div class="hd-map-wrap">
                    <a class="hd-map-openlink" href="{{ $mapsUrl }}" target="_blank" rel="noopener">
                        🗺️ Mở Google Maps ↗
                    </a>
                    <iframe
                        src="{{ $embedUrl }}"
                        width="100%" height="200" style="border:0;"
                        allowfullscreen loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                @if($hotel->address)
                <div class="hd-sm-addr">📌 {{ $hotel->address }}</div>
                @endif

                @if(!empty($hotel->nearby_places))
                @php
                $typeIcons = [
                    'beach'     => '🏖️',
                    'food'      => '🍜',
                    'landmark'  => '🏛️',
                    'transport' => '🚌',
                    'shopping'  => '🛍️',
                    'hospital'  => '🏥',
                    'other'     => '📍',
                ];
                @endphp
                <div class="hd-sm-nearby">
                    <div class="hd-sm-nearby-title">Khu vực lân cận</div>
                    @foreach($hotel->nearby_places as $place)
                    <div class="hd-sm-nearby-item">
                        <span class="hd-sm-nearby-icon">{{ $typeIcons[$place['type'] ?? 'other'] ?? '📍' }}</span>
                        <span class="hd-sm-nearby-name">{{ $place['name'] }}</span>
                        <span class="hd-sm-nearby-dist">{{ $place['distance'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

        </aside>

    </div>{{-- /.hd-grid --}}
</div>
</div>

@push('scripts')
<script>
const hdPhotos = JSON.parse(document.getElementById('hd-photos-data').dataset.photos);
let lbIdx = 0;

const coverPosition = '{{ $hotel->cover_position ?? 'center center' }}';

function switchMain(el, src, idx) {
    const img = document.getElementById('hd-main-img');
    img.src = src;
    img.style.objectPosition = idx === 0 ? coverPosition : 'center center';
    document.querySelectorAll('.hd-thumb').forEach(t => t.classList.remove('hd-thumb-active'));
    el.classList.add('hd-thumb-active');
    lbIdx = idx;
}

// Thumb click: open lightbox directly (hd-thumb-more opens from index 0 to show all)
document.querySelectorAll('.hd-thumb').forEach(el => {
    el.addEventListener('click', function() {
        const idx = parseInt(this.dataset.idx);
        openGallery(this.classList.contains('hd-thumb-more') ? 0 : idx);
    });
});

// Lightbox thumbnail click delegation
document.querySelectorAll('.hd-lb-tn').forEach(el => {
    el.addEventListener('click', function() {
        openGallery(parseInt(this.dataset.index));
    });
});

// Init rating bar widths from data-w attribute
document.querySelectorAll('.hd-sc-fill[data-w]').forEach(el => {
    el.style.width = el.dataset.w + '%';
});

function openGallery(idx) {
    lbIdx = Math.max(0, Math.min(idx, hdPhotos.length - 1));
    updateLb();
    document.getElementById('hdLightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeGallery() {
    document.getElementById('hdLightbox').classList.remove('open');
    document.body.style.overflow = '';
}
function lbNav(dir) {
    lbIdx = (lbIdx + dir + hdPhotos.length) % hdPhotos.length;
    updateLb();
}
function updateLb() {
    const p = hdPhotos[lbIdx];
    const img = document.getElementById('hdLbImg');
    img.style.opacity = '0';
    setTimeout(() => { img.src = p.src; img.style.opacity = '1'; }, 100);
    document.getElementById('hdLbCaption').textContent = p.caption || '';
    document.getElementById('hdLbCounter').textContent = (lbIdx + 1) + ' / ' + hdPhotos.length;
    document.querySelectorAll('.hd-lb-tn').forEach((t, i) => t.classList.toggle('active', i === lbIdx));
    const tn = document.querySelectorAll('.hd-lb-tn')[lbIdx];
    if (tn) tn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}
document.addEventListener('keydown', e => {
    if (!document.getElementById('hdLightbox').classList.contains('open')) return;
    if (e.key === 'ArrowRight') lbNav(1);
    if (e.key === 'ArrowLeft')  lbNav(-1);
    if (e.key === 'Escape') {
        closeGallery();
        document.getElementById('hdIntroModal')?.classList.remove('open');
    }
});

// Room image carousels
document.querySelectorAll('.hd-room-carousel').forEach(function(car) {
    const slides = car.querySelectorAll('.hd-rca-slide');
    const dots   = car.querySelectorAll('.hd-rca-dot');
    const prev   = car.querySelector('.hd-rca-prev');
    const next   = car.querySelector('.hd-rca-next');
    if (slides.length <= 1) return;

    let cur = 0;
    function goTo(n) {
        slides[cur].classList.remove('active');
        if (dots[cur]) dots[cur].classList.remove('active');
        cur = (n + slides.length) % slides.length;
        slides[cur].classList.add('active');
        if (dots[cur]) dots[cur].classList.add('active');
    }

    if (prev) prev.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); goTo(cur - 1); });
    if (next) next.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); goTo(cur + 1); });
    dots.forEach(function(dot, i) {
        dot.addEventListener('click', function(e) { e.stopPropagation(); goTo(i); });
    });

    // Swipe trên mobile
    let tx = 0;
    car.addEventListener('touchstart', function(e) { tx = e.touches[0].clientX; }, { passive: true });
    car.addEventListener('touchend', function(e) {
        const diff = tx - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
    });
});

// Smooth scroll nav tabs
document.querySelectorAll('.hd-nav-tab, .hd-cta-btn, .hd-pc-btn, .hd-nav-book, a[href="#rooms"]').forEach(a => {
    a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const el = document.querySelector(href);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Active tab on scroll
const hdSections = document.querySelectorAll('#overview,#amenities,#rooms,#rules,#reviews');
const hdTabs = document.querySelectorAll('.hd-nav-tab');
window.addEventListener('scroll', () => {
    let cur = '';
    hdSections.forEach(s => { if (window.scrollY >= s.offsetTop - 90) cur = s.id; });
    hdTabs.forEach(t => t.classList.toggle('active', t.getAttribute('href') === '#' + cur));
});

// ── Availability: cập nhật ngay khi tab active lại + poll mỗi 60 giây ──
(function() {
    const availUrl = '{{ route('hotels.availability', $hotel) }}';
    const checkin  = '{{ $checkin ?? '' }}';
    const checkout = '{{ $checkout ?? '' }}';

    function renderBadge(badge, action, available) {
        const soldOut = available <= 0;
        const urgent  = !soldOut && available <= 2;
        badge.dataset.available = available;
        const bookUrl = action.dataset.bookUrl;
        if (soldOut) {
            badge.innerHTML = '<span class="hd-avail-num hd-avail-sold">Hết</span>';
        } else {
            badge.innerHTML = `<span class="hd-avail-num">x${available}</span>` +
                (urgent ? `<span class="hd-avail-urgent">Chỉ còn ${available} phòng!</span>` : '');
        }
        action.innerHTML = soldOut
            ? '<span class="hd-btn-soldout">Hết phòng</span>'
            : `<a href="${bookUrl}" class="hd-btn-reserve">Đặt ngay</a>`;
        const row = badge.closest('[data-room-id]');
        if (row) row.classList.toggle('hd-pkg-soldout', soldOut);
    }

    function pollAvailability() {
        const params = new URLSearchParams();
        if (checkin)  params.set('checkin', checkin);
        if (checkout) params.set('checkout', checkout);
        fetch(`${availUrl}?${params}`)
            .then(r => r.json())
            .then(data => {
                document.querySelectorAll('[data-room-id]').forEach(row => {
                    const roomId = parseInt(row.dataset.roomId);
                    const badge  = row.querySelector('.hd-avail-badge');
                    const action = row.querySelector('.hd-room-action[data-book-url]');
                    if (!badge || !action || data[roomId] === undefined) return;
                    const newAvail = parseInt(data[roomId]);
                    if (newAvail !== parseInt(badge.dataset.available)) {
                        renderBadge(badge, action, newAvail);
                    }
                });
            })
            .catch(() => {});
    }

    // Trigger ngay khi tab được focus lại (sau khi đặt phòng xong quay về)
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') pollAvailability();
    });

    // Backup poll mỗi 60 giây
    setInterval(pollAvailability, 60000);
})();

// ── Room detail modal ──────────────────────────────────────────────
(function() {
    const amenityDefs = @json($amenityDefs);

    let mgImages = [];
    let mgIdx    = 0;

    function fmtPrice(n) {
        return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    }

    function mgRender(idx) {
        mgIdx = (idx + mgImages.length) % mgImages.length;
        const img = document.getElementById('hd-mg-img');
        if (img) { img.style.opacity = 0; setTimeout(() => { img.src = mgImages[mgIdx]; img.style.opacity = 1; }, 80); }
        const counter = document.getElementById('hd-mg-counter');
        if (counter) counter.textContent = mgImages.length > 1 ? (mgIdx + 1) + ' / ' + mgImages.length : '';
        document.querySelectorAll('.hd-mg-thumb').forEach((t, i) => {
            t.classList.toggle('active', i === mgIdx);
            if (i === mgIdx) t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        });
        const navBtns = document.querySelectorAll('.hd-mg-nav');
        navBtns.forEach(b => b.style.display = mgImages.length > 1 ? 'flex' : 'none');
    }

    function buildThumbs(images) {
        const strip = document.getElementById('hd-mg-thumbs');
        if (!strip) return;
        strip.innerHTML = '';
        images.forEach((src, i) => {
            const t = document.createElement('img');
            t.src = src; t.className = 'hd-mg-thumb' + (i === 0 ? ' active' : '');
            t.addEventListener('click', () => mgRender(i));
            strip.appendChild(t);
        });
    }

    const HIGHLIGHT_KEYS = ['sea_view','balcony','pool_access','bathtub','shower_standing',
        'breakfast','fridge','minibar','waiting_area','wifi','ac','hot_water','bathrobe','safe'];

    const SVG_AREA   = '<svg class="hd-mia-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>';
    const SVG_PERSON = '<svg class="hd-mia-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
    const SVG_CHILD  = '<svg class="hd-mia-svg hd-mia-svg-child" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
    const SVG_BED    = '<svg class="hd-mia-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>';

    function buildAmenities(amenityKeys, data) {
        const el = document.getElementById('hd-mi-amenities');
        if (!el) return;
        el.innerHTML = '';

        // Group by category first (needed for section 1 + 2)
        const cats = {};
        (amenityKeys || []).forEach(key => {
            const def = amenityDefs[key];
            if (!def) return;
            if (!cats[def.cat]) cats[def.cat] = [];
            cats[def.cat].push({ key, icon: def.icon, label: def.label });
        });

        // ── 1. Thông tin phòng ─────────────────────────────────────
        const infoRows = [];
        if (data && data.area)     infoRows.push([SVG_AREA,   data.area + ' m²']);
        if (data && data.guests)   infoRows.push([SVG_PERSON, data.guests + (data.children ? ' người lớn' : ' khách')]);
        if (data && data.children) infoRows.push([SVG_CHILD,  data.children + ' trẻ em']);
        if (data && data.bed)      infoRows.push([SVG_BED,    data.bed]);
        const infoAmen = cats['Thông tin phòng'] || [];
        if (infoRows.length || infoAmen.length) {
            let html = '<div class="hd-mia-section"><div class="hd-mia-title">Thông tin phòng</div>';
            infoRows.forEach(([svg, text]) => {
                html += `<div class="hd-mia-info-row">${svg}<span>${escHtml(String(text))}</span></div>`;
            });
            infoAmen.forEach(({ icon, label }) => {
                html += `<div class="hd-mia-info-row"><span class="hd-mia-hi-icon" style="font-size:15px">${icon}</span><span>${escHtml(label)}</span></div>`;
            });
            el.insertAdjacentHTML('beforeend', html + '</div>');
        }

        if (!amenityKeys || !amenityKeys.length) return;

        // ── 2. Tính năng phòng bạn thích ──────────────────────────
        const seen = new Set();
        const highlights = [];
        (cats['Điểm nổi bật'] || []).forEach(i => { seen.add(i.key); highlights.push(i); });
        HIGHLIGHT_KEYS.forEach(key => {
            if (seen.has(key) || !amenityKeys.includes(key)) return;
            const def = amenityDefs[key];
            if (def) { seen.add(key); highlights.push({ key, icon: def.icon, label: def.label }); }
        });
        if (highlights.length) {
            let html = '<div class="hd-mia-section"><div class="hd-mia-title">Tính năng phòng bạn thích</div>';
            highlights.slice(0, 6).forEach(({ icon, label }) => {
                html += `<div class="hd-mia-highlight-item"><span class="hd-mia-hi-icon">${icon}</span><span>${escHtml(label)}</span></div>`;
            });
            el.insertAdjacentHTML('beforeend', html + '</div>');
        }

        // ── 3-5. Tiện nghi theo danh mục ──────────────────────────
        [
            { name: 'Tiện nghi cơ bản',   col2: false },
            { name: 'Tiện nghi phòng',     col2: true  },
            { name: 'Tiện nghi phòng tắm', col2: true  },
        ].forEach(({ name, col2 }) => {
            const items = cats[name];
            if (!items || !items.length) return;
            let html = `<div class="hd-mia-section"><div class="hd-mia-title">${name}</div><ul class="hd-mia-list${col2 ? ' hd-mia-list-2col' : ''}">`;
            items.forEach(({ label }) => { html += `<li>${escHtml(label)}</li>`; });
            el.insertAdjacentHTML('beforeend', html + '</ul></div>');
        });
    }

    const NOTES_PREVIEW = 4; // số dòng hiển thị khi thu gọn

    function buildNotes(notes) {
        const el = document.getElementById('hd-mi-notes');
        if (!el) return;
        if (!notes || !notes.length) { el.style.display = 'none'; return; }
        el.style.display = '';

        const showAll = notes.length <= NOTES_PREVIEW;
        let html = '<div class="hd-mn-title">Về phòng này</div><ul class="hd-mn-list">';
        notes.forEach((text, i) => {
            html += `<li class="hd-mn-item${i >= NOTES_PREVIEW && !showAll ? ' hd-mn-extra' : ''}">${escHtml(text)}</li>`;
        });
        html += '</ul>';
        if (!showAll) {
            html += '<button class="hd-mn-toggle" data-open="0" onclick="hdNotesToggle(this)">Xem thêm ›</button>';
        }
        el.innerHTML = html;
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    window.hdToggleMore = function(btn) {
        const wrap = btn.closest('.hd-pkg-wrap');
        const open = btn.dataset.open === '1';
        wrap.querySelectorAll('.hd-pkg-extra').forEach(tr => tr.style.display = open ? 'none' : 'table-row');
        btn.dataset.open = open ? '0' : '1';
        btn.textContent  = open ? btn.dataset.moreLabel : 'Ẩn bớt ▴';
    };

    window.hdNotesToggle = function(btn) {
        const open = btn.dataset.open === '1';
        const list = btn.closest('.hd-mi-notes');
        list.querySelectorAll('.hd-mn-extra').forEach(li => li.style.display = open ? '' : 'none');
        btn.dataset.open = open ? '0' : '1';
        btn.textContent  = open ? 'Xem thêm ›' : 'Ẩn nội dung ‹';
    };

    function hdOpenModal(data) {
        mgImages = data.images || [];
        mgIdx = 0;

        // Gallery
        const gallery = document.querySelector('.hd-modal-gallery');
        const mainWrap = document.getElementById('hd-mg-main');
        const img = document.getElementById('hd-mg-img');
        if (mgImages.length === 0) {
            if (gallery) gallery.classList.add('hd-mg-empty');
            if (img) img.src = '';
            if (mainWrap) mainWrap.style.display = 'none';
        } else {
            if (gallery) gallery.classList.remove('hd-mg-empty');
            if (mainWrap) mainWrap.style.display = '';
            if (img) { img.style.opacity = 1; img.src = mgImages[0]; }
        }
        buildThumbs(mgImages);
        const counter = document.getElementById('hd-mg-counter');
        if (counter) counter.textContent = mgImages.length > 1 ? '1 / ' + mgImages.length : '';
        document.querySelectorAll('.hd-mg-nav').forEach(b => b.style.display = mgImages.length > 1 ? 'flex' : 'none');

        // Name
        const nameEl = document.getElementById('hd-mi-name');
        if (nameEl) nameEl.textContent = data.name || '';

        // Amenities + room info (tất cả build trong buildAmenities)
        buildAmenities(data.amenities || [], data);

        // Về phòng này
        buildNotes(data.notes || []);

        // Price
        const priceEl = document.getElementById('hd-mi-price');
        if (priceEl) priceEl.innerHTML = fmtPrice(data.price) + '<span>/đêm</span>';

        // Open
        document.getElementById('hd-room-modal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    window.hdCloseModal = function() {
        document.getElementById('hd-room-modal').classList.remove('open');
        document.body.style.overflow = '';
    };

    window.hdGallery = function(dir) { mgRender(mgIdx + dir); };

    // Attach click handlers to all detail links
    document.querySelectorAll('.hd-detail-link').forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            try {
                const data = JSON.parse(this.dataset.room);
                hdOpenModal(data);
            } catch(err) { console.warn('Room data parse error', err); }
        });
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') hdCloseModal();
    });
})();

// ── Đồng bộ link "Đặt ngay" với ngày từ server ────────────────────
(function() {
    const ci = '{{ $checkin ?? '' }}';
    const co = '{{ $checkout ?? '' }}';
    document.querySelectorAll('.hd-room-action[data-base-url]').forEach(action => {
        const url = new URL(action.dataset.baseUrl, window.location.origin);
        if (ci) url.searchParams.set('checkin', ci);
        if (co) url.searchParams.set('checkout', co);
        action.dataset.bookUrl = url.toString();
        const link = action.querySelector('.hd-btn-reserve');
        if (link) link.href = url.toString();
    });
})();
</script>
@endpush

@endsection
