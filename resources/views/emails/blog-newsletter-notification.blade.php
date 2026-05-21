<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{{ $post->title }} — StayGo Cẩm Nang</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:28px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#003580,#0066cc,#1976d2);padding:36px;text-align:center;}
.hdr-logo{font-size:28px;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.5px;}
.hdr-sub{color:rgba(255,255,255,.8);font-size:12px;letter-spacing:.12em;text-transform:uppercase;margin:0 0 14px;}
.hdr-badge{display:inline-block;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);border-radius:24px;padding:6px 18px;color:#fff;font-size:13px;font-weight:700;}
.thumb{width:100%;height:240px;object-fit:cover;display:block;}
.thumb-placeholder{width:100%;height:180px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;font-size:48px;}
.body{padding:32px 36px;}
.cat-badge{display:inline-block;background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;border-radius:20px;padding:4px 12px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;}
.post-title{font-size:22px;font-weight:800;color:#0d1b3e;line-height:1.35;margin:0 0 14px;}
.meta{display:flex;gap:14px;font-size:13px;color:#94a3b8;margin-bottom:16px;}
.excerpt{font-size:15px;color:#4b5563;line-height:1.7;margin:0 0 28px;}
.cta-wrap{text-align:center;margin-bottom:28px;}
.cta{display:inline-block;background:linear-gradient(135deg,#0052a3,#0088e0);color:#fff!important;text-decoration:none;padding:14px 40px;border-radius:12px;font-size:15px;font-weight:700;letter-spacing:.3px;}
.divider{height:1px;background:#f1f5f9;margin:24px 0;}
.related-head{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 14px;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;font-size:12px;color:#94a3b8;line-height:1.8;}
.footer a{color:#0066cc;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

    {{-- Header --}}
    <div class="hdr">
        <div class="hdr-logo">StayGo</div>
        <div class="hdr-sub">Cẩm Nang Du Lịch · StayGo Journal</div>
        <span class="hdr-badge">📖 Bài viết mới</span>
    </div>

    {{-- Thumbnail --}}
    @if($post->thumb)
    <img class="thumb"
         src="{{ str_starts_with($post->thumb, 'http') ? $post->thumb : asset('storage/' . $post->thumb) }}"
         alt="{{ $post->title }}">
    @else
    <div class="thumb-placeholder">🏝️</div>
    @endif

    {{-- Body --}}
    <div class="body">
        @if($post->category)
        <span class="cat-badge">{{ $post->category }}</span>
        @endif

        <h1 class="post-title">{{ $post->title }}</h1>

        <div class="meta">
            <span>📅 {{ $post->created_at?->format('d/m/Y') }}</span>
            @if($post->read_time)
            <span>⏱ {{ $post->read_time }} phút đọc</span>
            @endif
            @if($post->author)
            <span>✍ {{ $post->author }}</span>
            @endif
        </div>

        @if($post->summary)
        <p class="excerpt">{{ $post->summary }}</p>
        @endif

        <div class="cta-wrap">
            <a href="{{ route('blog.show', $post) }}" class="cta">Đọc bài viết ngay →</a>
        </div>

        <div class="divider"></div>
        <p style="font-size:14px;color:#6b7280;text-align:center;margin:0;">
            Bạn nhận được email này vì đã đăng ký nhận cẩm nang du lịch từ StayGo.
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <strong style="color:#374151;">StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam<br>
        <a href="{{ url('/blog') }}">Xem tất cả bài viết</a> ·
        <a href="{{ url('/uu-dai') }}">Ưu đãi hôm nay</a><br><br>
        © {{ date('Y') }} StayGo. Mọi quyền được bảo lưu.
    </div>

</div>
</body>
</html>
