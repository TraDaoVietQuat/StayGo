<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chatbot Escalation - StayGo Admin</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#1B3A6B;padding:20px 28px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.hdr p{margin:4px 0 0;color:#93c5fd;font-size:13px;}
.body{padding:24px 28px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;min-width:120px;}
.val{font-weight:600;color:#1a202c;text-align:right;}
.msg-box{background:#fef9c3;border-left:4px solid #f59e0b;border-radius:6px;padding:14px 16px;margin:16px 0;font-size:14px;color:#78350f;line-height:1.6;}
.btn{display:inline-block;background:#1B3A6B;color:#fff;text-decoration:none;padding:10px 22px;border-radius:8px;font-weight:700;font-size:14px;margin-top:16px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>💬 Chatbot cần hỗ trợ thủ công</h1>
    <p>Khách đặt câu hỏi mà AI không trả lời được — cần admin tiếp tục</p>
  </div>
  <div class="body">
    <div class="row"><span class="lbl">Ticket #</span><span class="val" style="color:#1B3A6B;">{{ $ticket->id }}</span></div>
    <div class="row"><span class="lbl">Khách hàng</span><span class="val">{{ $ticket->full_name }}</span></div>
    @if($ticket->email)
    <div class="row"><span class="lbl">Email</span><span class="val">{{ $ticket->email }}</span></div>
    @endif
    @if($ticket->user_id)
    <div class="row"><span class="lbl">Tài khoản</span><span class="val">Đã đăng nhập (ID {{ $ticket->user_id }})</span></div>
    @else
    <div class="row"><span class="lbl">Tài khoản</span><span class="val">Khách vãng lai</span></div>
    @endif
    <div class="row"><span class="lbl">Thời gian</span><span class="val">{{ now()->format('d/m/Y H:i') }}</span></div>

    <p style="margin:16px 0 6px;font-size:13px;color:#718096;font-weight:600;">Câu hỏi của khách:</p>
    <div class="msg-box">{{ $userMessage }}</div>

    <a href="{{ url('/admin/support-requests/' . $ticket->id) }}" class="btn">Xem & Trả lời trong Admin</a>
  </div>
  <div class="footer">
    <p>StayGo — Hệ thống thông báo tự động. Vui lòng không trả lời email này.</p>
  </div>
</div>
</body>
</html>
