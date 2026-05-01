@extends('layouts.app')
@section('title', 'Liên hệ & Hỗ trợ')

@section('content')
<div class="container" style="padding:32px 16px;max-width:800px;">
    <h2 style="font-family:'Playfair Display',serif;margin-bottom:8px;">Liên hệ & Hỗ trợ</h2>
    <p style="color:#666;font-size:14px;margin-bottom:28px;">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>

    @if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;padding:14px 18px;margin-bottom:20px;color:#065f46;font-size:14px;">
        ✅ {{ session('success') }}
    </div>
    @endif

    <div class="contact-layout" style="display:flex;gap:28px;align-items:flex-start;">

        {{-- Contact form --}}
        <div style="flex:1;">
            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                <h3 style="margin:0 0 20px;font-size:16px;">Gửi yêu cầu hỗ trợ</h3>

                @if($errors->any())
                <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:12px;margin-bottom:16px;">
                    <ul style="margin:0;padding-left:16px;font-size:13px;color:#856404;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('support.store') }}">
                    @csrf
                    {{-- Honeypot: ẩn với người dùng, bot sẽ điền vào --}}
                    <div style="display:none;" aria-hidden="true">
                        <input type="text" name="_hp_name" tabindex="-1" autocomplete="off" value="">
                        <input type="email" name="_hp_email" tabindex="-1" autocomplete="off" value="">
                    </div>
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Họ tên *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', auth()->user()?->full_name) }}" required
                                style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Số điện thoại *</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required
                                style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Chủ đề</label>
                            <select name="subject"
                                style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                                <option value="">Chọn chủ đề...</option>
                                <option value="Hỗ trợ đặt phòng" {{ old('subject') === 'Hỗ trợ đặt phòng' ? 'selected' : '' }}>Hỗ trợ đặt phòng</option>
                                <option value="Thanh toán" {{ old('subject') === 'Thanh toán' ? 'selected' : '' }}>Thanh toán</option>
                                <option value="Hủy phòng & hoàn tiền" {{ old('subject') === 'Hủy phòng & hoàn tiền' ? 'selected' : '' }}>Hủy phòng & hoàn tiền</option>
                                <option value="Khiếu nại" {{ old('subject') === 'Khiếu nại' ? 'selected' : '' }}>Khiếu nại</option>
                                <option value="Khác" {{ old('subject') === 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Nội dung</label>
                            <textarea name="note" rows="5"
                                style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;resize:vertical;"
                                placeholder="Mô tả vấn đề của bạn...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" style="margin-top:20px;width:100%;background:#1e73be;color:#fff;border:none;border-radius:8px;padding:13px;font-size:15px;font-weight:700;cursor:pointer;">
                        📨 Gửi yêu cầu
                    </button>
                </form>
            </div>
        </div>

        {{-- Contact info --}}
        <aside class="contact-aside" style="width:240px;flex-shrink:0;">
            <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-bottom:16px;">
                <h3 style="margin:0 0 16px;font-size:15px;">Thông tin liên hệ</h3>
                <div style="display:flex;flex-direction:column;gap:14px;font-size:14px;color:#555;">
                    <div>
                        <div style="font-weight:600;margin-bottom:3px;">📞 Hotline</div>
                        <div style="color:#1e73be;">1800 1234</div>
                        <div style="font-size:12px;color:#aaa;">(Miễn phí, 8:00 - 22:00)</div>
                    </div>
                    <div>
                        <div style="font-weight:600;margin-bottom:3px;">📧 Email</div>
                        <div>support@staygo.vn</div>
                    </div>
                    <div>
                        <div style="font-weight:600;margin-bottom:3px;">🕐 Giờ làm việc</div>
                        <div>T2-T7: 8:00 - 21:00</div>
                        <div>CN: 9:00 - 18:00</div>
                    </div>
                </div>
            </div>

            <div style="background:#f0f6ff;border-radius:12px;padding:20px;">
                <div style="font-weight:700;font-size:14px;margin-bottom:8px;">💬 Chat trực tiếp</div>
                <p style="font-size:13px;color:#555;margin:0 0 12px;">Dùng chatbot ở góc phải màn hình để được hỗ trợ ngay!</p>
            </div>
        </aside>
    </div>
</div>
@endsection
