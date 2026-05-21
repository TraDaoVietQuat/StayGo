@extends('layouts.app')
@section('title', 'Cẩm nang du lịch')

@section('content')

{{-- Hero --}}
<div class="blog-hero">
    <div class="container">
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
    background: linear-gradient(135deg, #0066cc 0%, #1976d2 60%, #42a5f5 100%) !important;
    padding: 52px 0 44px !important;
    text-align: center !important;
    display: flex !important;
    align-items: center !important;
}
.blog-hero .container { width: 100%; }
.blog-hero-title {
    font-size: 34px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    margin: 0 0 10px !important;
    letter-spacing: -.3px !important;
    text-shadow: none !important;
}
.blog-hero-sub {
    font-size: 15px;
    color: rgba(255,255,255,.85) !important;
    margin: 0;
}
@media (max-width: 640px) {
    .blog-hero { padding: 36px 0 30px !important; }
    .blog-hero-title { font-size: 26px !important; }
}
</style>
@endpush
