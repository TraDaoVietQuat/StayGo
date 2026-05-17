<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===== StayGo Scheduled Jobs =====
Schedule::command('admin:precompute-stats')->everyThirtyMinutes();      // pre-compute admin dashboard widgets vào cache
Schedule::command('staygo:complete-stays')->dailyAt('00:05');           // 00:05 — auto complete đơn đã checkout
Schedule::command('staygo:process-refunds')->dailyAt('08:00');          // 08:00 — xử lý refund tự động
// E-02: Chuỗi 3 email nhắc nhở check-in (7 ngày trước + sáng check-in + 1 ngày trước)
Schedule::command('staygo:pre-arrival-reminders')->dailyAt('08:00');    // 08:00 — wave morning (check-in today) + wave 7day + wave 1day
Schedule::command('staygo:checkout-reminders')->dailyAt('08:30');       // 08:30 — nhắc nhở trả phòng hôm nay
// E-04: Khảo sát sau checkout + reminder 3 ngày
Schedule::command('staygo:post-stay-surveys')->dailyAt('10:00');        // 10:00 — gửi khảo sát + reminder nếu chưa đánh giá
Schedule::command('staygo:expire-pending-bookings')->everyThirtyMinutes(); // huỷ đơn pending quá 30 phút chưa thanh toán
