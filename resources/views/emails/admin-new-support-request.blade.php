<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Yêu cầu hỗ trợ mới - StayGo Admin</title>
<style>
body{margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:560px;margin:24px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#1B3A6B;padding:20px 28px;}
.hdr h1{margin:0;color:#fff;font-size:18px;font-weight:700;}
.badge{display:inline-block;background:#ef4444;color:#fff;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;margin-top:8px;}
.body{padding:24px 28px;}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;}
.row:last-child{border-bottom:none;}
.lbl{color:#718096;font-size:13px;}
.val{font-weight:600;color:#1a202c;}
.note-box{background:#f8fafc;border-left:3px solid #1B3A6B;border-radius:4px;padding:12px 16px;margin-top:16px;font-size:14px;color:#374151;line-height:1.6;}
.btn{display:inline-block;background:#1B3A6B;color:#fff;text-decoration:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;margin-top:20px;}
.footer{background:#f8fafc;padding:14px 28px;text-align:center;border-top:1px solid #e2e8f0;}
.footer p{margin:0;font-size:11px;color:#94a3b8;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🆘 Yêu cầu hỗ trợ mới</h1>
    <div><span class="badge">Chờ xử lý</span></div>
  </div>
  <div class="body">
    <div class="row"><span class="lbl">Khách hàng</span><span class="val">{{ $supportRequest->full_name }}</span></div>
    <div class="row"><span class="lbl">Điện thoại</span><span class="val">{{ $supportRequest->phone }}</span></div>
    <div class="row"><span class="lbl">Email</span><span class="val">{{ $supportRequest->email ?? '(không có)' }}</span></div>
    <div class="row"><span class="lbl">Chủ đề</span><span class="val">{{ $supportRequest->subject ?? '(không có)' }}</span></div>
    <div class="row"><span class="lbl">Thời điểm gửi</span><span class="val">{{ now()->format('H:i d/m/Y') }}</span></div>
    @if($supportRequest->note)
    <p style="margin:16px 0 4px;font-size:13px;color:#718096;">Nội dung:</p>
    <div class="note-box">{{ $supportRequest->note }}</div>
    @endif
    <a href="{{ config('app.url') }}/admin/support-requests" class="btn">Xem & xử lý ngay</a>
  </div>
  <div class="footer">
    <p>StayGo Admin — Email tự động, không reply</p>
  </div>
</div>
</body>
</html>
