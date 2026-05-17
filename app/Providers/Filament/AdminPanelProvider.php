<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('admin')
            ->brandName('StayGo Admin')
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->colors([
                'primary' => Color::Rose,
            ])
            ->widgets([])
            ->assets([
                Css::make('admin-css', asset('assets/css/admin.css')),
                Css::make('login-admin-css', asset('assets/css/login_admin.css')),
                Js::make('admin-js', asset('assets/js/admin.js')),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <style>
                /* === Thu nhỏ sidebar Filament === */
                /* Sidebar nav: giảm padding và gap */
                .fi-sidebar-nav {
                    padding-top: 8px !important;
                    padding-bottom: 8px !important;
                    padding-left: 10px !important;
                    padding-right: 10px !important;
                    row-gap: 4px !important;
                    gap: 4px !important;
                }
                /* Khoảng cách giữa các nhóm menu */
                .fi-sidebar-nav-groups {
                    row-gap: 6px !important;
                    gap: 6px !important;
                }
                /* Từng nhóm */
                .fi-sidebar-group {
                    row-gap: 2px !important;
                    gap: 2px !important;
                }
                /* Item trong nhóm */
                .fi-sidebar-group-items {
                    row-gap: 1px !important;
                    gap: 1px !important;
                }
                /* Nút nhóm (label group) */
                .fi-sidebar-group-button {
                    padding-top: 4px !important;
                    padding-bottom: 4px !important;
                }
                /* Nút item */
                .fi-sidebar-item-button {
                    padding-top: 5px !important;
                    padding-bottom: 5px !important;
                    padding-left: 8px !important;
                    padding-right: 8px !important;
                }
                /* === FIX HEADER KHÔNG BỊ CUỐN KHI SCROLL === */

                /* Sidebar header: xóa ring/shadow, dùng border sạch */
                .fi-sidebar-header {
                    height: 4rem !important;
                    min-height: 4rem !important;
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                    --tw-ring-shadow: 0 0 #0000 !important;
                    --tw-ring-offset-shadow: 0 0 #0000 !important;
                    box-shadow: none !important;
                    border-bottom: 1px solid #e5e7eb !important;
                }

                /* Sidebar cố định khi scroll */
                .fi-sidebar {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    height: 100vh !important;
                    z-index: 30 !important;
                    width: var(--sidebar-width, 160px) !important;
                }

                /* Topbar: CỐ ĐỊNH (không sticky) để không bị cuốn */
                .fi-topbar {
                    position: fixed !important;
                    top: 0 !important;
                    left: var(--sidebar-width, 160px) !important;
                    right: 0 !important;
                    z-index: 29 !important;
                }

                /* Topbar nav: xóa ring/shadow, đồng bộ border */
                .fi-topbar > nav {
                    --tw-ring-shadow: 0 0 #0000 !important;
                    --tw-ring-offset-shadow: 0 0 #0000 !important;
                    box-shadow: none !important;
                    border-bottom: 1px solid #e5e7eb !important;
                    height: 4rem !important;
                    background: white !important;
                }

                /* Main content: đẩy xuống 64px để không bị che bởi topbar fixed */
                .fi-main-ctn {
                    padding-top: 4rem !important;
                    margin-left: var(--sidebar-width, 160px) !important;
                }

                html { scrollbar-gutter: stable; }
                </style>
                HTML)
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
