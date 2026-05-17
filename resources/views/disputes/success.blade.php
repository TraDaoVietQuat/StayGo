@extends('layouts.app')

@section('title', 'Khiếu nại đã gửi — StayGo')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 max-w-md w-full text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Khiếu nại đã được tiếp nhận!</h1>
        <p class="text-gray-500 text-sm mb-6">
            Đội ngũ xử lý tranh chấp của StayGo đã nhận được thông tin.<br>
            Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.
        </p>

        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800 text-left mb-6 space-y-2">
            <p class="font-semibold">⏱️ Thời hạn xử lý</p>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Trường hợp thường: trong vòng <strong>24 giờ</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                <span>Overbooking / Hành vi không chuyên nghiệp: <strong>4 giờ</strong></span>
            </div>
        </div>

        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 text-left mb-6">
            <p class="font-semibold mb-1">Các bước tiếp theo:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Chuyên viên xem xét hồ sơ khiếu nại</li>
                <li>Chúng tôi liên hệ khách sạn để thu thập phản hồi</li>
                <li>Phán quyết được đưa ra dựa trên bằng chứng và chính sách</li>
                <li>Bạn nhận thông báo kết quả qua email</li>
            </ol>
        </div>

        <div class="flex flex-col gap-3">
            @auth
                <a href="{{ route('booking.my') }}"
                   class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 text-sm transition-colors text-center">
                    Xem lịch sử đặt phòng
                </a>
            @endauth
            <a href="{{ route('home') }}"
               class="rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-2.5 text-sm transition-colors text-center">
                Về trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
