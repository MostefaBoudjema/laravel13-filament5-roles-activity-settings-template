<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationActivity;
use App\Models\User;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
        
        // ── Authentication activity logging ─────────────────────────────────
        $listener = app(LogAuthenticationActivity::class);
        Event::listen(Login::class,  [$listener, 'handleLogin']);
        Event::listen(Logout::class, [$listener, 'handleLogout']);
        Event::listen(Failed::class, [$listener, 'handleFailed']);
        // ───────────────────────────────────────────────────────────────────

        // Super-admin bypasses all permission checks
        // Gate::before(function (User $user, string $ability) {
        //     return $user->hasRole('super-admin') ? true : null;
        // });

        // Register policies for Spatie models (not auto-discovered)
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        \BezhanSalleh\LanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\LanguageSwitch\LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr'])
                ->renderHook(\Filament\View\PanelsRenderHook::USER_MENU_PROFILE_AFTER);
        });

        \Filament\Tables\Table::configureUsing(function (\Filament\Tables\Table $table): void {
            $table
                ->defaultDateDisplayFormat('d-m-Y')
                ->defaultDateTimeDisplayFormat('d-m-Y H:i')
                ->paginationPageOptions([10, 20, 30, 50, 100, 'all'])
                ->defaultPaginationPageOption(20);  
        });

        \Filament\Forms\Components\DatePicker::configureUsing(function (\Filament\Forms\Components\DatePicker $datePicker): void {
            $datePicker->displayFormat('d-m-Y')->native(false);
        });

        \Filament\Forms\Components\DateTimePicker::configureUsing(function (\Filament\Forms\Components\DateTimePicker $dateTimePicker): void {
            $dateTimePicker->displayFormat('d-m-Y H:i')->native(false);
        });

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            // Log it to storage/logs/laravel.log
            Log::warning("Missing translation key: '{$key}' for locale: '{$locale}'");
            
            // Alternatively, return a specific marker so it stands out in the UI
            return "⚠️ {$key}";
        });

    }
}
