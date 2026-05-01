@extends('layouts.app')
@section('title', 'Xác thực OTP')
@section('header_class', 'header-transparent')

@push('styles')
<style>
.otp-input-row { display:flex; gap:10px; justify-content:center; margin:24px 0 8px; }
.otp-digit {
    width:48px; height:58px; text-align:center; font-size:26px; font-weight:700;
    background:rgba(255,255,255,0.12) !important; border:1.5px solid rgba(255,255,255,0.35) !important;
    border-radius:10px !important; color:#fff !important; caret-color:#38bdf8;
    transition:border-color .2s; outline:none;
}
.otp-digit:focus { border-color:#38bdf8 !important; box-shadow:0 0 0 3px rgba(56,189,248,.2) !important; }
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

        <h2 class="auth-card-title">Xác thực OTP</h2>
        <p class="auth-card-sub">
            Mã đã gửi đến <strong style="color:#38bdf8;">{{ session('reset_email') }}</strong><br>
            <small style="opacity:.7;">Có hiệu lực trong 15 phút</small>
        </p>

        @error('otp')
        <div class="auth-alert">{{ $message }}</div>
        @enderror

        @if(session('success'))
        <div class="auth-alert" style="background:rgba(16,185,129,0.2)!important;border-color:rgba(16,185,129,0.4)!important;color:#a7f3d0!important;">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.verify-otp.post') }}" id="otpForm">
            @csrf

            <div class="otp-input-row" id="otpBoxes">
                @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    class="otp-digit" data-idx="{{ $i }}" autocomplete="off">
                @endfor
            </div>

            <input type="hidden" name="otp" id="otpHidden">

            <button type="submit" class="auth-btn" id="otpSubmit" style="margin-top:16px;" disabled>
                Xác nhận mã OTP
            </button>
        </form>

        <p class="auth-footer-text" style="margin-top:16px;">
            <a href="{{ route('password.forgot') }}" style="color:rgba(255,255,255,.7);">← Gửi lại OTP</a>
            &nbsp;·&nbsp;
            <a href="{{ route('login') }}" style="color:rgba(255,255,255,.7);">Đăng nhập</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const boxes  = document.querySelectorAll('.otp-digit');
    const hidden = document.getElementById('otpHidden');
    const submit = document.getElementById('otpSubmit');

    function update() {
        const val = [...boxes].map(b => b.value).join('');
        hidden.value = val;
        submit.disabled = val.length < 6;
    }

    boxes.forEach((box, idx) => {
        box.addEventListener('input', e => {
            box.value = e.target.value.replace(/\D/g, '').slice(-1);
            if (box.value && idx < 5) boxes[idx + 1].focus();
            update();
        });
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && idx > 0) boxes[idx - 1].focus();
        });
        box.addEventListener('paste', e => {
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            [...text.slice(0, 6)].forEach((ch, i) => { if (boxes[i]) boxes[i].value = ch; });
            boxes[Math.min(text.length, 5)].focus();
            update();
            e.preventDefault();
        });
    });

    boxes[0].focus();
})();
</script>
@endpush
@endsection
