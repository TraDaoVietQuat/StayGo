@extends('layouts.app')
@section('title', 'Đăng nhập bằng OTP')
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

@section('content')
<div class="auth-fullscreen">
    <div class="auth-bg">
        <img src="{{ asset('assets/images/dn.jpg') }}" alt="" fetchpriority="high">
        <div class="auth-bg-overlay"></div>
    </div>

    <div class="auth-card">
        <div class="auth-card-logo">
            <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:40px;">
        </div>

        <h2 class="auth-card-title">Đăng nhập bằng OTP</h2>
        <p class="auth-card-sub">Nhập email để nhận mã OTP qua hộp thư của bạn</p>

        @if(session('success'))
        <div class="auth-alert" style="background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.4);color:#bbf7d0;">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="auth-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.otp.send') }}" class="auth-form">
            @csrf
            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input type="email" name="email" id="otp_email" value="{{ old('email') }}" required
                    class="auth-ig-input" autocomplete="email">
                <label for="otp_email" class="auth-ig-label">Email đã đăng ký</label>
            </div>

            <button type="submit" class="auth-btn">Gửi mã OTP</button>
        </form>

        <div class="auth-divider"><span>hoặc</span></div>

        <a href="{{ route('auth.google') }}" class="auth-social-btn">
            <img src="{{ asset('assets/images/google.png') }}" width="18" alt="Google"> Đăng nhập với Google
        </a>

        <p class="auth-footer-text" style="margin-top:16px;">
            <a href="{{ route('login') }}" style="color:rgba(255,255,255,.7);">← Đăng nhập bằng mật khẩu</a>
        </p>
    </div>
</div>
@endsection

