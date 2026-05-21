@extends('layouts.app')
@section('title', 'Cẩm nang du lịch')

@section('content')

{{-- Hero --}}
<div class="blog-hero">
    <div class="blog-hero-overlay"></div>
    <img class="blog-hero-photo" src="{{ asset('assets/images/photo-1591373.jpg') }}" alt="Cẩm nang du lịch" loading="eager" fetchpriority="high">
    <div class="container blog-hero-inner">
        <h1 class="blog-hero-title">Cẩm Nang Du Lịch</h1>
        <p class="blog-hero-sub">Bí quyết, điểm đến và trải nghiệm nghỉ dưỡng tuyệt vời nhất Việt Nam</p>
    </div>
</div>

{{-- Blog Content --}}
<div class="blog-page-section">
    <div class="blog-page-container">

        {{-- Filter Bar --}}
        <div class="blg-filter-bar">
            <span class="blg-filter-label">LỌC:</span>
            <div class="blg-filter-pills">
                <a href="{{ route('blog.index') }}"
                   class="blg-pill {{ !request('category') ? 'blg-pill--active' : '' }}">Tất cả</a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat]) }}"
                   class="blg-pill {{ request('category') === $cat ? 'blg-pill--active' : '' }}">{{ $cat }}</a>
                @endforeach
            </div>
            <span class="blg-filter-count">{{ $posts->total() }} bài viết</span>
        </div>

        {{-- Posts --}}
        @if($posts->isEmpty())
        <div class="blog-empty">
            <span class="blog-empty-icon">✍</span>
            <p>Chưa có bài viết nào.</p>
        </div>
        @else

        {{-- Featured post — only on page 1 --}}
        @if($posts->currentPage() === 1)
        @php $featured = $posts->first(); @endphp
        <article class="blg-featured">
            {{-- Image --}}
            <a href="{{ route('blog.show', $featured) }}" class="blg-featured__img-wrap">
                @if($featured->thumb)
                <img src="{{ str_starts_with($featured->thumb, 'http') ? $featured->thumb : asset('storage/' . $featured->thumb) }}"
                     alt="{{ $featured->title }}" loading="eager">
                @else
                <div class="blg-featured__img-placeholder">📰</div>
                @endif
            </a>
            {{-- Content --}}
            <div class="blg-featured__body">
                <div class="blg-featured__badges">
                    @if($featured->category)
                    <span class="blg-featured__cat">{{ strtoupper($featured->category) }}</span>
                    @endif
                    <span class="blg-featured__hot">Bài nổi bật</span>
                </div>
                <div class="blg-featured__meta">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $featured->created_at?->format('d/m/Y') }}
                    @if($featured->read_time)
                    <span class="blg-meta-sep">•</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $featured->read_time }} phút đọc
                    @endif
                </div>
                <a href="{{ route('blog.show', $featured) }}" class="blg-featured__title">
                    {{ $featured->title }}
                </a>
                @if($featured->summary)
                <p class="blg-featured__excerpt">{{ Str::limit($featured->summary, 160) }}</p>
                @endif
                <a href="{{ route('blog.show', $featured) }}" class="blg-featured__cta">
                    Đọc bài viết đầy đủ →
                </a>
            </div>
        </article>
        @endif

        {{-- Posts Grid (skip featured on page 1) --}}
        <div class="blog-grid-3">
            @foreach($posts as $post)
            @if($loop->first && $posts->currentPage() === 1)
                @continue
            @endif
            <article class="blog-card">

                {{-- Thumbnail --}}
                @if($post->thumb)
                <div class="blog-card-img">
                    <img src="{{ str_starts_with($post->thumb, 'http') ? $post->thumb : asset('storage/' . $post->thumb) }}"
                         alt="{{ $post->title }}" loading="lazy"
                         onerror="this.parentElement.style.display='none'">
                </div>
                @else
                <div class="blog-card-placeholder">📰</div>
                @endif

                {{-- Body --}}
                <div class="blog-card-body">

                    @if($post->category)
                    <span class="blog-card-cat">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                        {{ $post->category }}
                    </span>
                    @endif

                    <a href="{{ route('blog.show', $post) }}" class="blog-card-title">
                        {{ $post->title }}
                    </a>

                    @if($post->summary)
                    <p class="blog-card-excerpt">{{ $post->summary }}</p>
                    @endif

                    <div class="blog-card-footer">
                        <span>{{ $post->created_at?->format('d/m/Y') }}</span>
                        <a href="{{ route('blog.show', $post) }}" class="blog-read-link">
                            Đọc tiếp
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                </div>
            </article>
            @endforeach
        </div>

        <div class="blog-pagination">
            {{ $posts->links() }}
        </div>
        @endif

    </div>
</div>

@endsection

@push('styles')
<style>
.blog-hero {
    position: relative;
    min-height: 380px;
    display: flex;
    align-items: center;
    overflow: hidden;
}
.blog-hero-photo {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center 55%;
    z-index: 0;
}
.blog-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to right, rgba(0,25,75,.90) 0%, rgba(0,55,160,.78) 35%, rgba(0,35,110,.45) 60%, rgba(0,15,50,.10) 100%);
    z-index: 1;
}
.blog-hero-inner {
    position: relative; z-index: 2;
    text-align: center;
    padding: 64px 0;
}
.blog-hero-title {
    font-size: 38px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    margin: 0 0 12px !important;
    letter-spacing: -.3px !important;
    text-shadow: 0 2px 12px rgba(0,0,0,.5) !important;
}
.blog-hero-sub {
    font-size: 16px !important;
    color: rgba(255,255,255,.92) !important;
    margin: 0 !important;
    text-shadow: 0 1px 6px rgba(0,0,0,.4) !important;
}
@media (max-width: 640px) {
    .blog-hero { min-height: 260px; }
    .blog-hero-inner { padding: 44px 20px; }
    .blog-hero-title { font-size: 26px !important; }
    .blog-hero-sub { font-size: 14px !important; }
}

/* ── Filter Bar ── */
.blg-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 0 24px;
    flex-wrap: wrap;
}
.blg-filter-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    color: #6b7280;
    text-transform: uppercase;
    white-space: nowrap;
}
.blg-filter-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    flex: 1;
}
.blg-pill {
    display: inline-block;
    padding: 7px 18px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    background: #ffffff;
    border: 1.5px solid #e5e7eb;
    text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
}
.blg-pill:hover { border-color: #0066cc; color: #0066cc; }
.blg-pill--active {
    background: #0d1b3e;
    color: #ffffff !important;
    border-color: #0d1b3e;
}
.blg-filter-count {
    font-size: 13px;
    color: #9ca3af;
    white-space: nowrap;
    margin-left: auto;
}

/* ── Featured Post Card ── */
.blg-featured {
    display: flex;
    background: #ffffff;
    border: 1.5px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
    min-height: 300px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: box-shadow .2s;
}
.blg-featured:hover { box-shadow: 0 6px 24px rgba(0,0,0,.10); }
.blg-featured__img-wrap {
    flex: 0 0 44%;
    max-width: 44%;
    overflow: hidden;
    display: block;
    background: #f3f4f6;
}
.blg-featured__img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform .35s;
}
.blg-featured:hover .blg-featured__img-wrap img { transform: scale(1.03); }
.blg-featured__img-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-size: 48px; background: #f3f4f6;
}
.blg-featured__body {
    flex: 1;
    padding: 36px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0;
}
.blg-featured__badges {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.blg-featured__cat {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    color: #ffffff;
    background: #0d1b3e;
    padding: 4px 12px;
    border-radius: 50px;
}
.blg-featured__hot {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    color: #ffffff;
    background: #0066cc;
    padding: 4px 12px;
    border-radius: 50px;
}
.blg-featured__meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 14px;
}
.blg-featured__meta svg { opacity: .6; flex-shrink: 0; }
.blg-meta-sep { color: #d1d5db; }
.blg-featured__title {
    font-size: 22px;
    font-weight: 700;
    color: #0d1b3e;
    line-height: 1.35;
    text-decoration: none;
    margin-bottom: 14px;
    display: block;
    transition: color .15s;
}
.blg-featured__title:hover { color: #0066cc; }
.blg-featured__excerpt {
    font-size: 14.5px;
    color: #4b5563;
    line-height: 1.65;
    margin: 0 0 20px;
}
.blg-featured__cta {
    display: inline-block;
    font-size: 14px;
    font-weight: 600;
    color: #0066cc;
    text-decoration: none;
    transition: color .15s;
}
.blg-featured__cta:hover { color: #0052a3; text-decoration: underline; }

@media (max-width: 768px) {
    .blg-featured { flex-direction: column; min-height: unset; }
    .blg-featured__img-wrap { flex: 0 0 220px; max-width: 100%; height: 220px; }
    .blg-featured__body { padding: 24px 20px; }
    .blg-featured__title { font-size: 18px; }
    .blg-filter-count { margin-left: 0; }
}
</style>
@endpush
