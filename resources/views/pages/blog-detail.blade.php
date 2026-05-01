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
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e73be" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                <h2 class="bld-hotels-title">Khách sạn nổi bật tại {{ $blogPost->category }}</h2>
            </div>
            <a href="{{ route('hotels.index', ['keyword' => $blogPost->category]) }}" class="bld-hotels-see-all">
                Xem tất cả →
            </a>
        </div>

        <div class="bld-hotels-grid">
            @foreach($suggestedHotels as $hotel)
            <a href="{{ route('hotels.show', $hotel->id) }}" class="bld-hcard">
                {{-- Image --}}
                <div class="bld-hcard-img-wrap">
                    <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}"
                        class="bld-hcard-img" loading="lazy"
                        onerror="this.src='{{ asset('assets/images/hotel-placeholder.jpg') }}'">
                    {{-- Stars --}}
                    @if($hotel->stars)
                    <div class="bld-hcard-stars">
                        @for($i = 0; $i < min((int)$hotel->stars, 5); $i++)
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                    </div>
                    @endif
                </div>
                {{-- Info --}}
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

@push('styles')
<style>
/* ══ BLOG DETAIL ═════════════════════════════════════════════════════════ */
.bld-wrap { max-width:860px; margin:0 auto; padding:32px 20px 64px; }

/* Article */
.bld-breadcrumb { font-size:12.5px; color:#94a3b8; margin-bottom:20px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.bld-breadcrumb a { color:#1e73be; text-decoration:none; }
.bld-breadcrumb a:hover { text-decoration:underline; }

.bld-cat-badge { display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#1e73be; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:.3px; }

.bld-title { font-family:'Playfair Display',serif; font-size:30px; font-weight:800; color:#1a202c; margin:14px 0 16px; line-height:1.35; }

.bld-meta { display:flex; align-items:center; gap:16px; flex-wrap:wrap; font-size:12.5px; color:#94a3b8; margin-bottom:28px; }
.bld-meta span { display:flex; align-items:center; gap:5px; }

.bld-cover-wrap { border-radius:16px; overflow:hidden; margin-bottom:32px; }
.bld-cover-img { width:100%; max-height:460px; object-fit:cover; display:block; }

.bld-content { background:#fff; border-radius:14px; padding:32px 36px; box-shadow:0 2px 12px rgba(0,0,0,.07); line-height:1.95; font-size:15px; color:#374151; border:1px solid #f0f4f8; overflow-x:hidden; }
.bld-content img { max-width:100% !important; height:auto !important; border-radius:8px; }
.bld-content table { max-width:100%; overflow-x:auto; display:block; }
.bld-content pre, .bld-content code { overflow-x:auto; white-space:pre-wrap; word-break:break-word; }
.bld-content iframe, .bld-content video { max-width:100% !important; }
.bld-content h2 { font-size:22px; font-weight:800; color:#1a202c; margin:28px 0 12px; }
.bld-content h3 { font-size:18px; font-weight:700; color:#1a202c; margin:22px 0 10px; }
.bld-content p { margin:0 0 14px; }
.bld-content ul, .bld-content ol { padding-left:24px; margin:0 0 14px; }
.bld-content blockquote { border-left:4px solid #1e73be; margin:20px 0; padding:12px 20px; background:#f0f7ff; border-radius:0 8px 8px 0; color:#374151; font-style:italic; }

.bld-tags { display:flex; flex-wrap:wrap; gap:8px; margin-top:24px; }
.bld-tag { font-size:12px; color:#64748b; background:#f1f5f9; padding:4px 11px; border-radius:20px; font-weight:500; }

/* ── Khách sạn gợi ý ── */
.bld-hotels-section { margin-top:52px; }
.bld-hotels-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
.bld-hotels-title-wrap { display:flex; align-items:center; gap:9px; }
.bld-hotels-title { font-size:19px; font-weight:800; color:#1a202c; margin:0; }
.bld-hotels-see-all { font-size:13px; font-weight:600; color:#1e73be; text-decoration:none; white-space:nowrap; }
.bld-hotels-see-all:hover { text-decoration:underline; }

.bld-hotels-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }

/* Hotel card */
.bld-hcard { display:flex; flex-direction:column; background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.08); border:1px solid #edf2f7; text-decoration:none; transition:all .25s; }
.bld-hcard:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,.14); }

.bld-hcard-img-wrap { position:relative; height:148px; overflow:hidden; flex-shrink:0; }
.bld-hcard-img { width:100%; height:100%; object-fit:cover; transition:transform .35s; }
.bld-hcard:hover .bld-hcard-img { transform:scale(1.06); }
.bld-hcard-stars { position:absolute; top:9px; left:9px; display:flex; gap:1px; background:rgba(0,0,0,.35); padding:3px 7px; border-radius:20px; backdrop-filter:blur(4px); }

.bld-hcard-body { padding:13px 14px 14px; display:flex; flex-direction:column; flex:1; }
.bld-hcard-type { font-size:10.5px; font-weight:700; color:#1e73be; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px; }
.bld-hcard-name { font-size:13px; font-weight:700; color:#1a202c; line-height:1.35; flex:1; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:10px; }

.bld-hcard-bottom { display:flex; flex-direction:column; gap:5px; margin-top:auto; }
.bld-hcard-rating { display:flex; align-items:center; gap:6px; }
.bld-hcard-score { background:#1e73be; color:#fff; font-size:11px; font-weight:800; padding:2px 7px; border-radius:6px; }
.bld-hcard-reviews { font-size:11px; color:#64748b; }
.bld-hcard-price { display:flex; align-items:baseline; gap:3px; }
.bld-hcard-price-from { font-size:10.5px; color:#94a3b8; }
.bld-hcard-price-amt { font-size:14px; font-weight:800; color:#e53e3e; }
.bld-hcard-price-unit { font-size:10.5px; color:#94a3b8; }

/* ── Related posts ── */
.bld-related { margin-top:48px; }
.bld-related-title { font-size:18px; font-weight:800; color:#1a202c; margin-bottom:16px; }
.bld-related-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }

.bld-rcard { display:flex; flex-direction:column; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); text-decoration:none; border:1px solid #edf2f7; transition:all .2s; }
.bld-rcard:hover { transform:translateY(-3px); box-shadow:0 6px 20px rgba(0,0,0,.12); }
.bld-rcard-img { width:100%; height:130px; object-fit:cover; }
.bld-rcard-no-img { height:130px; display:flex; align-items:center; justify-content:center; background:#f0f7ff; font-size:28px; }
.bld-rcard-body { padding:12px 14px; }
.bld-rcard-cat { font-size:10.5px; font-weight:700; color:#1e73be; text-transform:uppercase; letter-spacing:.5px; }
.bld-rcard-title { font-size:13px; font-weight:700; color:#1a202c; margin:5px 0 6px; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.bld-rcard-date { font-size:11.5px; color:#94a3b8; }

/* Back */
.bld-back { margin-top:36px; }
.bld-back a { display:inline-flex; align-items:center; gap:6px; font-size:13.5px; color:#1e73be; font-weight:600; text-decoration:none; }
.bld-back a:hover { text-decoration:underline; }

/* Responsive */
@media(max-width:900px) {
    .bld-hotels-grid { grid-template-columns:repeat(2,1fr); }
    .bld-related-grid { grid-template-columns:repeat(2,1fr); }
    .bld-title { font-size:24px; }
}
@media(max-width:580px) {
    .bld-hotels-grid { grid-template-columns:repeat(2,1fr); gap:12px; }
    .bld-related-grid { grid-template-columns:1fr; }
    .bld-content { padding:20px; }
    .bld-title { font-size:21px; }
}
@media(max-width:380px) {
    .bld-hotels-grid { grid-template-columns:1fr; }
}
</style>
@endpush

@endsection
