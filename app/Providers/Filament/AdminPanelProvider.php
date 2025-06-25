<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverview;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration() // <-- 1. Tambahkan halaman registrasi jika perlu
            ->passwordReset() // <-- 2. Tambahkan fitur reset password
            ->profile()       // <-- 3. Tambahkan halaman profil pengguna
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Workout Kos') // <-- 4. Branding: Nama Panel
            ->brandLogo(asset('images/logo.svg')) // <-- 4. Branding: Logo (opsional)
            ->favicon(asset('images/favicon.png')) // <-- 4. Branding: Favicon (opsional)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Gabungkan semua widget dalam satu array
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                StatsOverview::class, // Widget kustom Anda
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
            ])
            ->navigation() // <-- 5. Aktifkan kustomisasi navigasi
            ->sidebarCollapsibleOnDesktop() // <-- 6. Sidebar bisa di-collapse di desktop
            ->maxContentWidth('full') // <-- 7. Atur lebar konten maksimal
            ->darkMode(true); // <-- 8. Aktifkan toggle dark mode
    }
}