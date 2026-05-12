<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StayGo — Nơi Bình Yên Thượng Hạng</title>
<meta name="description" content="StayGo — Trải nghiệm lưu trú xa hoa tại các khách sạn và resort đỉnh cao Việt Nam">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<style>
:root {
    --gold: #C9A84C;
    --gold-light: #E8D5A0;
    --gold-dark: #8B6914;
    --obsidian: #0D0D0F;
    --charcoal: #1A1A1E;
    --slate: #2C2C32;
    --ash: #5C5C6A;
    --silver: #A8A8B8;
    --pearl: #F5F3EE;
    --white: #FFFFFF;
    --serif: 'Cormorant Garamond', Georgia, serif;
    --sans: 'DM Sans', -apple-system, sans-serif;
}

*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { font-family: var(--sans); background: var(--white); color: var(--obsidian); overflow-x: hidden; }

/* ── NAV ── */
.nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 60px; height: 80px;
    transition: background 0.4s, border-color 0.4s, backdrop-filter 0.4s;
    border-bottom: 1px solid transparent;
}
.nav.scrolled {
    background: rgba(13,13,15,0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom-color: rgba(201,168,76,0.25);
}
.nav-logo {
    font-family: var(--serif); font-size: 28px; font-weight: 400;
    color: var(--white); text-decoration: none; letter-spacing: 0.02em;
}
.nav-logo span { color: var(--gold); }
.nav-menu { display: flex; align-items: center; gap: 40px; list-style: none; }
.nav-menu a {
    font-size: 13px; font-weight: 400; color: rgba(255,255,255,0.85);
    text-decoration: none; letter-spacing: 0.12em; text-transform: uppercase;
    transition: color 0.25s;
}
.nav-menu a:hover { color: var(--gold); }
.nav-cta {
    display: inline-flex; align-items: center; padding: 10px 24px;
    border: 1px solid var(--gold); font-size: 13px; font-weight: 400;
    letter-spacing: 0.1em; text-transform: uppercase; color: var(--gold);
    text-decoration: none; transition: background 0.3s, color 0.3s;
}
.nav-cta:hover { background: var(--gold); color: var(--obsidian); }

/* ── HERO ── */
.hero { position: relative; height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.hero-bg {
    position: absolute; inset: 0;
    background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=80');
    background-size: cover; background-position: center;
    animation: slowZoom 20s ease-in-out infinite alternate;
}
@keyframes slowZoom { from { transform: scale(1); } to { transform: scale(1.06); } }
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(13,13,15,0.35) 0%, rgba(13,13,15,0.1) 45%, rgba(13,13,15,0.55) 100%);
}
.hero-content { position: relative; z-index: 2; text-align: center; max-width: 900px; padding: 0 40px; }

.eyebrow {
    display: block; font-size: 11px; font-weight: 400; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold); margin-bottom: 24px;
}
.hero-title {
    font-family: var(--serif); font-size: clamp(52px,8vw,96px); font-weight: 300;
    color: var(--white); line-height: 1.05; margin-bottom: 24px;
}
.hero-title em { font-style: italic; color: var(--gold); }
.hero-subtitle {
    font-size: 17px; font-weight: 300; color: rgba(255,255,255,0.65);
    max-width: 520px; margin: 0 auto; line-height: 1.7;
}

@keyframes fadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
.hero .eyebrow    { animation: fadeUp 0.8s ease forwards; animation-delay: 0.3s; opacity: 0; }
.hero-title       { animation: fadeUp 0.8s ease forwards; animation-delay: 0.5s; opacity: 0; }
.hero-subtitle    { animation: fadeUp 0.8s ease forwards; animation-delay: 0.7s; opacity: 0; }

.scroll-indicator {
    position: absolute; bottom: 140px; left: 50%; transform: translateX(-50%); z-index: 2;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    animation: fadeUp 0.8s ease forwards; animation-delay: 1s; opacity: 0;
}
.scroll-indicator span { font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.5); }
.scroll-line {
    width: 1px; height: 48px;
    background: linear-gradient(to bottom, rgba(201,168,76,0.8), transparent);
    animation: linepulse 2s ease infinite;
}
@keyframes linepulse { 0%,100% { opacity: 0.4; } 50% { opacity: 1; } }

/* Search Bar */
.search-bar {
    position: absolute; bottom: 0; left: 50%;
    transform: translate(-50%, 50%); z-index: 10;
    width: min(960px, calc(100% - 80px));
    background: var(--white);
    display: grid; grid-template-columns: 1fr 1fr 1fr auto;
    box-shadow: 0 24px 80px rgba(0,0,0,0.18);
}
.search-field { padding: 20px 28px; border-right: 1px solid #E8E6E0; }
.search-field:last-of-type { border-right: none; }
.search-field label {
    display: block; font-size: 10px; font-weight: 500;
    letter-spacing: 0.15em; text-transform: uppercase; color: var(--ash); margin-bottom: 6px;
}
.search-field input,
.search-field select {
    width: 100%; background: transparent; border: none; outline: none;
    font-family: var(--sans); font-size: 15px; color: var(--obsidian);
    appearance: none; cursor: pointer;
}
.search-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 20px 36px; background: var(--gold); border: none; cursor: pointer;
    font-family: var(--sans); font-size: 13px; font-weight: 500;
    letter-spacing: 0.1em; text-transform: uppercase; color: var(--obsidian);
    transition: background 0.3s, color 0.3s;
}
.search-btn:hover { background: var(--gold-dark); color: var(--white); }

/* ── ROOMS ── */
.rooms-section { padding: 160px 80px 120px; background: var(--pearl); }
.rooms-header {
    display: grid; grid-template-columns: 1fr 1fr;
    align-items: start; gap: 40px; margin-bottom: 60px;
}
.section-eyebrow {
    display: block; font-size: 10px; font-weight: 500;
    letter-spacing: 0.25em; text-transform: uppercase; color: var(--gold); margin-bottom: 16px;
}
.section-title {
    font-family: var(--serif); font-size: clamp(36px,4vw,56px); font-weight: 300;
    color: var(--obsidian); line-height: 1.15; margin-bottom: 20px;
}
.divider-gold { width: 48px; height: 1px; background: var(--gold); margin-top: 16px; }

.filter-tags { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding-top: 32px; }
.filter-tag {
    padding: 8px 20px; border: 1px solid #D0CEC8; font-family: var(--sans);
    font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--ash); cursor: pointer; background: transparent;
    transition: border-color 0.25s, color 0.25s;
}
.filter-tag:hover, .filter-tag.active { border-color: var(--gold); color: var(--gold); }

.rooms-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 24px; }
.room-card {
    position: relative; overflow: hidden; cursor: pointer;
    opacity: 0; transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.room-card.visible { opacity: 1; transform: translateY(0); }
.room-card:nth-child(2) { transition-delay: 0.1s; }
.room-card:nth-child(3) { transition-delay: 0.2s; }
.room-img { width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block; transition: transform 0.6s ease; }
.room-card:first-child .room-img { aspect-ratio: 4/5; }
.room-card:hover .room-img { transform: scale(1.05); }
.room-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(13,13,15,0.85) 0%, rgba(13,13,15,0.05) 55%, transparent 100%);
}
.room-badge {
    position: absolute; top: 24px; left: 24px; padding: 5px 14px;
    background: var(--gold); font-size: 10px; font-weight: 500;
    letter-spacing: 0.12em; text-transform: uppercase; color: var(--obsidian);
}
.room-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 28px; }
.room-name { font-family: var(--serif); font-size: 24px; font-weight: 300; color: var(--white); margin-bottom: 8px; }
.room-meta { display: flex; align-items: center; justify-content: space-between; }
.room-area { font-size: 12px; color: rgba(255,255,255,0.55); letter-spacing: 0.08em; }
.room-price { font-family: var(--serif); font-size: 20px; font-weight: 400; color: var(--gold-light); }

/* ── EXPERIENCE ── */
.experience-section { background: var(--obsidian); }
.experience-grid { display: grid; grid-template-columns: 1fr 1fr; }
.experience-img-col { position: relative; min-height: 600px; }
.experience-img-col img { width: 100%; height: 100%; object-fit: cover; display: block; }
.experience-img-col::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(201,168,76,0.08) 0%, transparent 60%);
}
.experience-content { padding: 100px 72px; }
.experience-content .section-title { color: var(--white); }
.experience-content .section-title em { font-style: italic; color: var(--gold-light); }
.experience-desc { font-size: 15px; line-height: 1.8; color: rgba(255,255,255,0.5); margin: 24px 0 48px; }
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
.stat-num {
    font-family: var(--serif); font-size: 48px; font-weight: 300; color: var(--gold);
    line-height: 1; margin-bottom: 8px;
    opacity: 0; transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.stat-num.visible { opacity: 1; transform: translateY(0); }
.stat-label { font-size: 11px; font-weight: 400; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(255,255,255,0.4); }

/* ── AMENITIES ── */
.amenities-section { background: var(--white); padding: 120px 80px; }
.section-header-center { text-align: center; max-width: 640px; margin: 0 auto 72px; }
.section-header-center .divider-gold { margin: 16px auto; }
.section-desc { font-size: 15px; line-height: 1.8; color: var(--ash); margin-top: 16px; }

.amenities-grid {
    display: grid; grid-template-columns: repeat(4,1fr); grid-template-rows: repeat(2,1fr);
    background: #E8E6E0; gap: 1px;
}
.amenity-item {
    background: var(--white); padding: 48px 40px;
    display: flex; flex-direction: column; align-items: flex-start; gap: 16px;
    opacity: 0; transform: translateY(20px);
    transition: background 0.3s, opacity 0.5s ease, transform 0.5s ease;
}
.amenity-item.visible { opacity: 1; transform: translateY(0); }
.amenity-item:hover { background: var(--pearl); }
.amenity-icon { width: 48px; height: 48px; color: var(--gold); }
.amenity-name { font-family: var(--serif); font-size: 18px; font-weight: 400; color: var(--obsidian); }
.amenity-desc { font-size: 13px; color: var(--ash); line-height: 1.65; }

/* ── TESTIMONIAL ── */
.testimonial-section {
    position: relative; background: var(--charcoal);
    padding: 120px 80px; text-align: center; overflow: hidden;
}
.testimonial-section::before {
    content: '\201C'; font-family: var(--serif); font-size: 280px;
    color: var(--gold); opacity: 0.06; position: absolute; top: -40px;
    left: 50%; transform: translateX(-50%); line-height: 1;
    pointer-events: none; user-select: none;
}
.testimonial-stars { display: flex; justify-content: center; gap: 4px; margin-bottom: 32px; }
.star { color: var(--gold); font-size: 18px; }
.testimonial-quote {
    font-family: var(--serif); font-size: clamp(20px,3vw,32px); font-style: italic;
    font-weight: 300; color: var(--white); max-width: 720px; margin: 0 auto 40px;
    line-height: 1.6; position: relative; z-index: 1;
}
.testimonial-author { font-size: 11px; font-weight: 500; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gold); }

/* ── GALLERY ── */
.gallery-section {
    display: grid; grid-template-columns: repeat(5,1fr); grid-template-rows: repeat(2,280px);
}
.gallery-item { position: relative; overflow: hidden; }
.gallery-item:first-child { grid-column: span 2; grid-row: span 2; }
.gallery-item:nth-child(4) { grid-column: span 2; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease; }
.gallery-item:hover img { transform: scale(1.07); }
.gallery-item::after { content: ''; position: absolute; inset: 0; background: rgba(13,13,15,0); transition: background 0.5s; }
.gallery-item:hover::after { background: rgba(13,13,15,0.2); }

/* ── OFFERS ── */
.offers-section { background: var(--pearl); padding: 120px 80px; }
.offer-inner { background: var(--obsidian); display: grid; grid-template-columns: 1fr 1fr; }
.offer-img { min-height: 440px; object-fit: cover; width: 100%; display: block; }
.offer-content { padding: 72px 64px; display: flex; flex-direction: column; justify-content: center; }
.offer-tag {
    display: inline-block; padding: 6px 16px; border: 1px solid var(--gold);
    font-size: 10px; font-weight: 500; letter-spacing: 0.18em; text-transform: uppercase;
    color: var(--gold); margin-bottom: 32px; width: fit-content;
}
.offer-content .section-title { color: var(--white); margin-bottom: 20px; }
.offer-content .section-title em { font-style: italic; color: var(--gold-light); }
.offer-desc { font-size: 15px; line-height: 1.8; color: rgba(255,255,255,0.5); margin-bottom: 36px; }
.offer-price { font-family: var(--serif); font-size: 42px; font-weight: 300; color: var(--gold); margin-bottom: 36px; }
.offer-price small { font-size: 16px; color: rgba(255,255,255,0.4); }
.btn-gold {
    display: inline-flex; align-items: center; gap: 10px; padding: 14px 36px;
    background: var(--gold); font-family: var(--sans); font-size: 13px; font-weight: 500;
    letter-spacing: 0.1em; text-transform: uppercase; color: var(--obsidian);
    text-decoration: none; border: none; cursor: pointer; width: fit-content;
    transition: background 0.3s, color 0.3s;
}
.btn-gold:hover { background: var(--gold-dark); color: var(--white); }

/* ── FOOTER ── */
.footer { background: var(--obsidian); padding: 80px 80px 0; }
.footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 60px; padding-bottom: 60px; }
.footer-brand .nav-logo { display: block; margin-bottom: 20px; }
.footer-address { font-size: 13px; line-height: 1.9; color: rgba(255,255,255,0.4); }
.footer-col h4 { font-size: 10px; font-weight: 500; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gold); margin-bottom: 24px; }
.footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 12px; }
.footer-col ul a { font-size: 13px; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.25s; }
.footer-col ul a:hover { color: var(--gold-light); }
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.08); padding: 24px 0;
    display: flex; align-items: center; justify-content: space-between;
}
.footer-copy { font-size: 12px; color: rgba(255,255,255,0.3); }
.social-icons { display: flex; gap: 12px; }
.social-icon {
    width: 36px; height: 36px; border: 1px solid rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.45); text-decoration: none;
    transition: border-color 0.25s, color 0.25s;
}
.social-icon:hover { border-color: var(--gold); color: var(--gold); }

/* ── FLOATING CTA ── */
.floating-cta {
    position: fixed; bottom: 36px; right: 36px; z-index: 99;
    display: flex; align-items: center; gap: 10px; padding: 14px 24px;
    background: var(--gold); font-family: var(--sans); font-size: 13px; font-weight: 500;
    letter-spacing: 0.08em; color: var(--obsidian); text-decoration: none; border: none; cursor: pointer;
    box-shadow: 0 8px 32px rgba(201,168,76,0.35);
    transition: transform 0.25s, box-shadow 0.25s;
}
.floating-cta:hover { transform: translateY(-2px); box-shadow: 0 16px 48px rgba(201,168,76,0.5); }
</style>
</head>
<body>

<!-- ① NAV -->
<nav class="nav" id="mainNav">
    <a href="#" class="nav-logo">Stay<span>Go</span></a>
    <ul class="nav-menu">
        <li><a href="#rooms">Phòng</a></li>
        <li><a href="#amenities">Tiện nghi</a></li>
        <li><a href="#offers">Ưu đãi</a></li>
        <li><a href="#gallery">Bộ sưu tập</a></li>
        <li><a href="#footer">Liên hệ</a></li>
    </ul>
    <a href="#rooms" class="nav-cta">Đặt ngay</a>
</nav>

<!-- ② HERO -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="eyebrow">Trải nghiệm xa hoa · Việt Nam</span>
        <h1 class="hero-title">Nơi <em>Bình Yên</em><br>Thượng Hạng</h1>
        <p class="hero-subtitle">Khám phá những không gian lưu trú được chắt lọc từ tinh hoa nghệ thuật và sự sang trọng vượt thời gian.</p>
    </div>
    <div class="scroll-indicator">
        <span>Cuộn xuống</span>
        <div class="scroll-line"></div>
    </div>
    <div class="search-bar">
        <div class="search-field">
            <label>Điểm đến</label>
            <select>
                <option value="">Chọn điểm đến</option>
                <option>Đà Nẵng</option>
                <option>Nha Trang</option>
                <option>Phú Quốc</option>
                <option>Đà Lạt</option>
            </select>
        </div>
        <div class="search-field">
            <label>Ngày nhận phòng</label>
            <input type="date" id="checkin">
        </div>
        <div class="search-field">
            <label>Khách &amp; Phòng</label>
            <select>
                <option>1 khách · 1 phòng</option>
                <option>2 khách · 1 phòng</option>
                <option>2 khách · 2 phòng</option>
                <option>4 khách · 2 phòng</option>
            </select>
        </div>
        <button class="search-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            Tìm kiếm
        </button>
    </div>
</section>

<!-- ③ ROOMS -->
<section class="rooms-section" id="rooms">
    <div class="rooms-header">
        <div>
            <span class="section-eyebrow">Không gian lưu trú</span>
            <h2 class="section-title">Phòng &amp;<br>Suite Cao Cấp</h2>
            <div class="divider-gold"></div>
        </div>
        <div class="filter-tags">
            <button class="filter-tag active">Tất cả</button>
            <button class="filter-tag">Deluxe</button>
            <button class="filter-tag">Suite</button>
            <button class="filter-tag">Villa</button>
            <button class="filter-tag">Penthouse</button>
        </div>
    </div>
    <div class="rooms-grid">
        <div class="room-card">
            <img class="room-img" src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80" alt="Presidential Suite">
            <div class="room-overlay"></div>
            <div class="room-badge">Presidential</div>
            <div class="room-info">
                <h3 class="room-name">Presidential Suite</h3>
                <div class="room-meta">
                    <span class="room-area">280 m² · Tầng 32</span>
                    <span class="room-price">8.500.000₫/đêm</span>
                </div>
            </div>
        </div>
        <div class="room-card">
            <img class="room-img" src="https://images.unsplash.com/photo-1560347876-aeef00ee58a1?w=800&q=80" alt="Deluxe Lake View">
            <div class="room-overlay"></div>
            <div class="room-badge">Deluxe</div>
            <div class="room-info">
                <h3 class="room-name">Deluxe Lake View</h3>
                <div class="room-meta">
                    <span class="room-area">68 m² · Hướng hồ</span>
                    <span class="room-price">3.200.000₫/đêm</span>
                </div>
            </div>
        </div>
        <div class="room-card">
            <img class="room-img" src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=80" alt="Grand Suite">
            <div class="room-overlay"></div>
            <div class="room-badge">Grand Suite</div>
            <div class="room-info">
                <h3 class="room-name">Grand Suite</h3>
                <div class="room-meta">
                    <span class="room-area">140 m² · Toàn cảnh</span>
                    <span class="room-price">6.000.000₫/đêm</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ④ EXPERIENCE -->
<section class="experience-section" id="experience">
    <div class="experience-grid">
        <div class="experience-img-col">
            <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=900&q=80" alt="StayGo Experience">
        </div>
        <div class="experience-content">
            <span class="section-eyebrow">Câu chuyện của chúng tôi</span>
            <h2 class="section-title">Nghệ thuật<br><em>Tiếp đón</em> Hoàn Hảo</h2>
            <div class="divider-gold"></div>
            <p class="experience-desc">Hơn một thập kỷ kiến tạo những kỳ nghỉ đáng nhớ, StayGo không đơn thuần là nơi lưu trú — đó là hành trình trở về với chính mình giữa không gian được chắt lọc từ tinh hoa văn hóa Việt.</p>
            <div class="stats-grid">
                <div class="stat-item"><div class="stat-num">100+</div><div class="stat-label">Khách sạn &amp; Resort</div></div>
                <div class="stat-item"><div class="stat-num">248</div><div class="stat-label">Phòng cao cấp</div></div>
                <div class="stat-item"><div class="stat-num">5★</div><div class="stat-label">Đánh giá trung bình</div></div>
                <div class="stat-item"><div class="stat-num">98%</div><div class="stat-label">Khách hàng hài lòng</div></div>
            </div>
        </div>
    </div>
</section>

<!-- ⑤ AMENITIES -->
<section class="amenities-section" id="amenities">
    <div class="section-header-center">
        <span class="section-eyebrow">Đẳng cấp dịch vụ</span>
        <h2 class="section-title">Tiện Nghi<br>Thượng Hạng</h2>
        <div class="divider-gold"></div>
        <p class="section-desc">Mỗi tiện ích được thiết kế tỉ mỉ để mang đến trải nghiệm hoàn hảo nhất cho từng vị khách.</p>
    </div>
    <div class="amenities-grid">
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M24 8c0 0-8 6-8 14a8 8 0 0016 0c0-8-8-14-8-14z"/>
                <path d="M16 32c-4 2-6 6-6 6h28s-2-4-6-6"/>
                <path d="M20 26c-2 0-4-2-4-4"/><path d="M28 26c2 0 4-2 4-4"/>
            </svg>
            <div class="amenity-name">Spa &amp; Wellness</div>
            <p class="amenity-desc">Liệu pháp phục hồi độc quyền kết hợp tinh hoa Đông y và kỹ thuật hiện đại.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M12 8v12a6 6 0 0012 0V8"/><path d="M18 20v20"/>
                <path d="M32 8v32"/><path d="M28 8c0 8 8 8 8 16"/>
            </svg>
            <div class="amenity-name">Fine Dining</div>
            <p class="amenity-desc">Nhà hàng đặc sản với thực đơn theo mùa từ nguyên liệu địa phương tuyển chọn.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <rect x="6" y="14" width="36" height="26" rx="1"/>
                <path d="M6 22h36"/><path d="M16 8v12"/><path d="M32 8v12"/>
                <path d="M14 30h4v4h-4z"/><path d="M22 30h4v4h-4z"/><path d="M30 30h4v4h-4z"/>
            </svg>
            <div class="amenity-name">Event Spaces</div>
            <p class="amenity-desc">Không gian sự kiện sang trọng sức chứa đến 500 khách với thiết bị hiện đại.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="24" cy="16" r="8"/>
                <path d="M8 40c0-8.837 7.163-16 16-16s16 7.163 16 16"/>
            </svg>
            <div class="amenity-name">Concierge 24/7</div>
            <p class="amenity-desc">Đội ngũ hỗ trợ cá nhân hóa sẵn sàng đáp ứng mọi nhu cầu bất kể thời điểm.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M6 24h4"/><path d="M38 24h4"/>
                <path d="M10 18v12"/><path d="M38 18v12"/>
                <path d="M14 24h20"/>
                <circle cx="14" cy="24" r="3"/><circle cx="34" cy="24" r="3"/>
            </svg>
            <div class="amenity-name">Fitness Club</div>
            <p class="amenity-desc">Phòng gym đẳng cấp quốc tế với thiết bị Technogym và huấn luyện viên riêng.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M4 40h40"/><path d="M8 40V20L24 8l16 12v20"/>
                <path d="M18 40v-12h12v12"/>
            </svg>
            <div class="amenity-name">Private Villas</div>
            <p class="amenity-desc">Biệt thự riêng với hồ bơi vô cực, vườn nhiệt đới và tầm nhìn toàn cảnh đại dương.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="24" cy="12" r="6"/>
                <path d="M14 40V28a10 10 0 0120 0v12"/>
                <path d="M10 34h4"/><path d="M34 34h4"/><path d="M18 40h12"/>
            </svg>
            <div class="amenity-name">Kids Club</div>
            <p class="amenity-desc">Không gian vui chơi an toàn với chương trình hoạt động sáng tạo cho trẻ 3–12 tuổi.</p>
        </div>
        <div class="amenity-item">
            <svg class="amenity-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M4 30h40v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4z"/>
                <path d="M8 30l4-10h24l4 10"/>
                <circle cx="14" cy="36" r="3"/><circle cx="34" cy="36" r="3"/>
            </svg>
            <div class="amenity-name">Limousine Service</div>
            <p class="amenity-desc">Đưa đón sân bay và tham quan thành phố bằng xe limousine hạng sang.</p>
        </div>
    </div>
</section>

<!-- ⑥ TESTIMONIAL -->
<section class="testimonial-section">
    <div class="testimonial-stars">
        <span class="star">★</span><span class="star">★</span><span class="star">★</span>
        <span class="star">★</span><span class="star">★</span>
    </div>
    <blockquote class="testimonial-quote">
        "Chưa bao giờ tôi cảm nhận được sự chăm sóc tận tâm đến từng chi tiết như tại StayGo. Đây không chỉ là kỳ nghỉ — đó là một tác phẩm nghệ thuật sống."
    </blockquote>
    <p class="testimonial-author">Nguyễn Minh Châu — Hà Nội, Việt Nam</p>
</section>

<!-- ⑦ GALLERY -->
<section class="gallery-section" id="gallery">
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80" alt="Gallery 1">
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&q=80" alt="Gallery 2">
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80" alt="Gallery 3">
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1455587734955-081b22074882?w=800&q=80" alt="Gallery 4">
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80" alt="Gallery 5">
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&q=80" alt="Gallery 6">
    </div>
</section>

<!-- ⑧ OFFERS -->
<section class="offers-section" id="offers">
    <div class="offer-inner">
        <img class="offer-img" src="https://images.unsplash.com/photo-1530521954074-e64f6810b32d?w=800&q=80" alt="Special Offer">
        <div class="offer-content">
            <div class="offer-tag">Ưu đãi đặc biệt · Hè 2025</div>
            <h2 class="section-title">Trọn Vẹn<br><em>Kỳ Nghỉ Hè</em></h2>
            <p class="offer-desc">Đặt phòng từ nay đến 31/07/2025, nhận ngay ưu đãi giảm 30% cùng gói bữa sáng miễn phí cho 2 người và dịch vụ đưa đón sân bay sang trọng.</p>
            <div class="offer-price">từ 2.400.000₫ <small>/ đêm</small></div>
            <a href="#" class="btn-gold">
                Đặt ngay
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- ⑨ FOOTER -->
<footer class="footer" id="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <a href="#" class="nav-logo">Stay<span>Go</span></a>
            <p class="footer-address">
                18 Đường Bờ Hồ, Phường Bạch Đằng<br>
                Quận Hoàn Kiếm, Hà Nội<br><br>
                +84 24 3826 8888<br>
                hello@staygo.vn
            </p>
        </div>
        <div class="footer-col">
            <h4>Khám phá</h4>
            <ul>
                <li><a href="#">Phòng &amp; Suite</a></li>
                <li><a href="#">Nhà hàng</a></li>
                <li><a href="#">Spa &amp; Wellness</a></li>
                <li><a href="#">Tiện nghi</a></li>
                <li><a href="#">Bộ sưu tập</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Ưu đãi</h4>
            <ul>
                <li><a href="#">Ưu đãi hè 2025</a></li>
                <li><a href="#">Gói cưới &amp; Trăng mật</a></li>
                <li><a href="#">Hội viên StayGo+</a></li>
                <li><a href="#">Đặt phòng sớm</a></li>
                <li><a href="#">Nghỉ dài ngày</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <ul>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Chính sách hủy</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Điều khoản dịch vụ</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p class="footer-copy">© 2025 StayGo. Bảo lưu mọi quyền.</p>
        <div class="social-icons">
            <a href="#" class="social-icon" aria-label="Facebook">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                </svg>
            </a>
            <a href="#" class="social-icon" aria-label="Instagram">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="2" width="20" height="20" rx="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                </svg>
            </a>
            <a href="#" class="social-icon" aria-label="Twitter">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                </svg>
            </a>
            <a href="#" class="social-icon" aria-label="YouTube">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/>
                    <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="currentColor" stroke="none"/>
                </svg>
            </a>
        </div>
    </div>
</footer>

<!-- ⑩ FLOATING CTA -->
<a href="#rooms" class="floating-cta">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    Đặt phòng ngay
</a>

<script>
// Sticky nav
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
});

// Filter tags
document.querySelectorAll('.filter-tag').forEach(tag => {
    tag.addEventListener('click', () => {
        document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
        tag.classList.add('active');
    });
});

// Default checkin = tomorrow
const checkin = document.getElementById('checkin');
if (checkin) {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    checkin.value = d.toISOString().split('T')[0];
}

// Scroll reveal
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.15 });

document.querySelectorAll('.room-card, .amenity-item, .stat-num').forEach(el => observer.observe(el));
</script>
</body>
</html>
