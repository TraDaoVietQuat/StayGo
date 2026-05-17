<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký đối tác — StayGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-2xl">
    {{-- Logo & header --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="text-3xl font-bold text-blue-600">Stay<span class="text-indigo-500">Go</span></a>
        <p class="text-gray-600 mt-2">Đăng ký trở thành đối tác khách sạn</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Thông tin đăng ký đối tác</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('partner.register.submit') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email đăng nhập *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <hr class="border-gray-200">
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin khách sạn / doanh nghiệp</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên khách sạn / resort *</label>
                    <input type="text" name="hotel_name" value="{{ old('hotel_name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="VD: The Imperial Vũng Tàu">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên công ty / doanh nghiệp *</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế</label>
                    <input type="text" name="tax_code" value="{{ old('tax_code') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-start gap-3">
                <input type="checkbox" id="agree" required class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600">
                <label for="agree" class="text-sm text-gray-600">
                    Tôi đồng ý với <a href="#" class="text-blue-600 underline">Điều khoản đối tác</a> và
                    <a href="#" class="text-blue-600 underline">Chính sách bảo mật</a> của StayGo.
                </label>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 text-sm transition-colors">
                Gửi đơn đăng ký
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-gray-500">
            Đã có tài khoản?
            <a href="{{ url('/partner/login') }}" class="text-blue-600 underline">Đăng nhập tại đây</a>
        </p>
    </div>
</div>
</body>
</html>
