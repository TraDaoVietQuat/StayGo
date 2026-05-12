@extends('layouts.app')
@section('title', $blogPost->title)

@section('content')

<div class="bld-wrap">

    {{-- ── ARTICLE ── --}}
    <article class="bld-article">

        {{-- Breadcrumb --}}
        <nav class="bld-breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}">Cẩm nang</a>
            <span>/</span>
            <span>{{ Str::limit($blogPost->title, 50) }}</span>
        </nav>

        {{-- Category badge --}}
        @if($blogPost->category)
        <span class="bld-cat-badge">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            {{ $blogPost->category }}
        </span>
        @endif

        {{-- Title --}}
        <h1 class="bld-title">{{ $blogPost->title }}</h1>

        {{-- Meta --}}
        <div class="bld-meta">
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ $blogPost->created_at?->format('d/m/Y') }}
            </span>
            @if($blogPost->author)
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $blogPost->author }}
            </span>
            @endif
            @if($blogPost->read_time)
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                {{ $blogPost->read_time }} phút đọc
            </span>
            @endif
        </div>

        {{-- Cover image --}}
        @if($blogPost->img)
        <div class="bld-cover-wrap">
            <img src="{{ str_starts_with($blogPost->img,'http') ? $blogPost->img : asset('storage/'.$blogPost->img) }}"
                alt="{{ $blogPost->title }}" loading="lazy" class="bld-cover-img"
                onerror="this.parentElement.style.display='none'">
        </div>
        @endif

        {{-- Content --}}
        <div class="bld-content">
            {!! $blogPost->content !!}
        </div>

        {{-- Tags --}}
        @if($blogPost->tags)
        @php $tags = is_array($blogPost->tags) ? $blogPost->tags : json_decode($blogPost->tags, true); @endphp
        @if($tags && count($tags))
        <div class="bld-tags">
            @foreach($tags as $tag)
            <span class="bld-tag"># {{ $tag }}</span>
            @endforeach
        </div>
        @endif
        @endif

    </article>

    {{-- ── KHÁCH SẠN GỢI Ý ── --}}
    @if(isset($suggestedHotels) && $suggestedHotels->count())
    <section class="bld-hotels-section">
        <div class="bld-hotels-header">
            <div class="bld-hotels-title-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                <h2 class="bld-hotels-title">Khách sạn nổi bật tại {{ $blogPost->category }}</h2>
            </div>
            <a href="{{ route('hotels.index', ['keyword' => $blogPost->category]) }}" class="bld-hotels-see-all">
                Xem tất cả →
            </a>
        </div>

        <div class="bld-hotels-grid">
            @foreach($suggestedHotels as $hotel)
            <a href="{{ route('hotels.show', $hotel->id) }}" class="bld-hcard">
                <div class="bld-hcard-img-wrap">
                    <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}"
                        class="bld-hcard-img" loading="lazy"
                        onerror="this.src='{{ asset('assets/images/hotel-placeholder.jpg') }}'">
                    @if($hotel->stars)
                    <div class="bld-hcard-stars">
                        @for($i = 0; $i < min((int)$hotel->stars, 5); $i++)
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                    </div>
                    @endif
                </div>
                <div class="bld-hcard-body">
                    <div class="bld-hcard-type">{{ $hotel->type === 'resort' ? 'Resort' : 'Khách sạn' }}</div>
                    <h3 class="bld-hcard-name">{{ $hotel->name }}</h3>
                    <div class="bld-hcard-bottom">
                        @if($hotel->rating)
                        <div class="bld-hcard-rating">
                            <span class="bld-hcard-score">{{ number_format($hotel->rating, 1) }}</span>
                            <span class="bld-hcard-reviews">
                                {{ $hotel->rating >= 9 ? 'Xuất sắc' : ($hotel->rating >= 8 ? 'Rất tốt' : 'Tốt') }}
                                @if($hotel->review_count) · {{ number_format($hotel->review_count) }} đánh giá @endif
                            </span>
                        </div>
                        @endif
                        @if($hotel->price)
                        <div class="bld-hcard-price">
                            <span class="bld-hcard-price-from">từ</span>
                            <span class="bld-hcard-price-amt">{{ number_format($hotel->price) }}đ</span>
                            <span class="bld-hcard-price-unit">/đêm</span>
                        </div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── BÀI VIẾT LIÊN QUAN ── --}}
    @if($related->count())
    <section class="bld-related">
        <h3 class="bld-related-title">Bài viết liên quan</h3>
        <div class="bld-related-grid">
            @foreach($related as $post)
            <a href="{{ route('blog.show', $post) }}" class="bld-rcard">
                @if($post->thumb)
                <img src="{{ str_starts_with($post->thumb,'http') ? $post->thumb : asset('storage/'.$post->thumb) }}"
                    alt="{{ $post->title }}" class="bld-rcard-img" loading="lazy">
                @else
                <div class="bld-rcard-img bld-rcard-no-img">📖</div>
                @endif
                <div class="bld-rcard-body">
                    @if($post->category)<span class="bld-rcard-cat">{{ $post->category }}</span>@endif
                    <h4 class="bld-rcard-title">{{ $post->title }}</h4>
                    <div class="bld-rcard-date">{{ $post->created_at?->format('d/m/Y') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Back --}}
    <div class="bld-back">
        <a href="{{ route('blog.index') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Quay lại Cẩm nang
        </a>
    </div>

</div>

@endsection
