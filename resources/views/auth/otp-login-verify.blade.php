@extends('layouts.app')
@section('title', 'Nhập mã OTP')
@section('header_class', '')

@push('styles')
<style>
html body .auth-ig-input,
html body .auth-ig-input:focus {
  background: transparent !important;
  border: none !important;
  border-bottom: 1.5px solid rgba(255,255,255,0.4) !important;
  border-radius: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  margin: 0 !important;
}
html body .auth-ig-input:focus { border-bottom-color: #38bdf8 !important; }

.otp-input-row { display:flex; gap:10px; justify-content:center; margin:24px 0 8px; }
.otp-digit {
    width:48px; height:58px; text-align:center; font-size:26px; font-weight:700;
    background:rgba(255,255,255,0.12) !important; border:1.5px solid rgba(255,255,255,0.35) !important;
    border-radius:10px !important; color:#fff !important; caret-color:#38bdf8;
    transition:border-color .2s;
}
.otp-digit:focus { border-color:#38bdf8 !important; outline:none; box-shadow:0 0 0 3px rgba(56,189,248,.2) !important; }
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

        <h2 class="auth-card-title">Nhập mã OTP</h2>
        <p class="auth-card-sub">
            Mã đã gửi đến <strong style="color:#38bdf8;">{{ session('login_otp_email') }}</strong><br>
            <small style="opacity:.7;">Có hiệu lực trong 10 phút</small>
        </p>

        @if($errors->any())
        <div class="auth-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.otp.verify.post') }}" class="auth-form" id="otpForm">
            @csrf

            {{-- 6 ô OTP tách biệt cho UX tốt --}}
            <div class="otp-input-row" id="otpBoxes">
                @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    class="otp-digit" data-idx="{{ $i }}" autocomplete="off">
                @endfor
            </div>

            {{-- Hidden field gộp 6 chữ số --}}
            <input type="hidden" name="otp" id="otpHidden">

            <button type="submit" class="auth-btn" id="otpSubmit" disabled>Xác nhận đăng nhập</button>
        </form>

        <p class="auth-footer-text" style="margin-top:16px;">
            <a href="{{ route('login.otp') }}" style="color:rgba(255,255,255,.7);">← Gửi lại OTP</a>
            &nbsp;·&nbsp;
            <a href="{{ route('login') }}" style="color:rgba(255,255,255,.7);">Đăng nhập mật khẩu</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const boxes   = document.querySelectorAll('.otp-digit');
    const hidden  = document.getElementById('otpHidden');
    const submit  = document.getElementById('otpSubmit');

    function update() {
        const val = [...boxes].map(b => b.value).join('');
        hidden.value = val;
        submit.disabled = val.length < 6;
    }

    boxes.forEach((box, idx) => {
        box.addEventListener('input', e => {
            const v = e.target.value.replace(/\D/g, '');
            box.value = v.slice(-1);
            if (v && idx < 5) boxes[idx + 1].focus();
            update();
        });
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && idx > 0) {
                boxes[idx - 1].focus();
            }
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

