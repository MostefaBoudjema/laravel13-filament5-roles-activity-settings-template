<?php

namespace App\Providers;

use App\Http\Middleware\EnsureFilamentUserHasRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Resources\Settings\SettingResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Pages\FinancialReports;
use Illuminate\Support\Facades\Storage;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $logo = null;
        $favicon = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $logo = \App\Models\Setting::where('key', 'logo')->value('value');
                $favicon = \App\Models\Setting::where('key', 'favicon')->value('value');
            }
        } catch (\Throwable $e) {
        }
        return $panel
            ->default()
            ->id('admin')
            ->path('/admin')

            ->colors([
                'primary' => Color::{config('filament.color')},
                'secondary' => Color::Indigo,
                'gray' => [
                    50 => '248, 250, 252',   // Light theme main background
                    100 => '241, 245, 249',  // Light theme secondary background/cards
                    200 => '226, 232, 240',
                    300 => '203, 213, 225',
                    400 => '148, 163, 184',
                    500 => '100, 116, 139',
                    600 => '71, 85, 105',
                    700 => '51, 65, 85',
                    800 => '30, 41, 59',
                    900 => '15, 23, 42',     // Dark theme secondary background/cards
                    950 => '2, 6, 23',       // Dark theme main background
                ],
            ])
            ->login()
            ->brandLogo($logo ? url('/storage/' . $logo) : asset('logo.png'))
            ->darkModeBrandLogo($logo ? url('/storage/' . $logo) : asset('logo.png'))
            ->favicon($favicon ? url('/storage/' . $favicon) : asset('favicon.ico'))
           
            ->brandLogoHeight(fn () => request()->routeIs('filament.admin.auth.login') ? '4rem' : '2rem') 
            ->maxContentWidth(Width::Full)
            ->discoverResources(app_path('Filament/Resources'), app()->getNamespace() . 'Filament\\Resources')
            ->discoverWidgets(app_path('Filament/Widgets'), app()->getNamespace() . 'Filament\\Widgets')
            ->discoverPages(app_path('Filament/Pages'), app()->getNamespace() . 'Filament\\Pages')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->navigationGroups([
                NavigationGroup::make()->label(fn () => __('Security')),
                NavigationGroup::make()->label(fn () => __('Settings')),
            ])
            
            // ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            //     return $builder->items([
            //         NavigationItem::make('Dashboard')
            //             ->icon('heroicon-s-home')
            //             ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.Crm.FinancialReports'))
            //             ->url(fn (): string => FinancialReports::getUrl()),
            //         ...UserResource::getNavigationItems(),
            //         ...SettingResource::getNavigationItems(),
            //     ]);
            // })
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
            ])
            // In your PanelProvider.php
            ->renderHook(
                'panels::body.end',
                fn () => new \Illuminate\Support\HtmlString(
                    '<style>' . file_get_contents(resource_path('css/filament-sticky-fix.css')) . '</style>'
                )
            )

            ;


}
}
