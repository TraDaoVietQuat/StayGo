<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành công — StayGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-lg p-10 max-w-md w-full text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Đăng ký thành công!</h1>
    <p class="text-gray-600 text-sm mb-6">
        Đơn đăng ký đối tác của bạn đã được ghi nhận.<br>
        Đội ngũ StayGo sẽ xem xét và liên hệ với bạn trong vòng <strong>1-3 ngày làm việc</strong>.
    </p>
    <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700 text-left mb-6">
        <p class="font-semibold mb-1">Các bước tiếp theo:</p>
        <ol class="list-decimal list-inside space-y-1">
            <li>Admin StayGo xem xét hồ sơ của bạn</li>
            <li>Bạn nhận email thông báo kết quả duyệt</li>
            <li>Sau khi duyệt, đăng nhập tại <strong>/partner</strong> để quản lý khách sạn</li>
        </ol>
    </div>
    <a href="{{ route('home') }}"
       class="inline-block rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 text-sm transition-colors">
        Về trang chủ
    </a>
</div>
</body>
</html>
