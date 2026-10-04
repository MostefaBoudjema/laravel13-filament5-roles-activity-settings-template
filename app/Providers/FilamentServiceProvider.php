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

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/admin')
            ->colors([
                'primary' => Color::Amber,                
                'secondary' => Color::Indigo,
            ])
            ->login()
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight(fn () => request()->routeIs('filament.admin.auth.login') ? '4rem' : '2rem') 
            ->maxContentWidth(Width::Full)
            ->discoverResources(app_path('Filament/Resources'), app()->getNamespace() . 'Filament\\Resources')
            ->discoverWidgets(app_path('Filament/Widgets'), app()->getNamespace() . 'Filament\\Widgets')
            ->discoverPages(app_path('Filament/Pages'), app()->getNamespace() . 'Filament\\Pages')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->navigationGroups([
                NavigationGroup::make()->label(fn () => __('School Management')),
                NavigationGroup::make()->label(fn () => __('Financial')),
                NavigationGroup::make()->label(fn () => __('CRM')),
                NavigationGroup::make()->label(fn () => __('Settings')),
                NavigationGroup::make()->label(fn () => __('Security')),
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
            // ->renderHook(
            //     \Filament\View\PanelsRenderHook::SIDEBAR_LOGO_AFTER,
            //     fn (): string => \Illuminate\Support\Facades\Blade::render('
            //         @if($currentAcademicYear = \App\Models\AcademicYear::current()->first())
            //             <span class="ml-4 text-[36pt] font-bold text-primary-600 dark:text-primary-400">
            //                 {{ $currentAcademicYear->name }}
            //             </span>
            //         @endif
            //     ')
            // )
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_LOGO_AFTER,
                fn (): string => \Illuminate\Support\Facades\Blade::render('
                    @if($currentAcademicYear = \App\Models\AcademicYear::current()->first())
                        <span class="ml-4 font-bold text-primary-600 dark:text-primary-400" style="font-size: 16pt;">
                            {{ $currentAcademicYear->name }}
                        </span>
                    @endif
                ')
            )
            ;


}
}
