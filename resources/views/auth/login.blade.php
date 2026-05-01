@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('header_class', 'header-transparent')

@push('styles')
<style>
/* Force transparent inputs — override global input rule from style.css */
html body .auth-ig-input,
html body .auth-ig-input:focus,
html body .auth-ig-input:hover,
html body .auth-ig-input:active {
  background: transparent !important;
  background-color: transparent !important;
  border: none !important;
  border-bottom: 1.5px solid rgba(255,255,255,0.4) !important;
  border-radius: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  -webkit-box-shadow: none !important;
  margin: 0 !important;
}
html body .auth-ig-input:focus {
  border-bottom-color: #38bdf8 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@section('content')
<div class="auth-fullscreen">
    {{-- Background --}}
    <div class="auth-bg">
        <img src="{{ asset('assets/images/dn.jpg') }}" alt="" fetchpriority="high">
        <div class="auth-bg-overlay"></div>
    </div>

    {{-- Card glassmorphism --}}
    <div class="auth-card">
        <div class="auth-card-logo">
            <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:40px;">
        </div>

        <h2 class="auth-card-title">Đăng nhập</h2>
        <p class="auth-card-sub">Chào mừng trở lại với StayGo</p>

        @if($errors->any())
        <div class="auth-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="email" name="email" id="auth_email" value="{{ old('email') }}" required
                    class="auth-ig-input" autocomplete="email">
                <label for="auth_email" class="auth-ig-label">Email của bạn</label>
            </div>

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" name="password" id="auth_password" required
                    class="auth-ig-input" autocomplete="current-password">
                <label for="auth_password" class="auth-ig-label">Mật khẩu</label>
            </div>

            <div class="auth-row" style="margin-bottom:4px;">
                <a href="{{ route('password.forgot') }}" class="auth-forgot">Quên mật khẩu?</a>
            </div>

            <div class="auth-recaptcha-wrap">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
            </div>
            @error('g-recaptcha-response')
            <p class="auth-error">Vui lòng xác minh bạn không phải robot.</p>
            @enderror

            <button type="submit" class="auth-btn">Đăng nhập</button>
        </form>

        <div class="auth-divider"><span>hoặc</span></div>

        <a href="{{ route('auth.google') }}" class="auth-social-btn">
            <img src="{{ asset('assets/images/google.png') }}" width="18" alt="Google"> Đăng nhập với Google
        </a>

        <a href="{{ route('login.otp') }}" class="auth-social-btn" style="margin-top:10px;background:rgba(56,189,248,.15);border-color:rgba(56,189,248,.4);">
            <svg width="18" height="18" fill="none" stroke="#38bdf8" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span style="color:#fff;">Đăng nhập bằng OTP qua Email</span>
        </a>

        <p class="auth-footer-text">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </p>
    </div>
</div>
@endsection
