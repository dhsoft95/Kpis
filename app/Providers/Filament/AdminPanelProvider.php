<?php

namespace App\Providers\Filament;

use App\Filament\Pages\customer;
use App\Filament\Pages\financial;
use App\Filament\Pages\oparations;
use App\Filament\Pages\service;
use App\Filament\Widgets\ActiveChart;
use App\Filament\Widgets\ActiveChartAp;
use App\Filament\Widgets\ChurnChart;
use App\Filament\Widgets\CustomerMetric;
use App\Filament\Widgets\CustStatsOverview;
use App\Filament\Widgets\RegisterdUsersChart;
use App\Filament\Widgets\RegisteredChart;
use App\Filament\Widgets\TestOverview;
use App\Filament\Widgets\UserPerformance;
use App\Filament\Widgets\userStatsOverview;
use App\Livewire\MapOverview;
use Awcodes\FilamentStickyHeader\StickyHeaderPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\ThemeMode;
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
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()  ->collapsibleNavigationGroups(true)->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('asset/images/logo.svg'))->brandLogoHeight('2rem')
            ->favicon(asset('asset/images/favicon.svg')) ->topbar(true)
            ->colors([
                'primary' => '#fcbb29',
                'secondary' => '#fcbb29',
            ]) ->defaultThemeMode(ThemeMode::Dark)
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
//            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
//            ->pages([customer::class,financial::class,oparations::class,service::class])
            ->pages([
                Pages\Dashboard::class,customer::class,service::class,oparations::class,financial::class,
            ])
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                userStatsOverview::class,
                RegisterdUsersChart::class,
//              RegisteredChart::class,
                ActiveChart::class,
                ChurnChart::class,


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
            ]) ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                FilamentApexChartsPlugin::make(),
                StickyHeaderPlugin::make()
                    ->floating()
                    ->colored()
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
