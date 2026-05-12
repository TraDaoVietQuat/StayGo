@extends('layouts.app')
@section('title', 'Ticket hỗ trợ #' . $supportRequest->id)

@section('content')
<div class="st-wrap">
    <div class="st-header">
        <a href="{{ route('profile.show') }}" class="st-back">← Tài khoản</a>
        <h2 class="st-title">Ticket #{{ $supportRequest->id }}</h2>
        @php
            $badgeMap = ['pending' => ['color' => '#f59e0b', 'label' => 'Chờ xử lý'], 'in_progress' => ['color' => '#3b82f6', 'label' => 'Đang xử lý'], 'resolved' => ['color' => '#22c55e', 'label' => 'Đã giải quyết'], 'closed' => ['color' => '#94a3b8', 'label' => 'Đã đóng']];
            $badge = $badgeMap[$supportRequest->status] ?? ['color' => '#94a3b8', 'label' => ucfirst($supportRequest->status)];
        @endphp
        <span class="st-status-badge" style="background:{{ $badge['color'] }}20;color:{{ $badge['color'] }};border:1px solid {{ $badge['color'] }}40;">{{ $badge['label'] }}</span>
    </div>

    {{-- Thông tin ticket --}}
    <div class="st-card st-ticket-info">
        <div class="st-info-row"><span class="st-lbl">Tiêu đề</span><span class="st-val">{{ $supportRequest->subject ?: '(không có tiêu đề)' }}</span></div>
        <div class="st-info-row"><span class="st-lbl">Ngày gửi</span><span class="st-val">{{ $supportRequest->created_at?->format('d/m/Y H:i') ?? '—' }}</span></div>
        <div class="st-info-row st-info-row-full"><span class="st-lbl">Nội dung ban đầu</span><p class="st-val" style="white-space:pre-wrap;">{{ $supportRequest->note }}</p></div>
        @if($supportRequest->admin_note)
        <div class="st-info-row st-info-row-full" style="background:#fdf2f8;border-radius:8px;padding:10px 14px;">
            <span class="st-lbl" style="color:#0066cc;">📌 Ghi chú từ Admin</span>
            <p class="st-val" style="white-space:pre-wrap;">{{ $supportRequest->admin_note }}</p>
        </div>
        @endif
    </div>

    {{-- Thread replies --}}
    <div class="st-thread">
        <div class="st-thread-title">💬 Cuộc trò chuyện</div>

        @forelse($supportRequest->replies as $reply)
        <div class="st-msg {{ $reply->is_admin ? 'st-msg-admin' : 'st-msg-user' }}">
            <div class="st-msg-meta">
                @if($reply->is_admin)
                    <span class="st-msg-avatar st-avatar-admin">A</span>
                    <span class="st-msg-name">StayGo Support</span>
                @else
                    <span class="st-msg-avatar st-avatar-user">{{ strtoupper(substr($reply->user?->full_name ?? 'U', 0, 1)) }}</span>
                    <span class="st-msg-name">{{ $reply->user?->full_name ?? 'Bạn' }}</span>
                @endif
                <span class="st-msg-time">{{ $reply->created_at?->format('d/m/Y H:i') }}</span>
            </div>
            <div class="st-msg-body">{{ $reply->message }}</div>
        </div>
        @empty
        <div style="text-align:center;color:#94a3b8;padding:24px;font-size:14px;">Chưa có phản hồi. Chúng tôi sẽ sớm liên hệ với bạn!</div>
        @endforelse
    </div>

    {{-- Reply form --}}
    @if($supportRequest->status !== 'closed')
    <div class="st-card st-reply-form">
        <div class="st-thread-title" style="margin-bottom:14px;">✏️ Gửi thêm thông tin</div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#166534;">
            ✅ {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#856404;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('support.reply', $supportRequest) }}">
            @csrf
            <div class="st-field">
                <textarea name="message" rows="4" placeholder="Nhập nội dung bổ sung hoặc phản hồi..." required>{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="st-submit-btn">Gửi phản hồi →</button>
        </form>
    </div>
    @else
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;text-align:center;color:#94a3b8;font-size:14px;">
        🔒 Ticket này đã đóng. Vui lòng tạo ticket mới nếu cần hỗ trợ thêm.
    </div>
    @endif
</div>

@push('styles')
<style>
.st-wrap { max-width: 720px; margin: 0 auto; padding: 32px 16px 60px; }
.st-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
.st-back { color: #0066cc; font-size: 14px; text-decoration: none; font-weight: 500; }
.st-back:hover { text-decoration: underline; }
.st-title { font-family: 'Inter', 'Be Vietnam Pro', sans-serif; font-size: 22px; color: #1a202c; margin: 0; flex: 1; }
.st-status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }

.st-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; }
.st-ticket-info { }
.st-info-row { display: flex; align-items: flex-start; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.st-info-row:last-child { border-bottom: none; }
.st-info-row-full { flex-direction: column; gap: 6px; }
.st-lbl { color: #718096; min-width: 120px; font-size: 13px; }
.st-val { color: #1a202c; font-weight: 500; flex: 1; margin: 0; }

.st-thread { margin-bottom: 20px; }
.st-thread-title { font-weight: 700; font-size: 15px; color: #1a202c; margin-bottom: 16px; }

.st-msg { padding: 14px 18px; border-radius: 12px; margin-bottom: 12px; }
.st-msg-admin { background: linear-gradient(135deg, #fdf2f8, #fff); border: 1px solid #0066cc30; }
.st-msg-user { background: #f8fafc; border: 1px solid #e2e8f0; }
.st-msg-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.st-msg-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; }
.st-avatar-admin { background: #0066cc; color: #fff; }
.st-avatar-user { background: #e2e8f0; color: #374151; }
.st-msg-name { font-weight: 600; font-size: 14px; color: #1a202c; flex: 1; }
.st-msg-time { font-size: 12px; color: #94a3b8; }
.st-msg-body { font-size: 14px; color: #374151; white-space: pre-wrap; line-height: 1.6; }

.st-reply-form { }
.st-field textarea { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; box-sizing: border-box; }
.st-field textarea:focus { outline: none; border-color: #0066cc; box-shadow: 0 0 0 3px #0066cc20; }
.st-submit-btn { margin-top: 12px; background: linear-gradient(135deg, #0066cc, #c2185b); color: #fff; border: none; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: pointer; }
.st-submit-btn:hover { opacity: .9; }

@media (max-width: 480px) {
    .st-info-row { flex-direction: column; gap: 4px; }
    .st-lbl { min-width: unset; }
}
</style>
@endpush
@endsection
