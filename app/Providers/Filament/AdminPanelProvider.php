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
use App\Livewire\CustomerMetric\ActiveAndInactive;
use App\Livewire\CustomerMetric\ChurnUsers;
use App\Livewire\Dawasa;
use App\Livewire\GoogleAnalytics\GeoChartWidget;
use App\Livewire\GoogleAnalytics\NumberOfDownloads;
use App\Livewire\MapOverview;
use App\Livewire\UserGanders;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use SolutionForest\FilamentAccessManagement\FilamentAccessManagementPanel;
use Vormkracht10\TwoFactorAuth\TwoFactorAuthPlugin;


class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->font('Inter', provider: GoogleFontProvider::class)->spa()
            ->default()
            ->id('admin')    ->breadcrumbs(false)
            ->sidebarWidth('17rem')
//            ->collapsedSidebarWidth('9rem')
            ->path('admin')->topbar(true)
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile'),
                'logout' => MenuItem::make()->label('Log out'),
            ])
            ->login()  ->collapsibleNavigationGroups(true)  ->font('Sarabun')
            ->brandLogo(asset('asset/images/logo.svg'))->brandLogoHeight('2rem')
            ->favicon(asset('asset/images/favicon.svg')) ->topbar(true)
//            ->plugin(FilamentSpatieLaravelHealthPlugin::make())
            ->colors([

            ])->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                Pages\Dashboard::class,customer::class,service::class,oparations::class,financial::class,
            ])
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([

                userStatsOverview::class,
                NumberOfDownloads::class,
                GeoChartWidget::class,
                RegisterdUsersChart::class,
                ActiveAndInactive::class,
                ChurnUsers::class,
      //          \App\Filament\Widgets\UserDemographic::class

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
                FilamentApexChartsPlugin::make(),
//                TwoFactorAuthPlugin::make(),
//                FilamentAccessManagementPanel::make(),

            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
