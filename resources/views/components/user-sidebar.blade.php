@php
    $user = auth()->user();
    $bookingCount = $user->bookings()->count();
    $pendingCount = $user->bookings()->where('status', 'pending')->count();
@endphp
<aside class="up-sidebar">
    <div class="up-avatar-wrap" style="background:#fff!important;color:#1a2b4a!important;">
        @if($user->avatar)
            @if(str_starts_with($user->avatar, 'http'))
                <img src="{{ $user->avatar }}" alt="Avatar" class="up-avatar up-avatar-img">
            @else
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="up-avatar up-avatar-img">
            @endif
        @else
            <div class="up-avatar">{{ mb_strtoupper(mb_substr($user->full_name ?? 'U', 0, 1)) }}</div>
        @endif
        <div class="up-user-name">{{ $user->full_name }}</div>
        <div class="up-user-email">{{ $user->email }}</div>
        <div class="up-joined">🗓 Tham gia {{ $user->created_at?->format('d/m/Y') }}</div>
        @if($pendingCount > 0)
        <div class="up-badge-wrap">
            <span class="up-pending-badge">{{ $pendingCount }} chờ xử lý</span>
        </div>
        @endif
    </div>

    <nav class="up-nav">
        <a href="{{ route('profile.show') }}" class="up-nav-item {{ request()->routeIs('profile.show') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Thông tin cá nhân
        </a>
        <a href="{{ route('booking.my') }}" class="up-nav-item {{ request()->routeIs('booking.my') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Lịch sử đặt phòng
            @if($bookingCount > 0)<span class="up-nav-count">{{ $bookingCount }}</span>@endif
        </a>
        <a href="{{ route('favorite.index') }}" class="up-nav-item {{ request()->routeIs('favorite.index') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Yêu thích
            @php $favCount = $user->favorites()->count(); @endphp
            @if($favCount > 0)<span class="up-nav-count">{{ $favCount }}</span>@endif
        </a>
        <a href="{{ route('profile.password') }}" class="up-nav-item {{ request()->routeIs('profile.password') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Đổi mật khẩu
        </a>
        <a href="{{ route('support.create') }}" class="up-nav-item {{ request()->routeIs('support.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Hỗ trợ
        </a>
        <a href="{{ route('hotels.index') }}" class="up-nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Tìm khách sạn
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="up-nav-item up-nav-logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Đăng xuất
            </button>
        </form>
    </nav>

    <div class="up-stats">
        <div class="up-stat-item">
            <div class="up-stat-num">{{ $user->bookings()->count() }}</div>
            <div class="up-stat-lbl">Đặt phòng</div>
        </div>
        <div class="up-stat-item">
            <div class="up-stat-num">{{ $user->bookings()->where('status','completed')->count() }}</div>
            <div class="up-stat-lbl">Hoàn thành</div>
        </div>
        <div class="up-stat-item">
            <div class="up-stat-num">{{ $user->reviews()->count() }}</div>
            <div class="up-stat-lbl">Đánh giá</div>
        </div>
    </div>
</aside>
