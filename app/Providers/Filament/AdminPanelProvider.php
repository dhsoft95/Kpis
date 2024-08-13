<?php

namespace App\Providers\Filament;

use App\Filament\Pages\customer;
use App\Filament\Pages\financial;
use App\Filament\Pages\oparations;
use App\Filament\Pages\service;
use App\Filament\Widgets\ActiveChartAp;
use App\Filament\Widgets\CustomerMetric;
use App\Filament\Widgets\CustStatsOverview;
use App\Filament\Widgets\TestOverview;
use App\Filament\Widgets\UserWidget\ActiveChart;
use App\Filament\Widgets\UserWidget\ChurnChart;
use App\Filament\Widgets\UserWidget\RegisterdUsersChart;
use App\Filament\Widgets\UserWidget\userStatsOverview;
use App\Livewire\devicDown;
use App\Livewire\MapOverview;
use App\Livewire\UserGanders;
use App\Livewire\WalletOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults;


class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->font('Inter', provider: GoogleFontProvider::class)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()  ->collapsibleNavigationGroups(true)  ->font('Sarabun')
            ->brandLogo(asset('asset/images/logo.svg'))->brandLogoHeight('2rem')
            ->favicon(asset('asset/images/favicon.svg')) ->topbar(true)
//            ->plugin(FilamentSpatieLaravelHealthPlugin::make())
            ->colors([

            ])->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
//            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
//            ->pages([customer::class,financial::class,oparations::class,service::class])
            ->pages([
                Pages\Dashboard::class,customer::class,service::class,oparations::class,financial::class,
            ])
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                userStatsOverview::class,
                devicDown::class,
                RegisterdUsersChart::class,
                ActiveChart::class,
                ChurnChart::class,
//                \App\Filament\Widgets\UserDemographic::class


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
//                FilamentSpatieLaravelHealthPlugin::make()
//                    ->usingPage(HealthCheckResults::class)

            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
