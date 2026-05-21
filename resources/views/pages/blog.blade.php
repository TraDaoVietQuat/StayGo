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

{{-- Filter floating card (giống trang Ưu đãi) --}}
<div class="dh-filter-wrap">
    <div class="container">
        <div class="dh-filter-card">
            <div class="dh-filter-label-col">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>Lọc theo<br>chủ đề</span>
            </div>
            <div class="dh-filter-divider"></div>
            <div class="dh-filter-tabs">
                @php
                $catIcons = ['Đà Lạt'=>'🌲','Nha Trang'=>'🌊','Vũng Tàu'=>'⛱️','Đà Nẵng'=>'🏖️'];
                @endphp
                <a href="{{ route('blog.index') }}"
                   class="dh-ftab blg-ftab {{ !request('category') ? 'active' : '' }}">
                    <span class="dh-ftab-icon">📖</span> Tất cả
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat]) }}"
                   class="dh-ftab blg-ftab {{ request('category') === $cat ? 'active' : '' }}">
                    <span class="dh-ftab-icon">{{ $catIcons[$cat] ?? '📍' }}</span>
                    {{ $cat }}
                </a>
                @endforeach
            </div>
            <div style="margin-left:auto;flex-shrink:0;">
                <span class="blg-total-count">{{ $posts->total() }} bài viết</span>
            </div>
        </div>
    </div>
</div>

{{-- Blog Content --}}
<div class="blog-page-section">
    <div class="blog-page-container">

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
            <a href="{{ route('blog.show', $featured) }}" class="blg-featured__img-wrap">
                @if($featured->thumb)
                <img src="{{ str_starts_with($featured->thumb, 'http') ? $featured->thumb : asset('storage/' . $featured->thumb) }}"
                     alt="{{ $featured->title }}" loading="eager" fetchpriority="high">
                @else
                <div class="blg-featured__img-placeholder">📰</div>
                @endif
            </a>
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
                @if($post->thumb)
                <div class="blog-card-img">
                    <img src="{{ str_starts_with($post->thumb, 'http') ? $post->thumb : asset('storage/' . $post->thumb) }}"
                         alt="{{ $post->title }}" loading="lazy"
                         onerror="this.parentElement.style.display='none'">
                </div>
                @else
                <div class="blog-card-placeholder">📰</div>
                @endif
                <div class="blog-card-body">
                    @if($post->category)
                    <span class="blog-card-cat">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                        {{ $post->category }}
                    </span>
                    @endif
                    <a href="{{ route('blog.show', $post) }}" class="blog-card-title">{{ $post->title }}</a>
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

{{-- Newsletter Card --}}
<div class="blg-nl-wrap">
    <div class="container">
        <div class="blg-nl-card">
            <div class="blg-nl-text">
                <p class="blg-nl-label">Cẩm Nang Du Lịch · StayGo</p>
                <h2 class="blg-nl-title">Nhận cẩm nang du lịch<br><em>mới nhất</em> mỗi tuần</h2>
                <p class="blg-nl-sub">Mẹo đặt phòng, địa điểm ẩn, ưu đãi độc quyền — gửi thẳng vào hộp thư của bạn.</p>
            </div>
            <form class="blg-nl-form" id="blgNlForm">
                @csrf
                <div class="blg-nl-row">
                    <input type="email" name="email" class="blg-nl-input" placeholder="email@example.com" required>
                    <button type="submit" class="blg-nl-btn">Đăng ký →</button>
                </div>
                <div class="blg-nl-msg" id="blgNlMsg"></div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Hero ── */
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

/* ── Filter floating card (reuse deals styles, override active color) ── */
.blg-ftab.active {
    background: linear-gradient(135deg, #0052a3, #0088e0) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 14px rgba(0,102,204,.35) !important;
}
.blg-ftab:hover:not(.active) {
    background: #e8f0fe !important;
    color: #0066cc !important;
    border-color: #93c5fd !important;
}
.blg-total-count {
    font-size: 13px;
    color: #9ca3af;
    white-space: nowrap;
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

/* Tablet landscape: thu nhỏ image width */
@media (max-width: 1024px) {
    .blg-featured__img-wrap { flex: 0 0 40%; max-width: 40%; }
    .blg-featured__body { padding: 28px 28px; }
    .blg-featured__title { font-size: 20px; }
}
/* Tablet portrait: stack dọc */
@media (max-width: 768px) {
    .blg-featured { flex-direction: column; min-height: unset; }
    .blg-featured__img-wrap { flex: 0 0 220px; max-width: 100%; height: 220px; }
    .blg-featured__body { padding: 24px 20px; }
    .blg-featured__title { font-size: 18px; }
    .blg-nl-card { padding: 32px 24px; gap: 24px; }
    .blg-nl-title { font-size: 22px !important; }
    .dh-filter-label-col { min-width: 100px; }
}
/* Mobile */
@media (max-width: 480px) {
    .blg-featured__body { padding: 20px 16px; }
    .blg-featured__title { font-size: 16px; }
    .blg-featured__excerpt { display: none; }
    .blg-filter-count { display: none; }
    .blg-nl-card { padding: 28px 18px; }
    .blg-nl-title { font-size: 20px !important; }
    .blg-nl-input { font-size: 13px !important; }
}

/* ── Newsletter Card ── */
.blg-nl-wrap {
    padding: 28px 0 48px;
}
.blg-nl-wrap .container {
    max-width: 75%;
}
.blg-nl-card {
    background: linear-gradient(135deg, #0d1b3e 0%, #0a3d7a 50%, #1565c0 100%);
    border-radius: 16px;
    padding: 32px 44px;
    display: flex;
    align-items: center;
    gap: 36px;
    overflow: hidden;
    position: relative;
}
.blg-nl-card::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.blg-nl-card::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 120px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.03);
    border-radius: 50%;
}
.blg-nl-text {
    flex: 1;
    min-width: 0;
    position: relative; z-index: 1;
}
.blg-nl-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: .12em !important;
    color: rgba(255,255,255,.5) !important;
    text-transform: uppercase !important;
    margin: 0 0 10px !important;
}
.blg-nl-title {
    font-size: 22px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    line-height: 1.3 !important;
    margin: 0 0 8px !important;
    text-shadow: none !important;
}
.blg-nl-title em {
    font-style: italic;
    color: #7ec8f8 !important;
    font-family: Georgia, serif;
}
.blg-nl-sub {
    font-size: 13px !important;
    color: rgba(255,255,255,.65) !important;
    line-height: 1.6 !important;
    margin: 0 !important;
}
.blg-nl-form {
    flex: 0 0 300px;
    position: relative; z-index: 1;
}
.blg-nl-row {
    display: flex;
    gap: 0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 14px rgba(0,0,0,.22);
}
.blg-nl-input {
    flex: 1;
    padding: 0 14px !important;
    height: 46px !important;
    border: none !important;
    outline: none !important;
    font-size: 13.5px !important;
    color: #1a202c !important;
    background: #fff !important;
    border-radius: 0 !important;
    width: auto !important;
    margin: 0 !important;
    box-shadow: none !important;
}
.blg-nl-input::placeholder { color: #94a3b8; }
.blg-nl-btn {
    flex-shrink: 0;
    padding: 0 20px;
    height: 46px;
    background: #1976d2;
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background .2s;
}
.blg-nl-btn:hover { background: #1565c0; }
.blg-nl-msg {
    margin-top: 8px;
    font-size: 12.5px;
    min-height: 16px;
    color: rgba(255,255,255,.85);
}
@media (max-width: 768px) {
    .blg-nl-wrap .container { max-width: 100%; }
    .blg-nl-card { flex-direction: column; padding: 28px 20px; gap: 20px; }
    .blg-nl-form { flex: unset; width: 100%; }
    .blg-nl-title { font-size: 19px !important; }
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('blgNlForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const btn  = form.querySelector('.blg-nl-btn');
    const msg  = document.getElementById('blgNlMsg');
    const email = form.querySelector('[name="email"]').value.trim();

    btn.disabled = true;
    btn.textContent = 'Đang gửi...';
    msg.textContent = '';

    try {
        const res = await fetch('{{ route("deals.newsletter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email }),
        });
        const data = await res.json();

        if (data.message === 'success') {
            msg.style.color = '#86efac';
            msg.textContent = '✓ Đăng ký thành công! Chúng tôi sẽ gửi cẩm nang mới đến bạn.';
            form.querySelector('[name="email"]').value = '';
        } else if (data.message === 'already_subscribed') {
            msg.style.color = '#fde68a';
            msg.textContent = '⚡ Email này đã đăng ký nhận cẩm nang rồi.';
        } else {
            msg.style.color = '#fca5a5';
            msg.textContent = 'Đã có lỗi xảy ra, vui lòng thử lại.';
        }
    } catch {
        msg.style.color = '#fca5a5';
        msg.textContent = 'Không kết nối được. Vui lòng thử lại.';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Đăng ký →';
    }
});
</script>
@endpush
