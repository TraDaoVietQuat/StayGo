@extends('layouts.app')
@section('title', 'Lịch sử đặt phòng')

@section('content')
<div class="up-wrap">
    <div class="up-layout">
        @include('components.user-sidebar')

        <div class="up-main">
            @if(session('success'))
            <div class="up-alert up-alert-success">✅ {{ session('success') }}</div>
            @endif

            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="#1e73be" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="#1e73be" stroke-width="2" stroke-linecap="round"/></svg>
                    Lịch sử đặt phòng
                </div>

                @forelse($bookings as $booking)
                @php
                    $stMap = [
                        'pending'   => ['Chờ xác nhận', 'up-st-pending'],
                        'confirmed' => ['Đã xác nhận',  'up-st-confirmed'],
                        'completed' => ['Hoàn thành',   'up-st-completed'],
                        'cancelled' => ['Đã hủy',       'up-st-cancelled'],
                    ];
                    [$stLabel, $stClass] = $stMap[$booking->status] ?? ['Unknown', 'up-st-pending'];
                    $pmMap = ['bank'=>'Chuyển khoản','momo'=>'MoMo','vnpay'=>'VNPay','card'=>'Thẻ quốc tế','hotel'=>'Tại khách sạn'];
                @endphp
                <div class="up-booking-row">
                    <div class="up-bk-left">
                        <div class="up-bk-hotel-img">
                            @if($booking->room?->hotel?->image)
                            <img src="{{ $booking->room->hotel->image_url }}" alt="">
                            @else
                            <div class="up-bk-no-img">🏨</div>
                            @endif
                        </div>
                        <div class="up-bk-info">
                            <div class="up-bk-code">{{ $booking->order_code }}</div>
                            <div class="up-bk-hotel">{{ $booking->room?->hotel?->name ?? 'N/A' }}</div>
                            <div class="up-bk-room">🛏 {{ $booking->room?->room_name }}</div>
                            <div class="up-bk-dates">
                                📅 {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}
                                → {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                                &nbsp;·&nbsp;
                                {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} đêm
                            </div>
                            <div class="up-bk-pm">💳 {{ $pmMap[$booking->payment_method] ?? $booking->payment_method }}</div>
                        </div>
                    </div>
                    <div class="up-bk-right">
                        <div class="up-bk-price">{{ number_format($booking->total_price) }}đ</div>
                        <span class="up-status-badge {{ $stClass }}">{{ $stLabel }}</span>
                        <div class="up-bk-actions">
                            @if(in_array($booking->status, ['pending','confirmed']))
                                @if($booking->status === 'pending' && !$booking->payment)
                                <a href="{{ route('payment.show', $booking) }}" class="up-btn-sm up-btn-blue">
                                    Thanh toán
                                </a>
                                @endif
                                <form method="POST" action="{{ route('booking.cancel', $booking) }}"
                                    onsubmit="return confirm('Bạn có chắc muốn hủy?')">
                                    @csrf
                                    <button type="submit" class="up-btn-sm up-btn-red">Hủy</button>
                                </form>
                            @endif
                            @if($booking->status === 'completed' && !$booking->review)
                            <a href="{{ route('hotels.show', $booking->room?->hotel_id) }}#review-form"
                                class="up-btn-sm up-btn-outline">✍️ Đánh giá</a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="up-empty">
                    <div class="up-empty-icon">📋</div>
                    <p>Bạn chưa có đặt phòng nào.</p>
                    <a href="{{ route('hotels.index') }}" class="up-btn up-btn-primary" style="display:inline-block;text-decoration:none;">
                        Tìm khách sạn ngay
                    </a>
                </div>
                @endforelse
            </div>

            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
