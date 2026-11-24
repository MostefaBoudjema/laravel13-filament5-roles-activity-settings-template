<?php

namespace App\Providers;

use App\Http\Middleware\EnsureFilamentUserHasRole;
use App\Models\Setting;
use Closure;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa(hasPrefetching: true)
            ->spaUrlExceptions(['files*'])
            ->unsavedChangesAlerts()
            ->strictAuthorization()
            ->databaseTransactions()
            ->profile(isSimple: false)
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchDebounce('750ms')
            ->font('Cairo')
            ->brandName(config('app.name'))
            ->brandLogo($this->settingUrl('logo', 'logo.png'))
            ->darkModeBrandLogo($this->settingUrl('logo', 'logo.png'))
            ->favicon($this->settingUrl('favicon', 'favicon.ico'))
            ->brandLogoHeight(fn () => request()->routeIs('filament.admin.auth.login') ? '4rem' : '2rem')
            ->colors([
                'primary' => Color::{config('filament.color')},
                'secondary' => Color::Indigo,
                'gray' => Color::Slate,
     
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(app_path('Filament/Resources'), app()->getNamespace() . 'Filament\\Resources')
            ->discoverWidgets(app_path('Filament/Widgets'), app()->getNamespace() . 'Filament\\Widgets')
            ->discoverPages(app_path('Filament/Pages'), app()->getNamespace() . 'Filament\\Pages')
            ->navigationGroups([
                NavigationGroup::make()->label(fn () => __('Security')),
                NavigationGroup::make()->label(fn () => __('Settings')),
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
                EnsureFilamentUserHasRole::class,
            ]);
    }

    private function settingUrl(string $key, string $fallback): Closure
    {
        return function () use ($key, $fallback): string {
            $path = cache()->rememberForever(
                "setting.{$key}",
                fn () => Setting::where('key', $key)->value('value'),
            );

            return $path ? url("/storage/{$path}") : asset($fallback);
        };
    }
}