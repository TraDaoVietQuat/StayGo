@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu')
@section('header_class', '')

@push('styles')
<style>
html body .auth-rp-input,
html body .auth-rp-input:focus {
  background: rgba(255,255,255,0.08) !important;
  border: 1px solid rgba(255,255,255,0.25) !important;
  border-radius: 10px !important;
  color: #fff !important;
  padding: 12px 14px !important;
  font-size: 14px !important;
  width: 100% !important;
  box-sizing: border-box !important;
  outline: none !important;
  transition: border-color .2s !important;
}
html body .auth-rp-input:focus {
  border-color: rgba(56,189,248,.6) !important;
  background: rgba(255,255,255,0.12) !important;
}
html body .auth-rp-input::placeholder { color: rgba(255,255,255,0.45) !important; }
.auth-rp-label {
  display: block; font-size: 12.5px; font-weight: 600;
  color: rgba(255,255,255,0.75); margin-bottom: 6px;
}
.auth-rp-field { margin-bottom: 16px; }
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

        <h2 class="auth-card-title">Đặt lại mật khẩu</h2>
        <p class="auth-card-sub">Nhập mật khẩu mới cho tài khoản của bạn</p>

        @if($errors->any())
        <div class="auth-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}" class="auth-form">
            @csrf

            <div class="auth-rp-field">
                <label class="auth-rp-label">Mật khẩu mới</label>
                <input type="password" name="password" required class="auth-rp-input"
                    placeholder="Tối thiểu 6 ký tự" autocomplete="new-password">
            </div>

            <div class="auth-rp-field" style="margin-bottom:20px;">
                <label class="auth-rp-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" required class="auth-rp-input"
                    placeholder="Nhập lại mật khẩu" autocomplete="new-password">
            </div>

            <button type="submit" class="auth-btn">Cập nhật mật khẩu</button>
        </form>

        <p class="auth-footer-text" style="margin-top:16px;">
            <a href="{{ route('login') }}" style="color:rgba(255,255,255,.7);">← Quay lại đăng nhập</a>
        </p>
    </div>
</div>
@endsection

