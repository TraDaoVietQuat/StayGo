@extends('layouts.app')
@section('title', 'Blog')

@section('content')
<div class="container" style="padding:32px 16px;max-width:1100px;">
    <h2 style="font-family:'Playfair Display',serif;margin-bottom:8px;">Blog du lịch</h2>
    <p style="color:#666;font-size:14px;margin-bottom:24px;">Khám phá những điểm đến tuyệt vời cùng StayGo</p>

    {{-- Category filter --}}
    @if($categories->count())
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:28px;">
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

    @if($posts->isEmpty())
    <div style="text-align:center;padding:60px;color:#999;">
        <div style="font-size:48px;margin-bottom:12px;">📝</div>
        <p>Chưa có bài viết nào.</p>
    </div>
    @else
    <div class="blog-grid-3">
        @foreach($posts as $post)
        <article class="blog-card">
            @if($post->thumb)
            <img src="{{ str_starts_with($post->thumb, 'http') ? $post->thumb : asset('storage/' . $post->thumb) }}"
                alt="{{ $post->title }}" loading="lazy"
                style="width:100%;height:200px;object-fit:cover;"
                onerror="this.style.display='none'">
            @else
            <div style="width:100%;height:200px;background:linear-gradient(135deg,#1e73be20,#1e73be40);display:flex;align-items:center;justify-content:center;font-size:48px;">📰</div>
            @endif
            <div style="padding:18px;">
                @if($post->category)
                <span style="display:inline-flex;align-items:center;gap:4px;background:#fff0f6;color:#e91e8c;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;border:1px solid #ffd6eb;">
                    <svg width="9" height="12" viewBox="0 0 12 16" fill="#e91e8c"><path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 6.5A1.5 1.5 0 1 1 6 3.5 1.5 1.5 0 0 1 6 6.5z"/></svg>
                    {{ $post->category }}
                </span>
                @endif
                <h3 style="margin:10px 0 8px;font-size:16px;line-height:1.4;">
                    <a href="{{ route('blog.show', $post) }}" style="color:#1a1a1a;text-decoration:none;">{{ $post->title }}</a>
                </h3>
                @if($post->summary)
                <p style="font-size:13px;color:#666;line-height:1.6;margin:0 0 12px;display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">{{ $post->summary }}</p>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#aaa;">
                    <span>{{ $post->created_at?->format('d/m/Y') }}</span>
                    <a href="{{ route('blog.show', $post) }}" style="color:#1e73be;text-decoration:none;font-weight:600;">Đọc tiếp →</a>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    <div style="margin-top:32px;">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
