<?php

namespace App\Providers\Filament;

use App\Filament\Pages\customer;
use App\Filament\Pages\service;
use App\Filament\Support\Pages\usermanagement;
use App\Filament\Support\Widgets\UserInfo;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SupportPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('support')
            ->path('support')
            ->colors([
                'primary' => Color::Amber,
            ])->login()
            ->discoverResources(in: app_path('Filament/Support/Resources'), for: 'App\\Filament\\Support\\Resources')
            ->discoverPages(in: app_path('Filament/Support/Pages'), for: 'App\\Filament\\Support\\Pages')
            ->pages([
                UserManagement::class
            ])
            ->discoverWidgets(in: app_path('Filament/Support/Widgets'), for: 'App\\Filament\\Support\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
                  userinfo::class,
//                Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
