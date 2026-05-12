@extends('layouts.app')
@section('title', 'Đăng ký')
@section('header_class', '')

@push('styles')
<style>
html body .auth-ig-input,
html body .auth-ig-input:focus,
html body .auth-ig-input:hover,
html body .auth-ig-input:active {
  background: transparent !important;
  background-color: transparent !important;
  border: none !important;
  border-bottom: 1.5px solid #cccccc !important;
  border-radius: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  -webkit-box-shadow: none !important;
  margin: 0 !important;
  color: #111111 !important;
  caret-color: #111111 !important;
}
html body .auth-ig-input:focus {
  border-bottom-color: #0066cc !important;
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
    <div class="auth-card" style="max-width:460px;">
        <div class="auth-card-logo">
            <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:40px;">
        </div>

        <h2 class="auth-card-title">Đăng ký tài khoản</h2>
        <p class="auth-card-sub">Tạo tài khoản để đặt phòng dễ dàng hơn</p>

        @if($errors->any())
        <div class="auth-alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" name="full_name" id="reg_name" value="{{ old('full_name') }}" required
                    class="auth-ig-input" autocomplete="name">
                <label for="reg_name" class="auth-ig-label">Họ và tên</label>
            </div>

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input type="email" name="email" id="reg_email" value="{{ old('email') }}" required
                    class="auth-ig-input" autocomplete="email">
                <label for="reg_email" class="auth-ig-label">Email</label>
            </div>

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.6 3.41 2 2 0 0 1 3.58 1.25h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.81a16 16 0 0 0 6.29 6.29l1.42-1.42a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <input type="tel" name="phone" id="reg_phone" value="{{ old('phone') }}"
                    class="auth-ig-input" autocomplete="tel">
                <label for="reg_phone" class="auth-ig-label">Số điện thoại</label>
            </div>

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" name="password" id="reg_password" required
                    class="auth-ig-input" autocomplete="new-password">
                <label for="reg_password" class="auth-ig-label">Mật khẩu (tối thiểu 6 ký tự)</label>
            </div>

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" name="password_confirmation" id="reg_pw_confirm" required
                    class="auth-ig-input" autocomplete="new-password">
                <label for="reg_pw_confirm" class="auth-ig-label">Xác nhận mật khẩu</label>
            </div>

            <div class="auth-recaptcha-wrap">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
            </div>
            @error('g-recaptcha-response')
            <p class="auth-error">Vui lòng xác minh bạn không phải robot.</p>
            @enderror

            <button type="submit" class="auth-btn">Đăng ký</button>
        </form>

        <div class="auth-divider"><span>hoặc đăng ký nhanh</span></div>

        <a href="{{ route('auth.google') }}" class="auth-social-btn">
            <img src="{{ asset('assets/images/google.png') }}" width="18" alt="Google"> Đăng ký với Google
        </a>

        <p class="auth-footer-text">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
        </p>
    </div>
</div>
@endsection

