<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Phản hồi yêu cầu hỗ trợ - StayGo</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#1B3A6B;padding:20px 28px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.body{padding:24px 28px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;}
.val{font-weight:600;color:#1a202c;}
.note-box{background:#f8fafc;border-left:3px solid #718096;border-radius:4px;padding:12px 16px;margin:8px 0 16px;font-size:14px;color:#374151;line-height:1.6;}
.reply-box{background:#eff6ff;border-left:3px solid #1B3A6B;border-radius:4px;padding:12px 16px;margin:8px 0 16px;font-size:14px;color:#1e3a5f;line-height:1.6;}
.resolved-badge{display:inline-block;background:#059669;color:#fff;border-radius:6px;padding:4px 12px;font-size:13px;font-weight:700;margin-bottom:16px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    @if($isResolved)
    <h1>✅ Yêu cầu của bạn đã được giải quyết</h1>
    @else
    <h1>💬 Admin đã phản hồi yêu cầu của bạn</h1>
    @endif
  </div>
  <div class="body">
    <p style="margin:0 0 16px;font-size:15px;color:#374151;">Xin chào <strong>{{ $supportRequest->full_name }}</strong>,</p>

    @if($isResolved)
    <span class="resolved-badge">Đã giải quyết</span>
    <p style="font-size:14px;color:#374151;">Yêu cầu hỗ trợ của bạn đã được đội ngũ StayGo xử lý và đánh dấu hoàn thành.</p>
    @else
    <p style="font-size:14px;color:#374151;">Đội ngũ hỗ trợ StayGo đã phản hồi yêu cầu của bạn.</p>
    @endif

    <div class="row"><span class="lbl">Chủ đề</span><span class="val">{{ $supportRequest->subject ?? '(không có)' }}</span></div>

    @if($supportRequest->note)
    <p style="margin:16px 0 4px;font-size:13px;color:#718096;">Nội dung bạn đã gửi:</p>
    <div class="note-box">{{ $supportRequest->note }}</div>
    @endif

    @if($adminReply)
    <p style="margin:16px 0 4px;font-size:13px;color:#718096;">Phản hồi từ StayGo:</p>
    <div class="reply-box">{{ $adminReply }}</div>
    @endif

    <p style="font-size:13px;color:#718096;margin-top:16px;">Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ lại qua trang <a href="{{ config('app.url') }}/contact" style="color:#1B3A6B;">liên hệ</a> hoặc email <a href="mailto:supportstaygo@gmail.com" style="color:#1B3A6B;">supportstaygo@gmail.com</a>.</p>
  </div>
  <div class="footer">
    <p>StayGo — Email tự động, không reply trực tiếp vào email này</p>
  </div>
</div>
</body>
</html>
