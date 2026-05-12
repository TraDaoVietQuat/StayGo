@extends('layouts.app')
@section('title', 'Quên mật khẩu')
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

        <h2 class="auth-card-title">Quên mật khẩu</h2>
        <p class="auth-card-sub">Nhập email để nhận mã OTP đặt lại mật khẩu</p>

        @if(session('success'))
        <div class="auth-alert" style="background:rgba(22,163,74,0.08)!important;border-color:rgba(22,163,74,0.3)!important;color:#166534!important;">
            {{ session('success') }}
        </div>
        @endif
        @error('email')
        <div class="auth-alert">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('password.send-otp') }}" class="auth-form">
            @csrf

            <div class="auth-input-group">
                <svg class="auth-ig-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input type="email" name="email" id="fp_email" value="{{ old('email') }}" required
                    class="auth-ig-input" autocomplete="email">
                <label for="fp_email" class="auth-ig-label">Email đã đăng ký</label>
            </div>

            <button type="submit" class="auth-btn">Gửi mã OTP</button>
        </form>

        <p class="auth-footer-text" style="margin-top:20px;">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </p>
    </div>
</div>
@endsection

