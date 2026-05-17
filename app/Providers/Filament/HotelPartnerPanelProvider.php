<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HotelPartnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hotel-partner')
            ->path('partner')
            ->login()
            ->authGuard('hotel_partner')
            ->brandName('StayGo Partner')
            ->brandLogo(null)
            ->colors(['primary' => Color::Blue])
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(
                in: app_path('Filament/HotelPartner/Resources'),
                for: 'App\\Filament\\HotelPartner\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/HotelPartner/Pages'),
                for: 'App\\Filament\\HotelPartner\\Pages'
            )
            ->pages([])
            ->discoverWidgets(
                in: app_path('Filament/HotelPartner/Widgets'),
                for: 'App\\Filament\\HotelPartner\\Widgets'
            )
            ->navigationGroups([
                'Khách sạn',
                'Đặt phòng & Doanh thu',
                'Đánh giá',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
