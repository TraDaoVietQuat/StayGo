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
// E-10A: Daily digest gửi cho admin team mỗi sáng
Schedule::command('staygo:send-admin-daily-digest')->dailyAt('07:00');      // 07:00 — admin daily digest
// E-07: Gửi email bảng kê payout cho partner khi payout được đánh dấu paid hôm nay
Schedule::command('staygo:send-payout-emails')->dailyAt('09:00');           // 09:00 — gửi payout statement email
// E-08: Kiểm tra KPI và gửi cảnh báo yellow/red cho partner
Schedule::command('staygo:check-partner-kpi-alerts')->dailyAt('09:30');     // 09:30 — KPI alert emails
// Blog newsletter: gửi digest bài viết mới trong tuần cho subscribers mỗi thứ Hai 9:00 sáng
Schedule::command('staygo:blog-weekly-digest')->weeklyOn(1, '09:00');       // Thứ Hai 09:00 — blog weekly digest
