<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Review;
use App\Observers\BlogPostObserver;
use App\Observers\BookingObserver;
use App\Observers\HotelObserver;
use App\Observers\LocationObserver;
use App\Observers\PaymentObserver;
use App\Observers\ReviewObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            // Production (Railway): luôn dùng APP_URL, tránh bị X-Forwarded-Host railway ghi đè
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        } elseif (!$this->app->runningInConsole()) {
            // Local / ngrok: đọc X-Forwarded-Host để hỗ trợ tunnel dynamic
            $req   = request();
            $fHost = $req->header('X-Forwarded-Host');
            if ($fHost) {
                $proto = $req->header('X-Forwarded-Proto', 'https');
                $root  = $proto . '://' . explode(',', $fHost)[0];
            } else {
                $root = $req->getSchemeAndHttpHost();
            }
            URL::forceRootUrl($root);
            if (str_starts_with($root, 'https://')) {
                URL::forceScheme('https');
            }
        }

        Paginator::defaultView('vendor.pagination.simple-staygo');
        Paginator::defaultSimpleView('vendor.pagination.simple-staygo');

        // Observers — tự động xóa cache khi dữ liệu thay đổi
        Hotel::observe(HotelObserver::class);
        Booking::observe(BookingObserver::class);
        BlogPost::observe(BlogPostObserver::class);
        Location::observe(LocationObserver::class);
        Payment::observe(PaymentObserver::class);
        Review::observe(ReviewObserver::class);
    }
}
