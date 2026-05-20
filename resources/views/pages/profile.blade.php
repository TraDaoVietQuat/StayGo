@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@section('content')
<div class="up-wrap">
    <div class="up-layout">
        @include('components.user-sidebar')

        <div class="up-main">
            @if(session('success'))
            <div class="up-alert up-alert-success">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="up-alert up-alert-error">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            {{-- Ảnh đại diện --}}
            <div class="up-card up-collapsible collapsed" id="anh-dai-dien">
                <div class="up-card-header up-toggle-header" onclick="upToggle(this)">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/><path d="M6 20c0-3 2.7-5 6-5s6 2 6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Ảnh đại diện
                    </span>
                    <svg class="up-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="up-card-body">
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="up-form" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                        @csrf
                        <div style="flex-shrink:0;">
                            @if($user->avatar)
                                @if(str_starts_with($user->avatar, 'http'))
                                    <img src="{{ $user->avatar }}" alt="Avatar" class="up-avatar-img">
                                @else
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="up-avatar-img">
                                @endif
                            @else
                                <div class="up-avatar">{{ mb_strtoupper(mb_substr($user->full_name ?? $user->email, 0, 1)) }}</div>
                            @endif
                        </div>
                        <div style="flex:1;min-width:200px;">
                            <div class="up-field" style="margin-bottom:12px;">
                                <label>Chọn ảnh mới</label>
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <div style="font-size:12px;color:rgba(255,255,255,0.28);margin-top:4px;letter-spacing:0.04em;">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 2MB.</div>
                            </div>
                            <button type="submit" class="up-btn up-btn-primary" style="padding:8px 20px;font-size:13px;">Cập nhật ảnh</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Thông tin cá nhân — mở mặc định --}}
            <div class="up-card up-collapsible" id="thong-tin">
                <div class="up-card-header up-toggle-header" onclick="upToggle(this)">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Thông tin cá nhân
                    </span>
                    <svg class="up-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="up-card-body">
                    <form method="POST" action="{{ route('profile.update') }}" class="up-form">
                        @csrf @method('PUT')
                        <div class="up-grid-2">
                            <div class="up-field">
                                <label>Họ tên *</label>
                                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                            </div>
                            <div class="up-field">
                                <label>Số điện thoại</label>
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0909 xxx xxx">
                            </div>
                            <div class="up-field up-field-full">
                                <label>Email *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <button type="submit" class="up-btn up-btn-primary">Lưu thay đổi</button>
                    </form>
                </div>
            </div>

            {{-- Liên kết mạng xã hội --}}
            <div class="up-card up-collapsible collapsed" id="lien-ket">
                <div class="up-card-header up-toggle-header" onclick="upToggle(this)">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        Liên kết & Xác minh
                    </span>
                    <svg class="up-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="up-card-body">
                    <div style="padding:24px;display:flex;flex-direction:column;gap:12px;">

                        {{-- Google --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <img src="{{ asset('assets/images/google.png') }}" width="22" alt="Google" style="flex-shrink:0;opacity:0.85;">
                                <div>
                                    <div style="font-size:15px;font-weight:600;color:rgba(255,255,255,0.82);">Google</div>
                                    <div style="font-size:12px;color:rgba(255,255,255,0.3);letter-spacing:0.04em;">Đăng nhập nhanh bằng tài khoản Google</div>
                                </div>
                            </div>
                            @if($user->google_id)
                                <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(74,222,128,0.1);border:1px solid rgba(74,222,128,0.3);color:#4ade80;font-size:11px;font-weight:700;padding:4px 12px;letter-spacing:0.1em;text-transform:uppercase;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    Đã liên kết
                                </span>
                            @else
                                <a href="{{ route('auth.google') }}" class="btn-dark-gold btn-sm">
                                    Kết nối Google
                                </a>
                            @endif
                        </div>

                        {{-- Email verification --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <div>
                                    <div style="font-size:15px;font-weight:600;color:rgba(255,255,255,0.82);">Xác minh Email</div>
                                    <div style="font-size:12px;color:rgba(255,255,255,0.3);letter-spacing:0.04em;">{{ $user->email }}</div>
                                </div>
                            </div>
                            @if($user->email_verified_at)
                                <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(74,222,128,0.1);border:1px solid rgba(74,222,128,0.3);color:#4ade80;font-size:11px;font-weight:700;padding:4px 12px;letter-spacing:0.1em;text-transform:uppercase;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    Đã xác minh
                                </span>
                            @else
                                <form method="POST" action="{{ route('email.resend') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-ghost-gold btn-sm">Gửi xác minh</button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.up-collapsible .up-card-body { overflow: hidden; transition: max-height 0.28s ease, opacity 0.22s ease; max-height: 800px; opacity: 1; }
.up-collapsible.collapsed .up-card-body { max-height: 0; opacity: 0; pointer-events: none; }
.up-toggle-header { cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; }
.up-chevron { transition: transform 0.25s ease; flex-shrink: 0; }
.up-collapsible.collapsed .up-chevron { transform: rotate(-90deg); }
</style>

<script>
function upToggle(header) {
    var card = header.closest('.up-collapsible');
    card.classList.toggle('collapsed');
}

// Auto-open section targeted by URL hash
(function() {
    var hash = window.location.hash;
    if (hash) {
        var target = document.querySelector(hash + '.up-collapsible');
        if (target && target.classList.contains('collapsed')) {
            target.classList.remove('collapsed');
            setTimeout(function() { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
        }
    }
    // Also open if errors exist (so user sees the error)
    @if($errors->has('current_password'))
    var pwCard = document.getElementById('doi-mat-khau');
    if (pwCard) pwCard.classList.remove('collapsed');
    @endif
})();
</script>
@endsection
