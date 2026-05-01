@extends('layouts.app')
@section('title', 'Thanh toán · ' . $room->hotel->name)

@section('content')

{{-- ── STEPS BAR ── --}}
<div class="bk-steps-bar">
    <div class="bk-steps-inner">
        <a href="{{ url()->previous() }}" class="bk-back-hotel">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            {{ Str::limit($room->hotel->name, 32) }}
        </a>
        <div class="bk-steps-nav">
            <div class="bk-sn-item bk-sn-done">
                <span class="bk-sn-circle">✓</span>
                <span class="bk-sn-label">Xem lại</span>
            </div>
            <div class="bk-sn-line"></div>
            <div class="bk-sn-item bk-sn-active">
                <span class="bk-sn-circle">2</span>
                <span class="bk-sn-label">Thanh toán</span>
            </div>
        </div>
    </div>
</div>

{{-- ── COUNTDOWN BANNER ── --}}
<div class="bk-countdown-bar">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
    <span>Vui lòng hoàn tất thanh toán trong &nbsp;</span>
    <span class="bk-timer" id="bkTimer">15:00</span>
    <span>&nbsp;để giữ giá hiện tại.</span>
</div>

@if($errors->any())
<div class="bk-alert-bar">
    <div class="bk-container">
        <div class="bk-alert-error">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
</div>
@endif

<div class="bk-page">
<div class="bk-container">
<div class="bk-layout">

{{-- ══════════ LEFT COLUMN ══════════ --}}
<div class="bk-main">
<form id="bkForm" method="POST" action="{{ route('booking.store') }}"
      data-price="{{ $displayPrice ?? $room->price }}"
      onsubmit="mergeSpecialRequests(this)">
    @csrf
    <div style="display:none;position:absolute;left:-9999px" aria-hidden="true">
        <input type="text"  name="_hp_name"  tabindex="-1" autocomplete="off">
        <input type="email" name="_hp_email" tabindex="-1" autocomplete="off">
    </div>
    <input type="hidden" name="room_id"   value="{{ $room->id }}">
    <input type="hidden" name="stay_type" value="{{ $stayType ?? 'night' }}">

    {{-- ── CONTACT + DATES CARD ── --}}
    <div class="bk-card">
        <div class="bk-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Thông tin đặt phòng
        </div>

        {{-- Room + Hotel info --}}
        <div class="bk-room-info">
            <div class="bk-room-info-hotel">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                {{ $room->hotel->name }}
            </div>
            <div class="bk-room-info-room">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 9V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v2"/><path d="M2 9h20v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9z"/></svg>
                {{ $room->room_name }}
                @if($room->package_name)
                <span class="bk-room-info-pkg">· {{ $room->package_name }}</span>
                @endif
            </div>
        </div>

        <div class="bk-grid-2">
            <div class="bk-field bk-field-full">
                <label>Họ tên <span class="bk-req">*</span>
                    <span class="bk-label-note">Người Việt: Tên đệm + Tên + Họ. Người nước ngoài: Tên + Họ.</span>
                </label>
                <input type="text" name="full_name" autocomplete="name"
                    value="{{ old('full_name', auth()->user()?->full_name) }}"
                    required placeholder="Nguyễn Văn A">
            </div>
            <div class="bk-field">
                <label>Điện thoại <span class="bk-req">*</span></label>
                <div class="bk-phone-row">
                    <div class="bk-phone-prefix">
                        <span class="bk-phone-flag">🇻🇳</span>
                        <select class="bk-phone-select" name="_phone_code">
                            <option value="+84">+84</option>
                            <option value="+1">+1</option>
                            <option value="+44">+44</option>
                            <option value="+61">+61</option>
                            <option value="+65">+65</option>
                            <option value="+66">+66</option>
                            <option value="+81">+81</option>
                            <option value="+82">+82</option>
                            <option value="+86">+86</option>
                        </select>
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                    <input type="tel" name="phone" autocomplete="tel"
                        value="{{ old('phone', auth()->user()?->phone) }}"
                        required placeholder="901 234 567">
                </div>
            </div>
            <div class="bk-field">
                <label>Email <span class="bk-req">*</span></label>
                <input type="email" name="email" autocomplete="email"
                    value="{{ old('email', auth()->user()?->email) }}"
                    required placeholder="email@example.com">
            </div>
            <div class="bk-field">
                <label>Ngày nhận phòng <span class="bk-req">*</span></label>
                <input type="date" name="check_in" id="bkCheckin"
                    value="{{ old('check_in', $checkin) }}" required min="{{ date('Y-m-d') }}"
                    onchange="document.getElementById('bkCheckout').min=this.value; calcNights();">
            </div>
            <div class="bk-field">
                <label>Ngày trả phòng <span class="bk-req">*</span></label>
                <input type="date" name="check_out" id="bkCheckout"
                    value="{{ old('check_out', $checkout) }}" required
                    onchange="calcNights()">
            </div>
        </div>

        @if($availableCount <= 0)
        <div class="bk-avail-warn bk-avail-full" style="margin-top:12px">⚠️ <strong>Phòng đã hết chỗ</strong> cho khoảng thời gian này.</div>
        @elseif($availableCount <= 2)
        <div class="bk-avail-warn bk-avail-low" style="margin-top:12px">🔥 <strong>Chỉ còn {{ $availableCount }} phòng!</strong> Đặt ngay để giữ chỗ.</div>
        @endif

        <div class="bk-nights-info" id="bkNightsInfo" style="display:none">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="#1e73be"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
            <span id="bkNightsText"></span>
            <span class="bk-vat-tag {{ $room->is_tax_included ? 'bk-vat-in' : 'bk-vat-ex' }}">
                {{ $room->is_tax_included ? '✓ Đã gồm VAT' : '* Chưa gồm VAT' }}
            </span>
        </div>

        {{-- Special requests --}}
        <div class="bk-sr-section">
            <div class="bk-sr-title">Yêu cầu đặc biệt <span class="bk-sr-opt">(tuỳ chọn)</span></div>
            <div class="bk-req-grid">
                <label class="bk-req-item">
                    <input type="checkbox" name="special_requests[]" value="Phòng không hút thuốc">
                    <span class="bk-req-box"></span><span>🚭 Không hút thuốc</span>
                </label>
                <label class="bk-req-item">
                    <input type="checkbox" name="special_requests[]" value="Phòng thông nhau">
                    <span class="bk-req-box"></span><span>🚪 Phòng thông nhau</span>
                </label>
                <label class="bk-req-item">
                    <input type="checkbox" name="special_requests[]" value="Tầng cao">
                    <span class="bk-req-box"></span><span>🏢 Tầng cao</span>
                </label>
            </div>
            <div class="bk-field" style="margin-top:10px">
                <textarea name="note" id="bkNote" rows="2"
                    placeholder="Ghi chú thêm, giờ nhận phòng dự kiến...">{{ old('note') }}</textarea>
            </div>
        </div>
    </div>{{-- end contact card --}}

    {{-- ── PAYMENT TITLE ── --}}
    <div class="bk-pay-title-row">
        <span class="bk-pay-title">Bạn muốn thanh toán thế nào?</span>
        <span class="bk-pay-secure">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Thanh toán an toàn
        </span>
    </div>

    {{-- ── PAYMENT METHODS CARD ── --}}
    <div class="bk-card bk-pm-card">

        {{-- VietQR --}}
        <label class="bk-pm-row" id="pm-vietqr">
            <input type="radio" name="payment_method" value="vietqr" {{ old('payment_method')==='vietqr'?'checked':'' }}>
            <span class="bk-pm-radio"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name">VietQR</span>
            </span>
            <span class="bk-pm-right">
                <span class="bk-pm-badge-deal">Ưu đãi giảm giá</span>
                <img src="{{ asset('assets/images/vietqr.png') }}" alt="VietQR" class="bk-pm-logo" onerror="this.style.display='none'">
            </span>
        </label>

        {{-- Digital Wallet --}}
        <label class="bk-pm-row" id="pm-wallet">
            <input type="radio" name="payment_method" value="wallet" {{ old('payment_method')==='wallet'?'checked':'' }}>
            <span class="bk-pm-radio"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name">Ví điện tử</span>
            </span>
            <span class="bk-pm-right">
                <img src="{{ asset('assets/images/momo.png') }}" alt="MoMo" class="bk-pm-logo-sm" onerror="this.style.display='none'">
                <span class="bk-pm-logo-text bk-pm-logo-zalo">Zalo</span>
                <span class="bk-pm-logo-text bk-pm-logo-shopeepay">Shopee</span>
            </span>
        </label>

        {{-- Mobile Banking --}}
        <label class="bk-pm-row" id="pm-mbanking">
            <input type="radio" name="payment_method" value="bank" {{ old('payment_method','bank')==='bank'?'checked':'' }}>
            <span class="bk-pm-radio"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name">Ngân hàng di động</span>
                <span class="bk-pm-sub">Chuyển khoản nội địa</span>
            </span>
            <span class="bk-pm-right">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1e73be" stroke-width="1.5" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
            </span>
        </label>

        {{-- Credit/Debit Card (default selected) --}}
        <label class="bk-pm-row bk-pm-row-selected" id="pm-card">
            <input type="radio" name="payment_method" value="card" {{ old('payment_method')==='card'||(!old('payment_method')&&old('payment_method')!='bank'&&old('payment_method')!='vietqr'&&old('payment_method')!='wallet'&&old('payment_method')!='hotel'&&old('payment_method')!='mbanking') ?'checked':'' }}>
            <span class="bk-pm-radio bk-pm-radio-checked"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name">Thẻ thanh toán</span>
                <span class="bk-pm-sub">Thẻ quốc tế & nội địa</span>
            </span>
            <span class="bk-pm-right">
                <span class="bk-cl bk-cl-visa">VISA</span>
                <span class="bk-cl bk-cl-mc">MC</span>
                <span class="bk-cl bk-cl-jcb">JCB</span>
                <span class="bk-cl bk-cl-amex">AMEX</span>
            </span>
        </label>

        {{-- Card form (expanded under card option) --}}
        <div class="bk-card-form" id="bkCardForm">
            <div class="bk-cf-inner">
                <div class="bk-cf-row">
                    <div class="bk-field bk-field-full">
                        <label>Số thẻ tín dụng</label>
                        <div class="bk-card-input-wrap">
                            <input type="text" id="cardNumber" placeholder="•••• •••• •••• ••••" maxlength="19" autocomplete="cc-number">
                            <div class="bk-card-type-icons">
                                <span class="bk-cl bk-cl-visa" id="ciVisa">VISA</span>
                                <span class="bk-cl bk-cl-mc"   id="ciMc">MC</span>
                                <span class="bk-cl bk-cl-jcb"  id="ciJcb">JCB</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bk-cf-row bk-cf-row-3">
                    <div class="bk-field">
                        <label>Hợp lệ đến (MM/YY)</label>
                        <input type="text" id="cardExpiry" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp">
                    </div>
                    <div class="bk-field">
                        <label>CVV / CVN <span class="bk-cvv-tip" title="3 chữ số sau thẻ">?</span></label>
                        <input type="text" id="cardCvv" placeholder="•••" maxlength="4" autocomplete="cc-csc">
                    </div>
                    <div class="bk-field">
                        <label>Tên trên thẻ</label>
                        <input type="text" id="cardName" placeholder="NGUYEN VAN A" style="text-transform:uppercase" autocomplete="cc-name">
                    </div>
                </div>
                <div class="bk-cf-ssl">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Kết nối được mã hóa SSL 256-bit. Thông tin thẻ của bạn được bảo mật tuyệt đối.
                </div>
            </div>
        </div>

        {{-- At hotel --}}
        <label class="bk-pm-row" id="pm-hotel">
            <input type="radio" name="payment_method" value="hotel" {{ old('payment_method')==='hotel'?'checked':'' }}>
            <span class="bk-pm-radio"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name">Tại khách sạn</span>
                <span class="bk-pm-sub">Thanh toán khi nhận phòng</span>
            </span>
            <span class="bk-pm-right">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.5" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
            </span>
        </label>

        {{-- VietinBank (disabled) --}}
        <div class="bk-pm-row bk-pm-disabled">
            <span class="bk-pm-radio bk-pm-radio-disabled"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name" style="color:#94a3b8">Chuyển tiền qua VietinBank</span>
                <span class="bk-pm-sub" style="color:#ef4444">⏰ Chỉ khả dụng 02:00 – 21:50</span>
            </span>
        </div>

        {{-- Installment (disabled) --}}
        <div class="bk-pm-row bk-pm-disabled">
            <span class="bk-pm-radio bk-pm-radio-disabled"></span>
            <span class="bk-pm-info">
                <span class="bk-pm-name" style="color:#94a3b8">Trả góp thẻ tín dụng</span>
                <span class="bk-pm-sub" style="color:#94a3b8">Dưới mức tối thiểu cho phép</span>
            </span>
        </div>

    </div>{{-- end pm-card --}}

    {{-- ── PROMO CODE ── --}}
    @if(!($hasPromo ?? false))
    <div class="bk-card">
        <div class="bk-card-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1.5" fill="currentColor"/></svg>
            Mã giảm giá
        </div>
        <div class="bk-promo-row">
            <input type="text" id="promoCodeInput" name="promo_code"
                placeholder="Thêm mã giảm..." value="{{ old('promo_code') }}"
                class="bk-promo-input" oninput="this.value=this.value.toUpperCase()">
            <button type="button" onclick="applyPromo()" class="bk-promo-btn">Thêm mã</button>
        </div>
        <div id="promoMsg" class="bk-promo-msg"></div>
        <input type="hidden" id="promoApplied" name="promo_applied" value="0">
    </div>
    @else
    <div class="bk-promo-active-bar">
        <span>🎉</span>
        <span><strong>Ưu đãi 10% đang áp dụng!</strong> — Mã <code>NEWUSER10</code></span>
        <span class="bk-promo-active-tag">−10%</span>
    </div>
    @endif

    {{-- ── TOTAL + CTA (bottom of left col) ── --}}
    <div class="bk-cta-block">
        <div class="bk-cta-total" onclick="togglePriceBreakdown()">
            <span>Tổng giá tiền:&nbsp; <strong class="bk-cta-total-amt" id="sdTotalVal">—</strong></span>
            <svg class="bk-cta-chevron" id="bkTotalChevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <div class="bk-cta-breakdown" id="bkPriceBreakdown" style="display:none">
            <div class="bk-sps-row"><span>Giá phòng</span><span id="sdPriceRoom">—</span></div>
            <div class="bk-sps-row" id="sdDiscountRow" style="display:none">
                <span>🎉 Giảm giá</span>
                <span id="sdDiscountVal" style="color:#22c55e;font-weight:600"></span>
            </div>
            <div class="bk-sps-row" style="color:#94a3b8;font-style:italic"><span>Thuế & phí</span><span>Hiển thị khi xác nhận</span></div>
        </div>
        <button type="submit" form="bkForm" class="bk-cta-btn" id="bkCtaBtn">
            Thanh toán &nbsp;<span id="bkCtaMethodName">Thẻ thanh toán</span>
        </button>
        <div class="bk-cta-terms">Bằng cách thanh toán, bạn đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a> của StayGo.</div>
    </div>

</form>
</div>{{-- end bk-main --}}

{{-- ══════════ RIGHT SIDEBAR ══════════ --}}
<aside class="bk-sidebar">
    <div class="bk-sum-card">

        {{-- Blue header --}}
        <div class="bk-sum-header">
            <div class="bk-sum-header-left">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                <span>Tóm tắt khách sạn</span>
            </div>
            @php $bookingRef = 'SG' . date('ymd') . rand(1000,9999); @endphp
            <div class="bk-sum-code">Mã: {{ $bookingRef }}</div>
        </div>

        {{-- Body --}}
        <div class="bk-sum-body">

            {{-- Hotel name --}}
            <div class="bk-sum-hotel-name">{{ $room->hotel->name }}</div>

            {{-- Dates --}}
            <div class="bk-sum-dates">
                <div class="bk-sum-date-col">
                    <div class="bk-sum-date-lbl">Nhận phòng</div>
                    <div class="bk-sum-date-val" id="sdIn">—</div>
                    <div class="bk-sum-date-time">Từ 14:00</div>
                </div>
                <div class="bk-sum-date-mid">
                    <div class="bk-sum-nights-badge" id="sdNights">—</div>
                    <svg width="18" height="10" viewBox="0 0 24 12" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M0 6h20M15 1l5 5-5 5"/></svg>
                </div>
                <div class="bk-sum-date-col">
                    <div class="bk-sum-date-lbl">Trả phòng</div>
                    <div class="bk-sum-date-val" id="sdOut">—</div>
                    <div class="bk-sum-date-time">Trước 12:00</div>
                </div>
            </div>

            {{-- Room type --}}
            <div class="bk-sum-section">
                <div class="bk-sum-section-lbl">Loại phòng</div>
                <div class="bk-sum-section-val">{{ $room->room_name }}</div>
                @if($room->package_name)
                <div class="bk-sum-section-sub">{{ $room->package_name }}</div>
                @endif
            </div>

            {{-- Amenities row --}}
            <div class="bk-sum-amen-row">
                @if($room->max_guests)
                <span class="bk-sum-amen-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    {{ $room->max_guests }} khách
                </span>
                @endif
                @if($room->bed_type)
                <span class="bk-sum-amen-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>
                    {{ $room->bed_type }}
                </span>
                @endif
                @php $roomAm = (array)($room->room_amenities ?? []); @endphp
                @if(!in_array('breakfast', $roomAm))
                <span class="bk-sum-amen-chip bk-sum-amen-no">Không bữa sáng</span>
                @else
                <span class="bk-sum-amen-chip">🍳 Bữa sáng</span>
                @endif
                @if(in_array('wifi', $roomAm))
                <span class="bk-sum-amen-chip">📶 WiFi</span>
                @else
                <span class="bk-sum-amen-chip bk-sum-amen-no">Không WiFi</span>
                @endif
            </div>

            <div class="bk-sum-divider"></div>

            {{-- Special requests --}}
            <div class="bk-sum-section" id="sdSpecialReqSection" style="display:none">
                <div class="bk-sum-section-lbl">Yêu cầu đặc biệt</div>
                <div class="bk-sum-section-val" id="sdSpecialReqVal" style="font-size:12px;color:#4a5568"></div>
            </div>

            {{-- Guest + policy --}}
            <div class="bk-sum-section">
                <div class="bk-sum-section-lbl">Tên khách</div>
                <div class="bk-sum-guest-row">
                    <span class="bk-sum-section-val" id="sdGuestName">{{ auth()->user()?->full_name ?? '—' }}</span>
                    @if($room->is_refundable)
                        <span class="bk-policy-chip bk-policy-ok">Hoàn tiền</span>
                    @else
                        <span class="bk-policy-chip bk-policy-no">Không hoàn tiền</span>
                    @endif
                    <span class="bk-policy-chip bk-policy-no">Không đổi lịch</span>
                </div>
            </div>

            {{-- Contact info --}}
            <div class="bk-sum-contact">
                <div class="bk-sum-avatar" id="sdAvatar">{{ strtoupper(substr(auth()->user()?->full_name ?? 'U', 0, 1)) }}</div>
                <div class="bk-sum-contact-info">
                    <div class="bk-sum-contact-name" id="sdContactName">{{ auth()->user()?->full_name ?? '—' }}</div>
                    <div class="bk-sum-contact-detail" id="sdContactPhone">{{ auth()->user()?->phone ?? '—' }}</div>
                    <div class="bk-sum-contact-detail" id="sdContactEmail">{{ auth()->user()?->email ?? '—' }}</div>
                </div>
            </div>

        </div>{{-- end sum-body --}}

        {{-- Green badge --}}
        <div class="bk-sum-green-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
            Sự lựa chọn tuyệt vời cho kỳ nghỉ của bạn!
        </div>

    </div>
</aside>

</div>{{-- end bk-layout --}}
</div>{{-- end bk-container --}}
</div>{{-- end bk-page --}}

@push('styles')
<style>
/* ══ BOOKING PAGE ══════════════════════════════════════════════════════ */
/* Reset global input override */
.bk-page input,.bk-page textarea,.bk-page select{margin:0;box-sizing:border-box}
.bk-steps-bar{background:#fff;border-bottom:1px solid #e8edf2;padding:0 24px}
.bk-steps-inner{max-width:1100px;margin:0 auto;height:52px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.bk-back-hotel{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#1e73be;text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px}
.bk-back-hotel:hover{color:#1557a0}
.bk-steps-nav{display:flex;align-items:center;flex-shrink:0}
.bk-sn-item{display:flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600}
.bk-sn-circle{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:700;flex-shrink:0}
.bk-sn-done .bk-sn-circle{background:#22c55e;color:#fff}
.bk-sn-done .bk-sn-label{color:#22c55e}
.bk-sn-active .bk-sn-circle{background:#1e73be;color:#fff}
.bk-sn-active .bk-sn-label{color:#1e73be}
.bk-sn-line{width:36px;height:2px;background:#e2e8f0;margin:0 8px;flex-shrink:0}

/* Countdown */
.bk-countdown-bar{background:#eff6ff;border-bottom:2px solid #bfdbfe;padding:9px 24px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:13px;color:#1e40af}
.bk-timer{font-weight:800;font-size:15px;color:#1e73be;font-variant-numeric:tabular-nums;min-width:40px}

/* Alert */
.bk-alert-bar{background:#fff5f5;border-bottom:1px solid #fca5a5;padding:8px 24px}
.bk-alert-error{max-width:1060px;margin:0 auto;font-size:13px;color:#b91c1c;display:flex;align-items:flex-start;gap:8px}
.bk-alert-error ul{margin:0;padding-left:16px}

/* Page */
.bk-page{background:#f5f7fa;min-height:calc(100vh - 104px);padding:24px 16px 60px}
.bk-container{max-width:1060px;margin:0 auto}
.bk-layout{display:grid;grid-template-columns:1fr 320px;gap:22px;align-items:start}
.bk-main{min-width:0}

/* Cards */
.bk-card{background:#fff;border-radius:12px;padding:22px;box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:12px;border:1px solid #edf2f7}
.bk-card-title{display:flex;align-items:center;gap:8px;font-size:14px;font-weight:700;color:#1a202c;margin-bottom:16px}

/* Room info strip */
.bk-room-info{background:#f0f6ff;border:1px solid #dbeafe;border-radius:10px;padding:11px 14px;margin-bottom:16px;display:flex;flex-direction:column;gap:6px}
.bk-room-info-hotel{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:#1a202c}
.bk-room-info-room{display:flex;align-items:center;gap:7px;font-size:12.5px;font-weight:500;color:#4a5568}
.bk-room-info-pkg{color:#94a3b8;font-weight:400}

/* Fields */
.bk-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.bk-field{display:flex;flex-direction:column;gap:5px}
.bk-field-full{grid-column:1/-1}
.bk-field label{font-size:12.5px;font-weight:600;color:#374151}
.bk-req{color:#ef4444}
.bk-label-note{font-weight:400;color:#94a3b8;font-size:10.5px;display:block;margin-top:1px}
.bk-field input,.bk-field textarea{border:1.5px solid #e2e8f0!important;border-radius:12px!important;padding:10px 14px!important;font-size:13.5px!important;color:#1a202c!important;font-family:'Be Vietnam Pro','Segoe UI',Arial,sans-serif!important;transition:border-color .2s,box-shadow .2s;outline:none!important;width:100%;box-sizing:border-box!important;margin:0!important;background:#fff!important}
.bk-field input:focus,.bk-field textarea:focus{border-color:#1e73be!important;box-shadow:0 0 0 3px rgba(30,115,190,.1)!important}
.bk-field input::placeholder,.bk-field textarea::placeholder{color:#c1c9d4!important;font-weight:400!important}
.bk-field textarea{resize:vertical;margin:0!important}

/* Phone */
.bk-phone-row{display:flex;border:1.5px solid #e2e8f0!important;border-radius:12px!important;overflow:hidden;transition:border-color .2s,box-shadow .2s;margin:0!important}
.bk-phone-row:focus-within{border-color:#1e73be!important;box-shadow:0 0 0 3px rgba(30,115,190,.1)!important}
.bk-phone-prefix{display:flex;align-items:center;gap:4px;padding:0 11px;background:#f8fafc;border-right:1.5px solid #e2e8f0;flex-shrink:0}
.bk-phone-flag{font-size:14px}
.bk-phone-select{border:none!important;border-radius:0!important;background:transparent!important;font-size:12px!important;font-weight:600!important;color:#374151!important;font-family:'Be Vietnam Pro','Segoe UI',Arial,sans-serif!important;outline:none;cursor:pointer;padding:0 2px!important}
.bk-phone-row input{border:none!important;border-radius:0!important;box-shadow:none!important;flex:1;padding:10px 14px!important;margin:0!important;width:auto}

/* Availability */
.bk-avail-warn{padding:9px 13px;border-radius:8px;font-size:12.5px}
.bk-avail-full{background:#fff5f5;border:1px solid #fca5a5;color:#b91c1c}
.bk-avail-low{background:#fff7ed;border:1px solid #fed7aa;color:#92400e}
.bk-nights-info{margin-top:10px;padding:9px 13px;background:#f0f7ff;border-radius:8px;font-size:12.5px;color:#1e73be;font-weight:600;display:flex;gap:7px;align-items:center;flex-wrap:wrap}
.bk-vat-tag{font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;white-space:nowrap;margin-left:auto}
.bk-vat-in{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.bk-vat-ex{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}

/* Special requests */
.bk-sr-section{margin-top:16px;border-top:1px solid #f0f4f8;padding-top:14px}
.bk-sr-title{font-size:13px;font-weight:700;color:#1a202c;margin-bottom:10px}
.bk-sr-opt{font-weight:400;color:#94a3b8;font-size:11.5px}
.bk-req-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:0}
.bk-req-item{display:flex;align-items:center;gap:7px;cursor:pointer;font-size:12.5px;color:#374151;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:12px;transition:all .15s;user-select:none}
.bk-req-item:hover{border-color:#1e73be;background:#f8fbff}
.bk-req-item:has(input:checked){border-color:#1e73be;background:#eff6ff}
.bk-req-item input{display:none}
.bk-req-box{width:15px;height:15px;border:2px solid #cbd5e0;border-radius:4px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;font-size:9px;transition:all .15s}
.bk-req-item input:checked~.bk-req-box{background:#1e73be;border-color:#1e73be;color:#fff}

/* ── Payment title row ── */
.bk-pay-title-row{display:flex;align-items:center;justify-content:space-between;margin:4px 0 8px}
.bk-pay-title{font-size:16px;font-weight:700;color:#1a202c}
.bk-pay-secure{display:flex;align-items:center;gap:5px;font-size:11.5px;color:#15803d;background:#f0fdf4;border:1px solid #bbf7d0;padding:4px 10px;border-radius:20px;font-weight:600}

/* ── Payment method rows ── */
.bk-pm-card{padding:0;overflow:hidden}
.bk-pm-row{display:flex;align-items:center;gap:12px;padding:15px 18px;cursor:pointer;border-bottom:1px solid #f0f4f8;transition:background .15s;user-select:none}
.bk-pm-row:last-child{border-bottom:none}
.bk-pm-row:hover{background:#f8fbff}
.bk-pm-row-selected,.bk-pm-row.selected{background:#eff6ff}
.bk-pm-disabled{opacity:.55;cursor:not-allowed}
.bk-pm-disabled:hover{background:transparent}
.bk-pm-row input[type=radio]{display:none}
.bk-pm-radio{width:18px;height:18px;border-radius:50%;border:2px solid #cbd5e0;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.bk-pm-radio-checked,.bk-pm-row.selected .bk-pm-radio{border-color:#1e73be;background:#fff}
.bk-pm-radio-checked::after,.bk-pm-row.selected .bk-pm-radio::after{content:'';width:8px;height:8px;border-radius:50%;background:#1e73be;display:block}
.bk-pm-radio-disabled{border-color:#e2e8f0}
.bk-pm-info{flex:1;min-width:0}
.bk-pm-name{font-size:13.5px;font-weight:600;color:#1a202c}
.bk-pm-sub{font-size:11.5px;color:#718096;margin-top:2px}
.bk-pm-right{display:flex;align-items:center;gap:5px;flex-shrink:0}
.bk-pm-badge-deal{font-size:10px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fbbf24;padding:2px 7px;border-radius:20px}
.bk-pm-logo{height:24px;width:auto;object-fit:contain}
.bk-pm-logo-sm{height:20px;width:auto;object-fit:contain}
.bk-pm-logo-text{font-size:10px;font-weight:800;padding:2px 5px;border-radius:4px}
.bk-pm-logo-zalo{background:#0068ff;color:#fff}
.bk-pm-logo-shopeepay{background:#ee4d2d;color:#fff}

/* Card logos */
.bk-cl{font-size:9.5px;font-weight:900;padding:2px 5px;border-radius:3px;letter-spacing:.4px}
.bk-cl-visa{background:#1a1f71;color:#fff}
.bk-cl-mc{background:#eb001b;color:#fff}
.bk-cl-jcb{background:#003087;color:#fff}
.bk-cl-amex{background:#007bc1;color:#fff}

/* Card form (inline expanded) */
.bk-card-form{display:none;border-top:1px solid #e8edf2;background:#f8fafc}
.bk-card-form.show{display:block}
.bk-cf-inner{padding:16px 18px;display:flex;flex-direction:column;gap:12px}
.bk-cf-row{display:grid;gap:12px}
.bk-cf-row-3{grid-template-columns:1fr 1fr 1.4fr}
.bk-card-input-wrap{position:relative}
.bk-card-input-wrap input{padding-right:100px}
.bk-card-type-icons{position:absolute;right:10px;top:50%;transform:translateY(-50%);display:flex;gap:4px}
.bk-card-type-icons .bk-cl{opacity:.3;transition:opacity .2s}
.bk-card-type-icons .bk-cl.active{opacity:1}
.bk-cvv-tip{display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;background:#e2e8f0;border-radius:50%;font-size:10px;color:#718096;cursor:help}
.bk-cf-ssl{font-size:11px;color:#15803d;display:flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:7px 10px}

/* Promo */
.bk-promo-row{display:flex;gap:10px;align-items:center}
.bk-promo-input{flex:1;border:1.5px solid #e2e8f0!important;border-radius:12px!important;padding:10px 14px!important;font-size:13.5px!important;color:#1a202c!important;font-family:'Be Vietnam Pro','Segoe UI',Arial,sans-serif!important;outline:none;transition:border-color .2s,box-shadow .2s;margin:0!important;box-sizing:border-box!important;background:#fff!important}
.bk-promo-input:focus{border-color:#1e73be!important;box-shadow:0 0 0 3px rgba(30,115,190,.1)!important}
.bk-promo-input::placeholder{color:#c1c9d4!important;font-weight:400!important}
.bk-promo-btn{padding:10px 18px;background:#1e73be;color:#fff;border:none;border-radius:12px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;white-space:nowrap}
.bk-promo-btn:hover{background:#1557a0}
.bk-promo-msg{font-size:12px;margin-top:6px;min-height:16px}
.bk-promo-active-bar{background:#fef3c7;border:1px solid #fbbf24;border-radius:10px;padding:11px 15px;margin-bottom:12px;display:flex;align-items:center;gap:10px;font-size:13px;color:#78350f}
.bk-promo-active-tag{margin-left:auto;background:#f59e0b;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;flex-shrink:0}

/* ── CTA block ── */
.bk-cta-block{background:#fff;border-radius:12px;padding:18px 22px;box-shadow:0 2px 12px rgba(0,0,0,.08);border:1px solid #edf2f7;margin-bottom:12px}
.bk-cta-total{display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:4px 0 10px;border-bottom:1px solid #f0f4f8;margin-bottom:12px}
.bk-cta-total span{font-size:14px;color:#374151;font-weight:500}
.bk-cta-total-amt{font-size:16px;font-weight:800;color:#e53e3e}
.bk-cta-chevron{color:#94a3b8;transition:transform .2s;flex-shrink:0}
.bk-cta-breakdown{margin-bottom:12px}
.bk-sps-row{display:flex;justify-content:space-between;font-size:12.5px;color:#4a5568;padding:4px 0}
.bk-cta-btn{width:100%;background:linear-gradient(135deg,#1e73be,#2563eb);color:#fff;border:none;border-radius:11px;padding:15px;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s;letter-spacing:.3px}
.bk-cta-btn:hover{background:linear-gradient(135deg,#1557a0,#1d4ed8);box-shadow:0 6px 20px rgba(30,115,190,.4);transform:translateY(-1px)}
.bk-cta-terms{margin-top:10px;font-size:11px;color:#94a3b8;text-align:center;line-height:1.5}
.bk-cta-terms a{color:#1e73be;text-decoration:none}

/* ══ SIDEBAR ══════════════════════════════════════════════════════════ */
.bk-sidebar{position:sticky;top:16px}
.bk-sum-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.09);border:1px solid #edf2f7}

/* Header */
.bk-sum-header{background:linear-gradient(135deg,#1e73be,#2563eb);padding:16px 18px;display:flex;align-items:center;justify-content:space-between}
.bk-sum-header-left{display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#fff}
.bk-sum-code{font-size:11px;color:rgba(255,255,255,.75);background:rgba(255,255,255,.15);padding:3px 9px;border-radius:20px;font-weight:600}

/* Body */
.bk-sum-body{padding:16px 18px;display:flex;flex-direction:column;gap:0}
.bk-sum-hotel-name{font-size:13.5px;font-weight:700;color:#1a202c;margin-bottom:12px;line-height:1.35}

/* Dates */
.bk-sum-dates{display:grid;grid-template-columns:1fr auto 1fr;gap:6px;align-items:center;margin-bottom:14px;padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #e8edf2}
.bk-sum-date-col{text-align:center}
.bk-sum-date-lbl{font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.bk-sum-date-val{font-size:13px;font-weight:700;color:#1a202c;margin:3px 0}
.bk-sum-date-time{font-size:10px;color:#94a3b8}
.bk-sum-date-mid{display:flex;flex-direction:column;align-items:center;gap:4px}
.bk-sum-nights-badge{background:#1e73be;color:#fff;font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap}

/* Sections */
.bk-sum-section{margin-bottom:12px}
.bk-sum-section-lbl{font-size:10.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.bk-sum-section-val{font-size:13px;font-weight:600;color:#1a202c}
.bk-sum-section-sub{font-size:11.5px;color:#64748b;margin-top:2px}

/* Amenity chips */
.bk-sum-amen-row{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:12px}
.bk-sum-amen-chip{font-size:11px;display:inline-flex;align-items:center;gap:4px;padding:3px 8px;background:#f8fafc;border:1px solid #e8edf2;border-radius:20px;color:#4a5568}
.bk-sum-amen-no{color:#94a3b8;border-style:dashed}

.bk-sum-divider{border-top:1px solid #f0f4f8;margin:4px 0 12px}

/* Guest row */
.bk-sum-guest-row{display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin-top:4px}
.bk-policy-chip{font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px}
.bk-policy-ok{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.bk-policy-no{background:#fff5f5;color:#dc2626;border:1px solid #fca5a5}

/* Contact info */
.bk-sum-contact{display:flex;align-items:flex-start;gap:10px;margin-top:4px;padding:10px;background:#f8fafc;border-radius:9px}
.bk-sum-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1e73be,#2563eb);color:#fff;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.bk-sum-contact-info{min-width:0}
.bk-sum-contact-name{font-size:13px;font-weight:700;color:#1a202c}
.bk-sum-contact-detail{font-size:11.5px;color:#64748b;margin-top:2px}

/* Green badge */
.bk-sum-green-badge{background:#f0fdf4;border-top:1px solid #bbf7d0;padding:11px 18px;display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:#15803d}

/* ══ RESPONSIVE ══════════════════════════════════════════════════════ */
@media(max-width:780px){
    /* Layout */
    .bk-layout{grid-template-columns:1fr;gap:14px}
    .bk-sidebar{position:static;order:2}
    .bk-main{order:1}
    .bk-page{padding:16px 12px 48px}

    /* Steps bar */
    .bk-steps-bar{padding:0 12px}
    .bk-steps-inner{flex-direction:column;height:auto;padding:10px 0;gap:8px}
    .bk-back-hotel{max-width:100%}

    /* Countdown */
    .bk-countdown-bar{padding:8px 12px;font-size:12px;flex-wrap:wrap;gap:3px;text-align:center}

    /* Cards */
    .bk-card{padding:16px}
    .bk-cta-block{padding:14px 16px}

    /* Fields */
    .bk-grid-2{grid-template-columns:1fr;gap:12px}
    .bk-field-full{grid-column:auto}

    /* Special requests */
    .bk-req-grid{grid-template-columns:1fr 1fr}

    /* Payment title */
    .bk-pay-title-row{flex-direction:column;align-items:flex-start;gap:6px}

    /* Payment rows */
    .bk-pm-row{padding:13px 14px;gap:10px}
    .bk-pm-right{flex-wrap:wrap;gap:4px}

    /* Card form */
    .bk-cf-row-3{grid-template-columns:1fr 1fr}
    .bk-cf-inner{padding:14px}

    /* VAT tag — wrap xuống dòng riêng */
    .bk-nights-info{flex-wrap:wrap}
    .bk-vat-tag{margin-left:0}

    /* Room info */
    .bk-room-info{padding:10px 12px}

    /* Sidebar dates */
    .bk-sum-dates{gap:4px}
    .bk-sum-date-val{font-size:12px}
    .bk-sum-body{padding:14px}
    .bk-sum-header{padding:13px 14px}
}
@media(max-width:480px){
    .bk-page{padding:12px 8px 40px}
    .bk-card{padding:14px}
    .bk-req-grid{grid-template-columns:1fr}
    .bk-cf-row-3{grid-template-columns:1fr}

    /* Steps — chỉ hiện step hiện tại */
    .bk-sn-done .bk-sn-label,.bk-sn-line{display:none}

    /* Phone row — flag + select nhỏ lại */
    .bk-phone-prefix{padding:0 8px}
    .bk-phone-flag{font-size:13px}

    /* Countdown gọn */
    .bk-countdown-bar{font-size:11.5px}
    .bk-timer{font-size:13px}

    /* Payment logos */
    .bk-cl{font-size:8.5px;padding:2px 4px}
    .bk-pm-badge-deal{display:none}

    /* Promo */
    .bk-promo-row{flex-direction:column}
    .bk-promo-input{width:100%!important}
    .bk-promo-btn{width:100%}

    /* CTA */
    .bk-cta-btn{font-size:14px;padding:13px}
}
</style>
@endpush

@push('scripts')
<script>
const roomPricePerNight = parseInt(document.querySelector('#bkForm').dataset.price);
const hasPromo = {{ ($hasPromo ?? false) ? 'true' : 'false' }};
const stayType = '{{ $stayType ?? "night" }}';
const stayUnit = stayType === 'day' ? 'ngày' : 'đêm';

// ── Countdown timer ───────────────────────────────────────
let timerSec = 15 * 60;
const timerEl = document.getElementById('bkTimer');
function tickTimer() {
    if (timerSec <= 0) { timerEl.textContent = '00:00'; return; }
    timerSec--;
    const m = String(Math.floor(timerSec / 60)).padStart(2,'0');
    const s = String(timerSec % 60).padStart(2,'0');
    timerEl.textContent = m + ':' + s;
    setTimeout(tickTimer, 1000);
}
tickTimer();

// ── Payment method selection ──────────────────────────────
const pmMethodNames = {
    vietqr:'VietQR', wallet:'Ví điện tử', bank:'Ngân hàng',
    card:'Thẻ thanh toán', hotel:'Tại khách sạn'
};
document.querySelectorAll('.bk-pm-row:not(.bk-pm-disabled)').forEach(row => {
    const radio = row.querySelector('input[type=radio]');
    if (!radio) return;
    if (radio.checked) activatePayRow(row, radio.value);
    row.addEventListener('click', function() {
        document.querySelectorAll('.bk-pm-row').forEach(r => {
            r.classList.remove('selected','bk-pm-row-selected');
            const rr = r.querySelector('.bk-pm-radio');
            if (rr) { rr.classList.remove('bk-pm-radio-checked'); rr.style.borderColor=''; rr.style.background=''; }
        });
        activatePayRow(this, radio.value);
    });
});
function activatePayRow(row, value) {
    row.classList.add('selected');
    const radioEl = row.querySelector('.bk-pm-radio');
    if (radioEl) radioEl.classList.add('bk-pm-radio-checked');
    const radioInput = row.querySelector('input[type=radio]');
    if (radioInput) radioInput.checked = true;
    document.getElementById('bkCardForm').classList.toggle('show', value === 'card');
    const btnName = document.getElementById('bkCtaMethodName');
    if (btnName) btnName.textContent = pmMethodNames[value] || value;
}

// ── Card number formatting ────────────────────────────────
const cardInput = document.getElementById('cardNumber');
if (cardInput) {
    cardInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').substring(0,16);
        this.value = v.replace(/(.{4})/g,'$1  ').trim();
        const visa=document.getElementById('ciVisa'), mc=document.getElementById('ciMc'), jcb=document.getElementById('ciJcb');
        [visa,mc,jcb].forEach(el=>el.classList.remove('active'));
        if(/^4/.test(v)) visa.classList.add('active');
        else if(/^5[1-5]/.test(v)) mc.classList.add('active');
        else if(/^35/.test(v)) jcb.classList.add('active');
    });
}
const expiryInput = document.getElementById('cardExpiry');
if (expiryInput) {
    expiryInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').substring(0,4);
        if (v.length >= 2) v = v.substring(0,2)+' / '+v.substring(2);
        this.value = v;
    });
}

// ── Price breakdown toggle ────────────────────────────────
let priceBreakdownOpen = false;
function togglePriceBreakdown() {
    priceBreakdownOpen = !priceBreakdownOpen;
    document.getElementById('bkPriceBreakdown').style.display = priceBreakdownOpen ? 'block' : 'none';
    document.getElementById('bkTotalChevron').style.transform = priceBreakdownOpen ? 'rotate(180deg)' : '';
}

// ── Promo state ───────────────────────────────────────────
let promoDiscount = 0, promoPercent = 0;

// ── Date formatting ───────────────────────────────────────
const checkIn  = document.querySelector('input[name=check_in]');
const checkOut = document.querySelector('input[name=check_out]');
function fmtDate(dateStr) {
    if (!dateStr) return '—';
    const [y,m,d] = dateStr.split('-');
    return d + '/' + m + '/' + y;
}
function fmtPrice(n) { return n.toLocaleString('vi-VN') + 'đ'; }

function calcNights() {
    document.getElementById('sdIn').textContent  = fmtDate(checkIn.value);
    document.getElementById('sdOut').textContent = fmtDate(checkOut.value);
    if (!checkIn.value || !checkOut.value) {
        document.getElementById('sdNights').textContent = '—';
        document.getElementById('bkNightsInfo').style.display = 'none';
        document.getElementById('sdTotalVal').textContent = '—';
        return;
    }
    const d1 = new Date(checkIn.value), d2 = new Date(checkOut.value);
    const nights = Math.round((d2 - d1) / 86400000);
    if (nights <= 0) { document.getElementById('sdNights').textContent = '—'; return; }
    document.getElementById('sdNights').textContent = nights + ' ' + stayUnit;
    const baseTotal = nights * roomPricePerNight;
    let discount = hasPromo ? Math.round(baseTotal * 0.1) : (promoPercent > 0 ? Math.round(baseTotal * promoPercent / 100) : promoDiscount);
    const finalTotal = baseTotal - discount;
    document.getElementById('bkNightsInfo').style.display = 'flex';
    document.getElementById('bkNightsText').textContent = nights + ' ' + stayUnit + ' × ' + fmtPrice(roomPricePerNight) + ' = ' + fmtPrice(finalTotal);
    document.getElementById('sdPriceRoom').textContent = fmtPrice(baseTotal);
    const discRow = document.getElementById('sdDiscountRow');
    if (discRow) {
        const show = hasPromo || discount > 0;
        discRow.style.display = show ? 'flex' : 'none';
        if (show) document.getElementById('sdDiscountVal').textContent = '−' + fmtPrice(discount);
    }
    document.getElementById('sdTotalVal').textContent = fmtPrice(finalTotal);
}

// ── Sidebar contact sync ──────────────────────────────────
function syncContactSidebar() {
    const nameEl = document.querySelector('input[name=full_name]');
    const phoneEl = document.querySelector('input[name=phone]');
    const emailEl = document.querySelector('input[name=email]');
    if (nameEl) {
        const name = nameEl.value || '—';
        document.getElementById('sdGuestName').textContent = name;
        document.getElementById('sdContactName').textContent = name;
        document.getElementById('sdAvatar').textContent = name.trim() ? name.trim()[0].toUpperCase() : 'U';
    }
    if (phoneEl) document.getElementById('sdContactPhone').textContent = phoneEl.value || '—';
    if (emailEl) document.getElementById('sdContactEmail').textContent = emailEl.value || '—';
}
document.querySelector('input[name=full_name]')?.addEventListener('input', syncContactSidebar);
document.querySelector('input[name=phone]')?.addEventListener('input', syncContactSidebar);
document.querySelector('input[name=email]')?.addEventListener('input', syncContactSidebar);

// ── Special requests → sidebar ────────────────────────────
function syncSpecialRequests() {
    const checks = document.querySelectorAll('input[name="special_requests[]"]:checked');
    const section = document.getElementById('sdSpecialReqSection');
    const val = document.getElementById('sdSpecialReqVal');
    if (checks.length) {
        section.style.display = 'block';
        val.textContent = Array.from(checks).map(c=>c.value).join(', ');
    } else {
        section.style.display = 'none';
    }
}
document.querySelectorAll('input[name="special_requests[]"]').forEach(cb => {
    cb.addEventListener('change', () => {
        const box = cb.closest('.bk-req-item')?.querySelector('.bk-req-box');
        if (box) { box.textContent = cb.checked ? '✓' : ''; box.style.color = cb.checked ? '#fff' : 'transparent'; }
        syncSpecialRequests();
    });
    cb.closest('.bk-req-item')?.addEventListener('click', e => {
        if (e.target === cb) return;
        cb.checked = !cb.checked;
        cb.dispatchEvent(new Event('change'));
    });
});

// ── Promo AJAX ────────────────────────────────────────────
async function applyPromo() {
    const code  = document.getElementById('promoCodeInput')?.value?.trim();
    const msgEl = document.getElementById('promoMsg');
    if (!code) { msgEl.innerHTML = '<span style="color:#ef4444">Vui lòng nhập mã giảm giá.</span>'; return; }
    if (!checkIn.value || !checkOut.value) { msgEl.innerHTML = '<span style="color:#ef4444">Vui lòng chọn ngày trước.</span>'; return; }
    const nights = Math.round((new Date(checkOut.value) - new Date(checkIn.value)) / 86400000);
    const baseTotal = Math.max(nights,1) * roomPricePerNight;
    msgEl.innerHTML = '<span style="color:#94a3b8">Đang kiểm tra...</span>';
    try {
        const res  = await fetch("{{ route('promo.validate') }}", {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content??''},
            body:JSON.stringify({code, amount:baseTotal}),
        });
        const data = await res.json();
        if (data.valid) {
            promoPercent = data.type==='percent' ? data.value : 0;
            promoDiscount = data.discount_amount;
            document.getElementById('promoApplied').value = '1';
            msgEl.innerHTML = '<span style="color:#22c55e">✅ ' + data.message + '</span>';
            calcNights();
        } else {
            promoPercent = 0; promoDiscount = 0;
            document.getElementById('promoApplied').value = '0';
            msgEl.innerHTML = '<span style="color:#ef4444">❌ ' + data.message + '</span>';
            calcNights();
        }
    } catch(e) { msgEl.innerHTML = '<span style="color:#ef4444">Lỗi kết nối, thử lại sau.</span>'; }
}

// ── Merge special requests into note on submit ────────────
function mergeSpecialRequests(form) {
    const checks = form.querySelectorAll('input[name="special_requests[]"]:checked');
    if (!checks.length) return;
    const line = 'Yêu cầu đặc biệt: ' + Array.from(checks).map(c=>c.value).join(', ');
    const noteEl = document.getElementById('bkNote');
    noteEl.value = (noteEl.value.trim() ? noteEl.value.trim() + '\n' : '') + line;
}

checkIn.addEventListener('change', calcNights);
checkOut.addEventListener('change', calcNights);
calcNights();
syncContactSidebar();
</script>
@endpush

@endsection
