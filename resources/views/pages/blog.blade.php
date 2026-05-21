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

        {{-- Category Filter --}}
        @if($categories->count())
        <div class="blog-cat-bar">
            <a href="{{ route('blog.index') }}"
               class="blog-cat-btn {{ !request('category') ? 'blog-cat-btn--active' : '' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('blog.index', ['category' => $cat]) }}"
               class="blog-cat-btn {{ request('category') === $cat ? 'blog-cat-btn--active' : '' }}">
                {{ $cat }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Posts Grid --}}
        @if($posts->isEmpty())
        <div class="blog-empty">
            <span class="blog-empty-icon">✍</span>
            <p>Chưa có bài viết nào.</p>
        </div>
        @else
        <div class="blog-grid-3">
            @foreach($posts as $post)
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
    background: linear-gradient(100deg, rgba(0,30,80,.82) 0%, rgba(0,64,180,.65) 55%, rgba(0,20,60,.45) 100%);
    z-index: 1;
}
.blog-hero-inner {
    position: relative; z-index: 2;
    text-align: center;
    padding: 64px 0;
}
.blog-hero-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 12px;
    letter-spacing: -.3px;
    text-shadow: 0 2px 10px rgba(0,0,0,.35);
}
.blog-hero-sub {
    font-size: 16px;
    color: rgba(255,255,255,.88);
    margin: 0;
    text-shadow: 0 1px 4px rgba(0,0,0,.25);
}
@media (max-width: 640px) {
    .blog-hero { min-height: 260px; }
    .blog-hero-inner { padding: 44px 20px; }
    .blog-hero-title { font-size: 26px; }
    .blog-hero-sub { font-size: 14px; }
}
</style>
@endpush
