<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DailyScheduleChart;
use App\Filament\Widgets\UserRoleChart;
// Hapus atau komentari 'StatsOverview' yang lama
// use App\Filament\Widgets\StatsOverview; 
use App\Filament\Widgets\UsersChart;
use App\Filament\Widgets\WorkoutCategoryChart;
use App\Http\Middleware\CheckAdminRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
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
use App\Filament\Widgets\WorkoutLogChart;
use App\Filament\Widgets\CustomStatsOverview; // <-- TAMBAHKAN INI

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('Workout Kos')
            ->brandLogo(asset('images/logo3.png'))
            ->favicon(asset('images/logo3.png'))
            ->brandLogoHeight('110px')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                CustomStatsOverview::class, // <-- GANTI DARI StatsOverview::class MENJADI INI
                UsersChart::class,
                WorkoutCategoryChart::class,
                WorkoutLogChart::class,
                DailyScheduleChart::class,
                UserRoleChart::class,
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
                CheckAdminRole::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Manajemen Konten'),
                NavigationGroup::make()
                    ->label('Aktivitas Pengguna'),
                NavigationGroup::make()
                    ->label('Manajemen Penggunaa'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->darkMode(true);
    }
}
