@php
$discount = ($hotel->old_price && $hotel->old_price > $hotel->price)
    ? round((1 - $hotel->price / $hotel->old_price) * 100)
    : null;

$ratingLabel = match(true) {
    $hotel->rating >= 9.0 => 'Trên cả tuyệt vời',
    $hotel->rating >= 8.0 => 'Tuyệt vời',
    $hotel->rating >= 7.0 => 'Rất tốt',
    $hotel->rating >= 6.0 => 'Tốt',
    default                => 'Dễ chịu',
};

$isFav = auth()->check() && isset($favHotelIds) && in_array($hotel->id, $favHotelIds);

$searchParams = array_filter([
    'checkin'   => request('checkin'),
    'checkout'  => request('checkout'),
    'guests'    => request('guests'),
    'children'  => request('children'),
    'stay_type' => request('stay_type'),
]);
$detailUrl = route('hotels.show', $hotel) . ($searchParams ? '?' . http_build_query($searchParams) : '');
@endphp

<div class="hotel-card">
    <a href="{{ $detailUrl }}" class="hotel-card-link">
        <div class="hotel-card-img">
            <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" loading="lazy">
            <div class="hotel-card-badges">
                @if($discount)
                <span class="badge-discount">-{{ $discount }}%</span>
                @endif
                @if($hotel->is_weekend_deal)
                <span class="badge-deal">🔥 Deal</span>
                @endif
            </div>
            @auth
            <button class="hc-fav-btn {{ $isFav ? 'is-fav' : '' }}"
                data-hotel="{{ $hotel->id }}"
                data-url="{{ route('favorite.toggle', $hotel) }}"
                onclick="toggleFav(this, event)"
                title="{{ $isFav ? 'Xóa yêu thích' : 'Thêm yêu thích' }}">
                <svg viewBox="0 0 24 24" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            @endauth
        </div>
        <div class="hotel-card-info">
            <div class="hotel-location">
                <svg width="11" height="14" viewBox="0 0 12 16" fill="#e91e8c"><path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 6.5A1.5 1.5 0 1 1 6 3.5 1.5 1.5 0 0 1 6 6.5z"/></svg>
                {{ $hotel->location?->name ?? 'Việt Nam' }}, Việt Nam
            </div>
            <div class="hotel-name">{{ $hotel->name }}</div>
            <div class="hotel-rating">
                <span class="hc-score">{{ number_format($hotel->rating, 1) }}</span>
                <span class="hc-label">{{ $ratingLabel }}</span>
                <span class="hc-reviews">{{ $hotel->review_count }} đánh giá</span>
            </div>
            @if(!empty($hotel->amenities))
            @php
            $amenityIcons = ['wifi'=>'📶','parking'=>'🚗','pool'=>'🏊','breakfast'=>'🍳','ac'=>'❄️','gym'=>'💪','spa'=>'💆','bar'=>'🍹','restaurant'=>'🍽️','airport_shuttle'=>'🚐'];
            $topAmenities = array_slice(array_filter($hotel->amenities, fn($k) => isset($amenityIcons[$k])), 0, 4);
            @endphp
            @if(!empty($topAmenities))
            <div class="hotel-amenity-pills">
                @foreach($topAmenities as $key)
                <span class="hap">{{ $amenityIcons[$key] }}</span>
                @endforeach
            </div>
            @endif
            @endif
            <div class="hotel-price">
                @if($hotel->old_price)
                <span class="price-old">{{ number_format($hotel->old_price) }}đ</span>
                @endif
                <span class="price-new">{{ number_format($hotel->price) }}đ<span class="price-unit">/đêm</span></span>
            </div>
        </div>
    </a>
    <div class="hotel-card-actions">
        <a href="{{ $detailUrl }}" class="hca-detail">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            Xem chi tiết
        </a>
        <a href="{{ $detailUrl }}#rooms" class="hca-book">Đặt ngay</a>
    </div>
</div>
