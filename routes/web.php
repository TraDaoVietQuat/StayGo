<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportReplyController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\Admin\AdminAiChatController;
use App\Http\Controllers\Admin\EmailAiController;
use App\Http\Controllers\Partner\PartnerAiController;
use App\Http\Controllers\PartnerRegistrationController;
use Illuminate\Support\Facades\Route;

// ==================== PUBLIC ROUTES ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/luxury', fn() => view('luxury-landing'))->name('luxury.preview');

// Hotels
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/hotels/{hotel}/availability', [HotelController::class, 'availability'])->name('hotels.availability');

// Deals
Route::get('/uu-dai', [DealsController::class, 'index'])->name('deals.index');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');

// Support / Contact
Route::get('/lien-he', [SupportController::class, 'create'])->name('support.create');
Route::post('/lien-he', [SupportController::class, 'store'])->name('support.store')->middleware('throttle:5,1');

// Chatbot API
Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot');
Route::post('/chatbot/escalate', [ChatbotController::class, 'escalate'])->name('chatbot.escalate');

// Promo QR
Route::get('/uu-dai/moi', [PromoController::class, 'applyNewUser'])->name('promo.new_user');

// Promo code validation (AJAX)
Route::post('/promo/validate',  [PromoController::class, 'validate'])->name('promo.validate');
Route::get('/promo/available',  [PromoController::class, 'available'])->name('promo.available');

// ==================== AUTH ROUTES (guests only) ====================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/dang-nhap', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [LoginController::class, 'login'])->middleware('throttle:10,5');

    // Register
    Route::get('/dang-ky', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [RegisterController::class, 'register'])->middleware('throttle:5,5');

    // Forgot Password
    Route::get('/quen-mat-khau', [ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/quen-mat-khau', [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp')->middleware('throttle:5,15');
    Route::get('/xac-thuc-otp', [ForgotPasswordController::class, 'showVerifyOtp'])->name('password.verify-otp');
    Route::post('/xac-thuc-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp.post')->middleware('throttle:5,15');
    Route::get('/dat-lai-mat-khau', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset-form');
    Route::post('/dat-lai-mat-khau', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset')->middleware('throttle:5,15');

    // OTP Login
    Route::get('/dang-nhap-otp', [OtpLoginController::class, 'showForm'])->name('login.otp');
    Route::post('/dang-nhap-otp', [OtpLoginController::class, 'send'])->name('login.otp.send')->middleware('throttle:5,5');
    Route::get('/dang-nhap-otp/xac-thuc', [OtpLoginController::class, 'showVerify'])->name('login.otp.verify');
    Route::post('/dang-nhap-otp/xac-thuc', [OtpLoginController::class, 'verify'])->name('login.otp.verify.post')->middleware('throttle:10,5');

});

// Social Auth — no middleware (handles both guest login AND logged-in linking)
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Logout
Route::post('/dang-xuat', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Email verification (signed URL — no auth required)
Route::get('/xac-minh-email/{id}', [RegisterController::class, 'verify'])->name('email.verify');
Route::post('/xac-minh-email/gui-lai', [RegisterController::class, 'resend'])->name('email.resend')->middleware('auth');

// Booking & Payment — cho phép cả khách vãng lai
Route::get('/dat-phong/{room}',             [BookingController::class, 'create'])->name('booking.create');
Route::post('/dat-phong',                   [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:5,1');
Route::get('/thanh-toan/{booking}',         [PaymentController::class, 'show'])->name('payment.show');
Route::post('/thanh-toan/{booking}',        [PaymentController::class, 'process'])->name('payment.process');
Route::get('/api/payment/{booking}/status', [PaymentController::class, 'status'])->name('payment.status');

// ==================== AUTHENTICATED USER ROUTES ====================
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/tai-khoan', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/tai-khoan/mat-khau', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Booking (chỉ user đã đăng nhập)
    Route::get('/dat-phong-cua-toi', [BookingController::class, 'myBookings'])->name('booking.my');
    Route::post('/dat-phong/{booking}/huy', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/dat-phong/{booking}/hoan-tien', [BookingController::class, 'requestRefund'])->name('booking.refund');

    // Reviews
    Route::post('/danh-gia', [ReviewController::class, 'store'])->name('review.store')->middleware('throttle:3,1');
    Route::put('/danh-gia/{review}', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/danh-gia/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');

    // Favorites
    Route::get('/yeu-thich', [FavoriteController::class, 'index'])->name('favorite.index');
    Route::post('/yeu-thich/{hotel}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // Notifications
    Route::post('/thong-bao/doc-het', function () {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->unreadNotifications()->markAsRead();
        return back();
    })->name('notifications.read-all');

    // Polling endpoint: trả về unread count để JS cập nhật badge
    Route::get('/api/notifications/unread-count', function () {
        return response()->json([
            'count' => \Illuminate\Support\Facades\Auth::user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.unread-count');

    // Invoice download
    Route::get('/dat-phong/{booking}/hoa-don', [BookingController::class, 'invoice'])->name('booking.invoice');

    // Avatar upload
    Route::post('/tai-khoan/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Support ticket thread (user side)
    Route::get('/ho-tro/{supportRequest}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/ho-tro/{supportRequest}/tra-loi', [SupportReplyController::class, 'store'])->name('support.reply');
});

// ==================== PAYMENT WEBHOOKS (no CSRF, no auth) ====================
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    // VNPay
    Route::post('/webhook/vnpay/ipn',       [PaymentWebhookController::class, 'vnpayIpn'])->name('webhook.vnpay.ipn');
    Route::get('/webhook/vnpay/return',     [PaymentWebhookController::class, 'vnpayReturn'])->name('webhook.vnpay.return');
    // MoMo
    Route::post('/webhook/momo/ipn',        [PaymentWebhookController::class, 'momoIpn'])->name('webhook.momo.ipn');
    Route::get('/webhook/momo/return',      [PaymentWebhookController::class, 'momoReturn'])->name('webhook.momo.return');
    // SePay (bank transfer auto-detect)
    Route::post('/webhook/sepay',           [PaymentWebhookController::class, 'sepayWebhook'])->name('webhook.sepay');
});

// ==================== DISPUTE / COMPLAINT ====================
Route::get('/khieu-nai', [DisputeController::class, 'create'])->name('dispute.create');
Route::post('/khieu-nai', [DisputeController::class, 'store'])->name('dispute.store')->middleware('throttle:3,10');
Route::get('/khieu-nai/thanh-cong', [DisputeController::class, 'success'])->name('dispute.success');

// ==================== SITEMAP ====================
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ==================== HOTEL PARTNER REGISTRATION ====================
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('/dang-ky', [PartnerRegistrationController::class, 'showForm'])->name('register');
    Route::post('/dang-ky', [PartnerRegistrationController::class, 'submit'])->name('register.submit')->middleware('throttle:5,10');
    Route::get('/dang-ky/thanh-cong', [PartnerRegistrationController::class, 'success'])->name('register.success');
});

// ==================== ADMIN AI ASSISTANT ====================
Route::middleware(['auth:admin'])->prefix('admin-api')->group(function () {
    Route::post('/ai/chat', [AdminAiChatController::class, 'chat'])->name('admin.ai.chat');
    Route::get('/ai/kpi',  [AdminAiChatController::class, 'kpi'])->name('admin.ai.kpi');
    // E-11/E-12/E-13 — Email AI Studio
    Route::post('/email-ai/generate', [EmailAiController::class, 'generate'])->name('admin.email-ai.generate');
    Route::post('/email-ai/score',    [EmailAiController::class, 'score'])->name('admin.email-ai.score');
    Route::post('/email-ai/analyze',  [EmailAiController::class, 'analyze'])->name('admin.email-ai.analyze');
});

// ==================== PARTNER AI ASSISTANT ====================
Route::middleware(['auth:hotel_partner'])->prefix('partner-api')->name('partner.')->group(function () {
    Route::post('/ai/chat', [PartnerAiController::class, 'chat'])->name('ai.chat');
    Route::get('/ai/kpi',  [PartnerAiController::class, 'kpi'])->name('ai.kpi');
});
