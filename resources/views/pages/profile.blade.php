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
            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#1e73be" stroke-width="2"/><circle cx="12" cy="10" r="3" stroke="#1e73be" stroke-width="2"/><path d="M6 20c0-3 2.7-5 6-5s6 2 6 5" stroke="#1e73be" stroke-width="2" stroke-linecap="round"/></svg>
                    Ảnh đại diện
                </div>
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="up-form" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                    @csrf
                    <div style="flex-shrink:0;">
                        @if($user->avatar)
                            @if(str_starts_with($user->avatar, 'http'))
                                <img src="{{ $user->avatar }}" alt="Avatar" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;">
                            @else
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;">
                            @endif
                        @else
                            <div style="width:80px;height:80px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:32px;color:#94a3b8;">
                                {{ strtoupper(substr($user->full_name ?? $user->email, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:200px;">
                        <div class="up-field" style="margin-bottom:12px;">
                            <label>Chọn ảnh mới</label>
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="font-size:13px;">
                            <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 2MB.</div>
                        </div>
                        <button type="submit" class="up-btn up-btn-primary" style="padding:8px 20px;font-size:13px;">Cập nhật ảnh</button>
                    </div>
                </form>
            </div>

            {{-- Thông tin cá nhân --}}
            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="#1e73be" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="#1e73be" stroke-width="2" stroke-linecap="round"/></svg>
                    Thông tin cá nhân
                </div>
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

            {{-- Liên kết mạng xã hội --}}
            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e73be" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Liên kết mạng xã hội
                </div>
                <div style="display:flex;flex-direction:column;gap:12px;">

                    {{-- Google --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <img src="{{ asset('assets/images/google.png') }}" width="24" alt="Google" style="flex-shrink:0;">
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#1a202c;">Google</div>
                                <div style="font-size:12px;color:#64748b;">Đăng nhập nhanh bằng tài khoản Google</div>
                            </div>
                        </div>
                        @if($user->google_id)
                            <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Đã liên kết
                            </span>
                        @else
                            <a href="{{ route('auth.google') }}" style="display:inline-flex;align-items:center;gap:6px;background:#1e73be;color:#fff;font-size:12px;font-weight:600;padding:7px 14px;border-radius:8px;text-decoration:none;transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                Kết nối Google
                            </a>
                        @endif
                    </div>

                    {{-- Xác minh email --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#1a202c;">Xác minh Email</div>
                                <div style="font-size:12px;color:#64748b;">{{ $user->email }}</div>
                            </div>
                        </div>
                        @if($user->email_verified_at)
                            <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Đã xác minh
                            </span>
                        @else
                            <form method="POST" action="{{ route('email.resend') }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="display:inline-flex;align-items:center;gap:6px;background:#f59e0b;color:#fff;font-size:12px;font-weight:600;padding:7px 14px;border-radius:8px;border:none;cursor:pointer;">
                                    Gửi xác minh
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Đổi mật khẩu --}}
            <div class="up-card" id="doi-mat-khau">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="#1e73be" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="#1e73be" stroke-width="2" stroke-linecap="round"/></svg>
                    Đổi mật khẩu
                </div>
                <form method="POST" action="{{ route('profile.change-password') }}" class="up-form">
                    @csrf @method('PUT')
                    <div class="up-field">
                        <label>Mật khẩu hiện tại *</label>
                        <input type="password" name="current_password" required>
                        @error('current_password')<div class="up-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="up-grid-2">
                        <div class="up-field">
                            <label>Mật khẩu mới *</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="up-field">
                            <label>Xác nhận mật khẩu *</label>
                            <input type="password" name="password_confirmation" required>
                        </div>
                    </div>
                    <button type="submit" class="up-btn up-btn-green">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
