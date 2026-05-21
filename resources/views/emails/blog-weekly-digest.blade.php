<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Cẩm Nang Tuần Này — StayGo Journal</title>
<style>
*{box-sizing:border-box;}
body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:600px;margin:28px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.09);}
.hdr{background:linear-gradient(135deg,#003580,#0066cc,#1976d2);padding:36px;text-align:center;}
.hdr-logo{font-size:28px;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.5px;}
.hdr-sub{color:rgba(255,255,255,.8);font-size:12px;letter-spacing:.12em;text-transform:uppercase;margin:0 0 14px;}
.hdr-week{color:rgba(255,255,255,.9);font-size:15px;margin:0;}
.body{padding:28px 36px;}
.intro{font-size:15px;color:#4b5563;line-height:1.7;margin:0 0 28px;}
.section-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 16px;}
.post-card{display:flex;gap:16px;padding:16px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;}
.post-card:last-child{border-bottom:none;}
.post-thumb{width:90px;height:70px;object-fit:cover;border-radius:8px;flex-shrink:0;background:#dbeafe;}
.post-thumb-placeholder{width:90px;height:70px;border-radius:8px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;}
.post-info{flex:1;min-width:0;}
.post-cat{font-size:10px;font-weight:700;color:#0066cc;text-transform:uppercase;letter-spacing:.06em;margin:0 0 5px;}
.post-title{font-size:15px;font-weight:700;color:#0d1b3e;line-height:1.35;margin:0 0 6px;}
.post-meta{font-size:12px;color:#94a3b8;}
.cta-wrap{text-align:center;padding:24px 0;}
.cta{display:inline-block;background:linear-gradient(135deg,#0052a3,#0088e0);color:#fff!important;text-decoration:none;padding:13px 36px;border-radius:12px;font-size:14px;font-weight:700;}
.footer{background:#f8fafc;padding:24px 36px;text-align:center;font-size:12px;color:#94a3b8;line-height:1.8;}
.footer a{color:#0066cc;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

    <div class="hdr">
        <div class="hdr-logo">StayGo</div>
        <div class="hdr-sub">Cẩm Nang Du Lịch · StayGo Journal</div>
        <p class="hdr-week">📚 Tổng hợp bài viết tuần {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="body">
        <p class="intro">
            Chào bạn! Đây là những bài cẩm nang du lịch mới nhất từ StayGo trong tuần vừa qua.
            Khám phá ngay để có chuyến đi tuyệt vời nhất.
        </p>

        <div class="section-label">Bài viết tuần này</div>

        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post) }}" class="post-card" style="display:flex;">
            @if($post->thumb)
            <img class="post-thumb"
                 src="{{ str_starts_with($post->thumb, 'http') ? $post->thumb : asset('storage/' . $post->thumb) }}"
                 alt="{{ $post->title }}">
            @else
            <div class="post-thumb-placeholder">🏝️</div>
            @endif
            <div class="post-info">
                @if($post->category)
                <div class="post-cat">{{ $post->category }}</div>
                @endif
                <div class="post-title">{{ $post->title }}</div>
                <div class="post-meta">
                    {{ $post->created_at?->format('d/m/Y') }}
                    @if($post->read_time) · {{ $post->read_time }} phút đọc @endif
                </div>
            </div>
        </a>
        @endforeach

        <div class="cta-wrap">
            <a href="{{ url('/blog') }}" class="cta">Xem tất cả bài viết →</a>
        </div>
    </div>

    <div class="footer">
        <strong style="color:#374151;">StayGo</strong> — Nền tảng đặt phòng khách sạn & resort Việt Nam<br>
        <a href="{{ url('/blog') }}">Cẩm nang</a> ·
        <a href="{{ url('/uu-dai') }}">Ưu đãi</a> ·
        <a href="{{ url('/hotels') }}">Khách sạn</a><br><br>
        © {{ date('Y') }} StayGo. Mọi quyền được bảo lưu.
    </div>

</div>
</body>
</html>
