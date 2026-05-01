@extends('layouts.app')
@section('title', 'Thanh toán')

@section('content')
@php
    $isDay    = ($booking->stay_type ?? 'night') === 'day';
    $unit     = $isDay ? 'ngày' : 'đêm';
    $nights   = \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out);
    $nights   = max($nights, 1);
    $unitPrice = $nights > 0 ? round(($booking->total_price + ($booking->discount_amount ?? 0)) / $nights) : 0;
    $baseTotal = $unitPrice * $nights;
    $hasDisc  = ($booking->discount_amount ?? 0) > 0;
    $pmLabel  = match($booking->payment_method) {
        'bank'         => '🏦 Chuyển khoản ngân hàng',
        'momo'         => '💜 Ví MoMo',
        'vnpay'        => '🟢 VNPay',
        'card'         => '💳 Thẻ quốc tế',
        'zalopay'      => '🔵 ZaloPay',
        'bank_transfer'=> '🏦 Chuyển khoản',
        default        => '🏨 Thanh toán tại khách sạn',
    };
    $isOnlineMethod  = in_array($booking->payment_method, ['bank','bank_transfer','momo','vnpay','card','zalopay']);
    $isAlreadyPaid   = $payment && $payment->payment_status === 'completed';
@endphp

<div class="pmt-wrap">
<div class="pmt-inner">

{{-- ══════════ ALREADY PAID STATE ══════════ --}}
@if($isAlreadyPaid && !session('success'))
<div class="pmt-card">
    <div class="pmt-success-header" style="background:linear-gradient(135deg,#1e40af,#3b82f6)">
        <div class="pmt-check-circle">✓</div>
        <h2 class="pmt-success-title">Thanh toán đã hoàn tất!</h2>
        <p class="pmt-success-sub">Đặt phòng #{{ $booking->order_code }} đã được xác nhận.</p>
    </div>
    <div class="pmt-body">
        <div class="pmt-info-list">
            <div class="pmt-info-row">
                <span class="pmt-info-icon">🏨</span>
                <div><div class="pmt-info-lbl">Khách sạn · Phòng</div>
                    <div class="pmt-info-val">{{ $booking->room?->hotel?->name }} · {{ $booking->room?->room_name }}</div></div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">📅</span>
                <div><div class="pmt-info-lbl">Thời gian lưu trú</div>
                    <div class="pmt-info-val">
                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}
                        → {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                        <span class="pmt-nights-badge">{{ $nights }} {{ $unit }}</span>
                    </div>
                </div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">💳</span>
                <div><div class="pmt-info-lbl">Phương thức</div><div class="pmt-info-val">{{ $pmLabel }}</div></div>
            </div>
            @if($payment->transaction_no)
            <div class="pmt-info-row">
                <span class="pmt-info-icon">🔖</span>
                <div><div class="pmt-info-lbl">Mã giao dịch</div><div class="pmt-info-val">{{ $payment->transaction_no }}</div></div>
            </div>
            @endif
            @if($payment->paid_at)
            <div class="pmt-info-row">
                <span class="pmt-info-icon">🕐</span>
                <div><div class="pmt-info-lbl">Thời gian thanh toán</div><div class="pmt-info-val">{{ $payment->paid_at->format('d/m/Y H:i') }}</div></div>
            </div>
            @endif
        </div>
        <div class="pmt-price-box">
            <div class="pmt-price-total">
                <span>Tổng đã thanh toán</span>
                <span class="pmt-total-val">{{ number_format($booking->total_price) }}đ</span>
            </div>
        </div>
        <div class="pmt-actions" style="margin-top:20px">
            <a href="{{ route('booking.my') }}" class="pmt-btn pmt-btn-primary">Xem đặt phòng của tôi</a>
            <a href="{{ route('home') }}" class="pmt-btn pmt-btn-outline">Trang chủ</a>
        </div>
    </div>
</div>

{{-- ══════════ SUCCESS STATE ══════════ --}}
@elseif(session('success'))
<div class="pmt-card">

    {{-- Header --}}
    <div class="pmt-success-header">
        <div class="pmt-check-circle">✓</div>
        <h2 class="pmt-success-title">Đặt phòng thành công!</h2>
        <p class="pmt-success-sub">Yêu cầu đặt phòng của bạn đã được ghi nhận.</p>
    </div>

    <div class="pmt-body">
        {{-- Order code --}}
        <div class="pmt-order-box">
            <div class="pmt-order-label">Mã đơn hàng của bạn</div>
            <div class="pmt-order-code">{{ $booking->order_code }}</div>
            <button class="pmt-copy-btn"
                onclick="navigator.clipboard.writeText('{{ $booking->order_code }}').then(()=>this.textContent='✓ Đã sao chép')">
                📋 Sao chép mã
            </button>
        </div>

        {{-- Stay type badge --}}
        <div class="pmt-stay-badge {{ $isDay ? 'pmt-stay-day' : 'pmt-stay-night' }}">
            {{ $isDay ? '🌅 Chỗ ở Qua ngày' : '🌙 Chỗ ở Qua đêm' }}
        </div>

        {{-- Info rows --}}
        <div class="pmt-info-list">
            <div class="pmt-info-row">
                <span class="pmt-info-icon">👤</span>
                <div><div class="pmt-info-lbl">Họ và tên</div><div class="pmt-info-val">{{ $booking->full_name }}</div></div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">✉️</span>
                <div><div class="pmt-info-lbl">Email</div><div class="pmt-info-val">{{ $booking->email }}</div></div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">🏨</span>
                <div><div class="pmt-info-lbl">Khách sạn · Phòng</div>
                    <div class="pmt-info-val">{{ $booking->room?->hotel?->name }} · {{ $booking->room?->room_name }}</div></div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">📅</span>
                <div>
                    <div class="pmt-info-lbl">Thời gian lưu trú</div>
                    <div class="pmt-info-val">
                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}
                        → {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                        <span class="pmt-nights-badge">{{ $nights }} {{ $unit }}</span>
                    </div>
                </div>
            </div>
            <div class="pmt-info-row">
                <span class="pmt-info-icon">💳</span>
                <div><div class="pmt-info-lbl">Phương thức thanh toán</div><div class="pmt-info-val">{{ $pmLabel }}</div></div>
            </div>
        </div>

        {{-- Price breakdown --}}
        <div class="pmt-price-box">
            <div class="pmt-price-row">
                <span>{{ number_format($unitPrice) }}đ × {{ $nights }} {{ $unit }}</span>
                <span>{{ number_format($baseTotal) }}đ</span>
            </div>
            @if($hasDisc)
            <div class="pmt-price-row pmt-price-disc">
                <span>🎉 Giảm giá ({{ $booking->discount_code }})</span>
                <span>-{{ number_format($booking->discount_amount) }}đ</span>
            </div>
            @endif
            <div class="pmt-price-total">
                <span>Tổng thanh toán</span>
                <span class="pmt-total-val">{{ number_format($booking->total_price) }}đ</span>
            </div>
        </div>

        {{-- Status --}}
        @if($isOnlineMethod)
        <div class="pmt-status-box" style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af">
            🕐 Đơn hàng đang chờ xác nhận thanh toán · Hệ thống sẽ tự động cập nhật sau khi nhận được tiền
        </div>
        @else
        <div class="pmt-status-box">
            🕐 Đơn hàng đang chờ xác nhận · Vui lòng thanh toán khi nhận phòng
        </div>
        @endif

        {{-- Next steps --}}
        <div class="pmt-steps-box">
            <div class="pmt-steps-title">Các bước tiếp theo</div>
            @php
                $steps = match(true) {
                    in_array($booking->payment_method, ['bank','bank_transfer']) => [
                        'Kiểm tra email xác nhận gửi đến <strong>'.$booking->email.'</strong>',
                        'Chuyển khoản <strong>'.number_format($booking->total_price).'đ</strong> với nội dung <strong>SEVQR '.$booking->order_code.'</strong>',
                        'Hệ thống sẽ tự động xác nhận đặt phòng trong vài phút sau khi nhận tiền',
                    ],
                    $booking->payment_method === 'momo' => [
                        'Kiểm tra email xác nhận gửi đến <strong>'.$booking->email.'</strong>',
                        'Chuyển tiền <strong>'.number_format($booking->total_price).'đ</strong> vào ví MoMo với nội dung <strong>'.$booking->order_code.'</strong>',
                        'Đặt phòng sẽ được xác nhận sau khi chúng tôi nhận được tiền',
                    ],
                    default => [
                        'Kiểm tra email xác nhận gửi đến <strong>'.$booking->email.'</strong>',
                        'Đến khách sạn vào <strong>'.\Carbon\Carbon::parse($booking->check_in)->format('d/m/Y').'</strong>, xuất trình mã đơn hàng tại lễ tân',
                        'Thanh toán <strong>'.number_format($booking->total_price).'đ</strong> bằng tiền mặt hoặc thẻ ngân hàng',
                    ],
                };
            @endphp
            @foreach($steps as $i => $step)
            <div class="pmt-step-item">
                <span class="pmt-step-num">{{ $i+1 }}</span>
                <span class="pmt-step-text">{!! $step !!}</span>
            </div>
            @endforeach
        </div>

        {{-- Buttons --}}
        <div class="pmt-actions">
            <a href="{{ route('hotels.show', $booking->room->hotel) }}" class="pmt-btn pmt-btn-primary">← Về trang khách sạn</a>
            <a href="{{ route('home') }}" class="pmt-btn pmt-btn-outline">Trang chủ</a>
        </div>
    </div>
</div>

{{-- ══════════ PAYMENT FORM ══════════ --}}
@else
<div class="pmt-card">
    <div class="pmt-form-header">
        <h2 class="pmt-form-title">Xác nhận & Thanh toán</h2>
    </div>
    <div class="pmt-body">

        {{-- Stay type badge --}}
        <div class="pmt-stay-badge {{ $isDay ? 'pmt-stay-day' : 'pmt-stay-night' }}">
            {{ $isDay ? '🌅 Chỗ ở Qua ngày' : '🌙 Chỗ ở Qua đêm' }}
        </div>

        {{-- Booking summary --}}
        <div class="pmt-summary">
            <div class="pmt-summary-title">📋 Thông tin đặt phòng</div>
            <div class="pmt-summary-grid">
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Mã đặt phòng</span>
                    <span class="pmt-sum-val" style="font-weight:800;letter-spacing:1px;">{{ $booking->order_code }}</span>
                </div>
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Khách sạn</span>
                    <span class="pmt-sum-val">{{ $booking->room?->hotel?->name }}</span>
                </div>
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Phòng</span>
                    <span class="pmt-sum-val">{{ $booking->room?->room_name }}</span>
                </div>
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Nhận phòng</span>
                    <span class="pmt-sum-val">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</span>
                </div>
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Trả phòng</span>
                    <span class="pmt-sum-val">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</span>
                </div>
                <div class="pmt-sum-row">
                    <span class="pmt-sum-lbl">Số {{ $unit }}</span>
                    <span class="pmt-sum-val"><strong>{{ $nights }} {{ $unit }}</strong></span>
                </div>
            </div>

            {{-- Price breakdown --}}
            <div class="pmt-price-box">
                <div class="pmt-price-row">
                    <span>{{ number_format($unitPrice) }}đ × {{ $nights }} {{ $unit }}</span>
                    <span>{{ number_format($baseTotal) }}đ</span>
                </div>
                @if($hasDisc)
                <div class="pmt-price-row pmt-price-disc">
                    <span>🎉 Giảm giá ({{ $booking->discount_code }})</span>
                    <span>-{{ number_format($booking->discount_amount) }}đ</span>
                </div>
                @endif
                <div class="pmt-price-total">
                    <span>Tổng cộng</span>
                    <span class="pmt-total-val">{{ number_format($booking->total_price) }}đ</span>
                </div>
            </div>
        </div>

        {{-- Payment method instructions --}}
        @if($booking->payment_method === 'hotel')
        <div class="pmt-method-box pmt-method-hotel">
            <div class="pmt-method-title">🏨 Thanh toán tiền mặt tại khách sạn</div>
            @foreach([
                ['icon'=>'📋','title'=>'Xác nhận đặt phòng','desc'=>'Nhấn "Xác nhận" bên dưới. Đơn hàng sẽ ở trạng thái <em>chờ xác nhận</em>.'],
                ['icon'=>'📧','title'=>'Nhận email xác nhận','desc'=>'Email xác nhận kèm mã đơn hàng sẽ được gửi đến địa chỉ của bạn.'],
                ['icon'=>'💵','title'=>'Thanh toán khi nhận phòng','desc'=>'Xuất trình mã đơn hàng và thanh toán <strong>'.number_format($booking->total_price).'đ</strong> tại lễ tân.'],
            ] as $step)
            <div class="pmt-method-step">
                <span class="pmt-method-icon">{{ $step['icon'] }}</span>
                <div>
                    <div class="pmt-method-step-title">{{ $step['title'] }}</div>
                    <div class="pmt-method-step-desc">{!! $step['desc'] !!}</div>
                </div>
            </div>
            @endforeach
            <div class="pmt-method-warn">⚠️ Vui lòng đến nhận phòng đúng ngày đã đặt. Hủy trước ít nhất <strong>24 giờ</strong>.</div>
        </div>

        @elseif($booking->payment_method === 'bank' || $booking->payment_method === 'bank_transfer')
        @php
            $bankId      = 'ICB';           // Vietinbank — đổi nếu dùng ngân hàng khác
            $bankAccount = '107645394761';
            $bankName    = 'LE VAN HUY';
            $vietqrUrl   = "https://img.vietqr.io/image/{$bankId}-{$bankAccount}-compact2.png"
                         . "?amount={$booking->total_price}"
                         . "&addInfo=" . urlencode('SEVQR ' . $booking->order_code)
                         . "&accountName=" . urlencode($bankName);
        @endphp
        <div class="pmt-method-box pmt-method-bank" id="pmt-waiting-box">
            <div class="pmt-method-title">🏦 Chuyển khoản Vietinbank</div>
            <div class="pmt-qr-wrap">
                <img src="{{ $vietqrUrl }}" alt="QR Vietinbank" class="pmt-qr-img" onerror="this.style.display='none'">
            </div>
            <div class="pmt-bank-info">
                <div>🏦 <strong>Ngân hàng:</strong> Vietinbank</div>
                <div>💳 <strong>Số tài khoản:</strong> {{ $bankAccount }}</div>
                <div>👤 <strong>Chủ tài khoản:</strong> {{ $bankName }}</div>
                <div>💬 <strong>Nội dung CK:</strong> <span class="pmt-order-ref">SEVQR {{ $booking->order_code }}</span>
                    <button class="pmt-copy-inline" onclick="navigator.clipboard.writeText('SEVQR {{ $booking->order_code }}').then(()=>this.textContent='✓')">📋</button>
                </div>
                <div>💰 <strong>Số tiền:</strong> <span class="pmt-order-ref">{{ number_format($booking->total_price) }}đ</span></div>
            </div>
            <div class="pmt-polling-status" id="pmt-poll-status">
                <span class="pmt-poll-dot"></span> Đang chờ xác nhận thanh toán...
            </div>
        </div>

        @elseif($booking->payment_method === 'momo')
        @php
            $momoPhone  = '0373 848 395';
            $momoQrUrl  = asset('assets/images/qr_momo.jpg');
        @endphp
        <div class="pmt-method-box pmt-method-momo" id="pmt-waiting-box">
            <div class="pmt-method-title">
                <img src="{{ asset('assets/images/momo.png') }}" style="height:20px;vertical-align:middle;margin-right:6px;">Ví MoMo
            </div>
            <div class="pmt-qr-wrap">
                <img src="{{ $momoQrUrl }}" alt="QR MoMo" class="pmt-qr-img">
            </div>
            <p>Chuyển tiền đến số: <strong class="pmt-phone-num">{{ $momoPhone }}</strong></p>
            <p>Số tiền: <strong class="pmt-order-ref">{{ number_format($booking->total_price) }}đ</strong></p>
            <p>Nội dung: <strong class="pmt-order-ref">{{ $booking->order_code }}</strong>
                <button class="pmt-copy-inline" onclick="navigator.clipboard.writeText('{{ $booking->order_code }}').then(()=>this.textContent='✓')">📋</button>
            </p>
            <div class="pmt-polling-status" id="pmt-poll-status">
                <span class="pmt-poll-dot"></span> Đang chờ xác nhận thanh toán...
            </div>
        </div>

        @elseif($booking->payment_method === 'vnpay')
        <div class="pmt-method-box pmt-method-vnpay">
            <div class="pmt-method-title">🟢 VNPay</div>
            <p>Quét mã QR VNPay hoặc dùng app ngân hàng hỗ trợ VNPay.</p>
            <p>Số tiền: <strong class="pmt-order-ref">{{ number_format($booking->total_price) }}đ</strong></p>
            <p>Nội dung: <strong class="pmt-order-ref">{{ $booking->order_code }}</strong></p>
        </div>

        @elseif($booking->payment_method === 'card')
        <div class="pmt-method-box pmt-method-card">
            <div class="pmt-method-title">💳 Thẻ quốc tế (Visa / Mastercard / JCB)</div>
            <p>Thanh toán an toàn qua cổng quốc tế. Số tiền: <strong class="pmt-order-ref">{{ number_format($booking->total_price) }}đ</strong></p>
        </div>

        @elseif($booking->payment_method === 'zalopay')
        <div class="pmt-method-box pmt-method-bank">
            <div class="pmt-method-title">🔵 ZaloPay</div>
            <p>Mở app ZaloPay → Quét QR hoặc chuyển tiền.</p>
            <p>Số tiền: <strong class="pmt-order-ref">{{ number_format($booking->total_price) }}đ</strong></p>
            <p>Nội dung: <strong class="pmt-order-ref">{{ $booking->order_code }}</strong></p>
        </div>
        @endif

        {{-- Confirm button --}}
        <form method="POST" action="{{ route('payment.process', $booking) }}">
            @csrf
            @php
                $btnLabel = match($booking->payment_method) {
                    'vnpay'        => '🟢 Tiến hành thanh toán qua VNPay',
                    'momo'         => '💜 Xác nhận & chuyển khoản MoMo',
                    'bank','bank_transfer' => '🏦 Tôi đã chuyển khoản, xác nhận đặt phòng',
                    default        => '✅ Xác nhận đặt phòng',
                };
                $btnNote = match($booking->payment_method) {
                    'vnpay'  => 'Bạn sẽ được chuyển đến cổng thanh toán VNPay an toàn',
                    'momo'   => 'Hệ thống sẽ tự động xác nhận sau khi nhận tiền qua SePay',
                    'bank','bank_transfer' => 'Nhấn sau khi đã chuyển khoản. Hệ thống sẽ kiểm tra trong vài phút',
                    default  => 'Đặt phòng sẽ được xác nhận sau khi chúng tôi kiểm tra (trong 15 phút)',
                };
            @endphp
            <button type="submit" class="pmt-confirm-btn">{{ $btnLabel }}</button>
        </form>
        <p class="pmt-confirm-note">{{ $btnNote }}</p>
    </div>
</div>
@endif

</div>
</div>
@endsection

@push('styles')
<style>
.pmt-wrap  { min-height: 80vh; background: #f8fafc; padding: 32px 16px; }
.pmt-inner { max-width: 620px; margin: 0 auto; }
.pmt-card  { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,.09); overflow: hidden; }
.pmt-body  { padding: 24px; }

/* Success header */
.pmt-success-header { background: linear-gradient(135deg,#e91e8c,#ffa9f9); padding: 32px 24px; text-align: center; }
.pmt-check-circle   { width: 60px; height: 60px; background: rgba(255,255,255,.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 28px; color: #fff; }
.pmt-success-title  { color: #fff; margin: 0; font-size: 22px; font-weight: 700; }
.pmt-success-sub    { color: rgba(255,255,255,.85); margin: 6px 0 0; font-size: 14px; }

/* Form header */
.pmt-form-header { background: linear-gradient(135deg,#1e40af,#3b82f6); padding: 22px 24px; }
.pmt-form-title  { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }

/* Order box */
.pmt-order-box    { background: #f0fdf4; border: 1.5px dashed #86efac; border-radius: 12px; padding: 18px; text-align: center; margin-bottom: 16px; }
.pmt-order-label  { font-size: 11px; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.pmt-order-code   { font-size: 22px; font-weight: 800; color: #15803d; letter-spacing: 2px; }
.pmt-copy-btn     { margin-top: 10px; background: #fff; border: 1px solid #86efac; border-radius: 8px; padding: 6px 16px; font-size: 13px; cursor: pointer; color: #16a34a; transition: background .15s; }
.pmt-copy-btn:hover { background: #f0fdf4; }

/* Stay type badge */
.pmt-stay-badge    { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 7px 14px; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
.pmt-stay-night    { background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; }
.pmt-stay-day      { background: #fffbeb; border: 1.5px solid #fde68a; color: #92400e; }

/* Info list (success page) */
.pmt-info-list { margin-bottom: 16px; }
.pmt-info-row  { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.pmt-info-icon { font-size: 16px; width: 22px; text-align: center; flex-shrink: 0; }
.pmt-info-lbl  { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.pmt-info-val  { color: #1a202c; font-weight: 500; margin-top: 2px; font-size: 14px; }
.pmt-nights-badge { display: inline-block; background: #e0e7ff; color: #3730a3; font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 10px; margin-left: 6px; }

/* Summary (form page) */
.pmt-summary       { background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
.pmt-summary-title { font-weight: 700; font-size: 14px; margin-bottom: 12px; color: #1a202c; }
.pmt-summary-grid  { display: grid; gap: 0; margin-bottom: 0; }
.pmt-sum-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; }
.pmt-sum-row:last-child { border-bottom: none; }
.pmt-sum-lbl { color: #64748b; }
.pmt-sum-val { color: #1a202c; font-weight: 500; text-align: right; }

/* Price breakdown */
.pmt-price-box   { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-top: 12px; }
.pmt-price-row   { display: flex; justify-content: space-between; font-size: 13.5px; color: #374151; padding: 4px 0; }
.pmt-price-disc  { color: #7c3aed; font-weight: 600; }
.pmt-price-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; color: #1a202c; border-top: 1.5px solid #e2e8f0; margin-top: 8px; padding-top: 10px; }
.pmt-total-val   { font-size: 20px; font-weight: 800; color: #e91e8c; }

/* Status box */
.pmt-status-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #92400e; margin-bottom: 16px; }

/* Steps */
.pmt-steps-box    { background: #f8fafc; border-radius: 10px; padding: 16px; margin-bottom: 20px; }
.pmt-steps-title  { font-weight: 700; font-size: 14px; margin-bottom: 12px; color: #1a202c; }
.pmt-step-item    { display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-start; }
.pmt-step-item:last-child { margin-bottom: 0; }
.pmt-step-num     { background: linear-gradient(135deg,#e91e8c,#ffa9f9); color: #fff; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
.pmt-step-text    { color: #4a5568; line-height: 1.5; font-size: 13.5px; }

/* Method boxes */
.pmt-method-box        { border-radius: 12px; padding: 18px; margin-bottom: 16px; font-size: 13.5px; line-height: 1.7; }
.pmt-method-hotel      { background: #f0fdf4; border: 1px solid #bbf7d0; }
.pmt-method-bank       { background: #eff6ff; border: 1px solid #bfdbfe; }
.pmt-method-momo       { background: #fff0f6; border: 1px solid #fbcfe8; text-align: center; }
.pmt-method-vnpay      { background: #f0fdf4; border: 1px solid #bbf7d0; text-align: center; }
.pmt-method-card       { background: #fef3c7; border: 1px solid #fde68a; }
.pmt-method-title      { font-weight: 700; font-size: 14px; margin-bottom: 12px; }
.pmt-method-hotel .pmt-method-title { color: #15803d; }
.pmt-method-bank  .pmt-method-title { color: #1e40af; }
.pmt-method-momo  .pmt-method-title { color: #be185d; }
.pmt-method-vnpay .pmt-method-title { color: #15803d; }
.pmt-method-card  .pmt-method-title { color: #92400e; }
.pmt-method-step  { display: flex; gap: 12px; margin-bottom: 12px; }
.pmt-method-icon  { font-size: 20px; flex-shrink: 0; width: 28px; text-align: center; }
.pmt-method-step-title { font-weight: 600; font-size: 13px; color: #1a202c; }
.pmt-method-step-desc  { font-size: 13px; color: #4b5563; margin-top: 2px; }
.pmt-method-warn  { background: #fefce8; border-left: 3px solid #f59e0b; border-radius: 6px; padding: 10px 14px; font-size: 12px; color: #92400e; margin-top: 8px; }
.pmt-method-note  { font-size: 12px; color: #6b7280; margin: 10px 0 0; }
.pmt-bank-info    { background: #fff; border-radius: 10px; padding: 14px; font-size: 13.5px; line-height: 2.2; }
.pmt-order-ref    { color: #e91e8c; font-weight: 700; }
.pmt-phone-num    { font-size: 18px; color: #be185d; }

/* Confirm button */
.pmt-confirm-btn  { width: 100%; background: #10b981; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .2s; }
.pmt-confirm-btn:hover { background: #059669; }
.pmt-confirm-note { text-align: center; margin-top: 12px; font-size: 12px; color: #94a3b8; }

/* Action buttons */
.pmt-actions    { display: flex; gap: 10px; }
.pmt-btn        { flex: 1; border-radius: 10px; padding: 13px; font-size: 14px; font-weight: 700; cursor: pointer; text-align: center; text-decoration: none; display: block; transition: opacity .15s; }
.pmt-btn:hover  { opacity: .85; }
.pmt-btn-primary { background: linear-gradient(135deg,#e91e8c,#ffa9f9); color: #fff; }
.pmt-btn-outline { background: #fff; border: 1.5px solid #e2e8f0; color: #374151; }

/* QR */
.pmt-qr-wrap  { text-align: center; margin: 12px 0; }
.pmt-qr-img   { width: 180px; height: 180px; border-radius: 12px; border: 2px solid #e2e8f0; }

/* Copy inline */
.pmt-copy-inline { background: none; border: 1px solid #cbd5e1; border-radius: 5px; padding: 1px 7px; cursor: pointer; font-size: 12px; margin-left: 6px; vertical-align: middle; }
.pmt-copy-inline:hover { background: #f1f5f9; }

/* Polling status */
.pmt-polling-status { display: flex; align-items: center; gap: 8px; margin-top: 14px; font-size: 12.5px; color: #64748b; justify-content: center; }
.pmt-poll-dot { width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; animation: pmt-blink 1.2s ease-in-out infinite; flex-shrink: 0; }
@keyframes pmt-blink { 0%,100%{opacity:1} 50%{opacity:.2} }

/* Success overlay */
.pmt-success-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 9999; align-items: center; justify-content: center; }
.pmt-success-overlay.active { display: flex; }
.pmt-success-popup { background: #fff; border-radius: 20px; padding: 36px 28px; text-align: center; max-width: 340px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: pmt-pop .4s cubic-bezier(.34,1.56,.64,1); }
@keyframes pmt-pop { from{transform:scale(.7);opacity:0} to{transform:scale(1);opacity:1} }
.pmt-success-icon { font-size: 56px; margin-bottom: 12px; }
.pmt-success-popup h3 { font-size: 20px; font-weight: 800; color: #15803d; margin: 0 0 8px; }
.pmt-success-popup p  { color: #4b5563; font-size: 14px; margin: 0 0 20px; }
.pmt-success-popup .pmt-btn-primary { display: inline-block; padding: 12px 28px; text-decoration: none; border-radius: 10px; }
</style>
@endpush

@push('scripts')
@php
    $needsPolling = in_array($booking->payment_method, ['bank','bank_transfer','momo'])
                 && (!$payment || $payment->payment_status !== 'completed');
@endphp
@if($needsPolling)
{{-- Success overlay --}}
<div class="pmt-success-overlay" id="pmt-overlay">
    <div class="pmt-success-popup">
        <div class="pmt-success-icon">✅</div>
        <h3>Thanh toán thành công!</h3>
        <p>Chúng tôi đã nhận được tiền.<br>Đặt phòng <strong>{{ $booking->order_code }}</strong> đã được xác nhận.</p>
        <a href="{{ route('booking.my') }}" class="pmt-btn pmt-btn-primary">Xem đơn đặt phòng</a>
    </div>
</div>

<script>
(function () {
    const statusUrl = '{{ route('payment.status', $booking) }}';
    const overlay   = document.getElementById('pmt-overlay');
    const pollEl    = document.getElementById('pmt-poll-status');
    let done = false;

    function showSuccess() {
        done = true;
        if (overlay) overlay.classList.add('active');
        if (pollEl) {
            pollEl.innerHTML = '<span style="color:#16a34a;font-weight:700">✓ Đã nhận thanh toán!</span>';
        }
    }

    function poll() {
        if (done) return;
        fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.payment_status === 'completed' || data.booking_status === 'confirmed') {
                    showSuccess();
                }
            })
            .catch(() => {});
    }

    // Poll mỗi 4 giây
    poll();
    setInterval(poll, 4000);
})();
</script>
@endif
@endpush
